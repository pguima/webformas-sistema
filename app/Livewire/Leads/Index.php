<?php

namespace App\Livewire\Leads;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Plan;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Index extends Component
{
    public const STAGES = [
        'Novo',
        'Em Contato',
        'Reunião Marcada',
        'Proposta',
        'Ganho',
        'Perdido',
    ];

    public string $viewMode = 'kanban';

    public ?int $leadId = null;

    public ?string $name = null;

    public ?string $whatsapp = null;

    public ?string $empresa = null;

    public ?string $cnpj = null;

    public ?int $plan_id = null;

    /** @var array<int, string> */
    public array $service_ids = [];

    public $value_base = null;

    public ?string $discount_type = null;

    public $discount_value = null;

    public $value_final = null;

    public ?int $responsible_user_id = null;

    public ?string $origin = null;

    public ?string $campaign = null;

    public string $stage = 'Novo';

    public ?int $leadToDeleteId = null;

    public string $deleteConfirmation = '';

    private function isLockedStage(?string $stage): bool
    {
        return in_array($stage, ['Ganho', 'Perdido'], true);
    }

    private function buildColumns(): array
    {
        $leads = Lead::query()
            ->with(['responsibleUser:id,name'])
            ->orderBy('stage')
            ->orderBy('position')
            ->get();

        return collect(self::STAGES)
            ->map(function ($stage) use ($leads) {
                $items = $leads->where('stage', $stage)->values();

                return [
                    'stage' => $stage,
                    'count' => $items->count(),
                    'leads' => $items->map(function (Lead $lead) {
                        return [
                            'id' => (int) $lead->id,
                            'name' => (string) $lead->name,
                            'stage' => (string) $lead->stage,
                            'is_locked' => $this->isLockedStage($lead->stage),
                            'whatsapp' => $lead->whatsapp,
                            'empresa' => $lead->empresa,
                            'cnpj' => $lead->cnpj,
                            'plan' => $lead->plan,
                            'services' => $lead->services,
                            'value' => $lead->value,
                            'responsible' => $lead->responsibleUser?->name,
                            'origin' => $lead->origin,
                            'campaign' => $lead->campaign,
                            'updated_at' => optional($lead->updated_at)?->toISOString(),
                        ];
                    })->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'empresa' => ['nullable', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:18'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'service_ids' => ['array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'value_base' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', Rule::in(['value', 'percent'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'value_final' => ['nullable', 'numeric', 'min:0'],
            'responsible_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'origin' => ['nullable', 'string', 'max:255'],
            'campaign' => ['nullable', 'string', 'max:255'],
            'stage' => ['required', Rule::in(self::STAGES)],
        ];
    }

    public function updatedPlanId(): void
    {
        $this->syncServicesFromPlan();
        $this->recalculatePricing();
    }

    public function updatedServiceIds(): void
    {
        if ($this->plan_id) {
            $this->syncServicesFromPlan();
        }

        $this->recalculatePricing();
    }

    public function updatedDiscountType(): void
    {
        $this->recalculatePricing();
    }

    public function updatedDiscountValue(): void
    {
        $this->recalculatePricing();
    }

    private function syncServicesFromPlan(): void
    {
        if (!$this->plan_id) {
            return;
        }

        $plan = Plan::query()->with('services:id')->find($this->plan_id);
        if (!$plan) {
            return;
        }

        $this->service_ids = $plan->services
            ->pluck('id')
            ->map(fn ($v) => (string) $v)
            ->values()
            ->all();
    }

    private function recalculatePricing(): void
    {
        $base = 0.0;

        if ($this->plan_id) {
            $planPrice = (float) (Plan::query()->whereKey($this->plan_id)->value('price') ?? 0);
            $base = $planPrice;
        } else {
            $ids = array_values(array_filter(array_map('intval', $this->service_ids)));
            if (!empty($ids)) {
                $base = (float) (Service::query()->whereIn('id', $ids)->sum('price') ?? 0);
            }
        }

        $this->value_base = round($base, 2);

        $discountType = $this->discount_type;
        $discountValue = is_numeric($this->discount_value) ? (float) $this->discount_value : 0.0;

        $discountAmount = 0.0;
        if ($discountType === 'percent') {
            $discountAmount = $base * ($discountValue / 100.0);
        } elseif ($discountType === 'value') {
            $discountAmount = $discountValue;
        }

        $final = max(0.0, $base - $discountAmount);
        $this->value_final = round($final, 2);
    }

    public function create(): void
    {
        $this->reset([
            'leadId',
            'name',
            'whatsapp',
            'empresa',
            'cnpj',
            'plan_id',
            'service_ids',
            'value_base',
            'discount_type',
            'discount_value',
            'value_final',
            'responsible_user_id',
            'origin',
            'campaign',
        ]);
        $this->stage = 'Novo';
        $this->service_ids = [];
        $this->discount_type = 'value';
        $this->discount_value = 0;
        $this->recalculatePricing();
    }

    public function edit(int $id): void
    {
        $lead = Lead::findOrFail($id);

        if ($this->isLockedStage($lead->stage)) {
            $this->dispatch('notify', message: __('app.leads.messages.locked'), variant: 'danger', title: __('app.leads.messages.error_title'));
            return;
        }

        $this->leadId = $lead->id;
        $this->name = $lead->name;
        $this->whatsapp = $lead->whatsapp;
        $this->empresa = $lead->empresa;
        $this->cnpj = $lead->cnpj;
        $this->plan_id = $lead->plan_id;
        $this->service_ids = array_map('strval', $lead->service_ids ?? []);
        $this->value_base = $lead->value_base;
        $this->discount_type = $lead->discount_type;
        $this->discount_value = $lead->discount_value;
        $this->value_final = $lead->value_final;
        $this->responsible_user_id = $lead->responsible_user_id;
        $this->origin = $lead->origin;
        $this->campaign = $lead->campaign;
        $this->stage = $lead->stage;

        if ($this->plan_id) {
            $this->syncServicesFromPlan();
        }

        $this->recalculatePricing();
    }

    public function save(): void
    {
        $this->recalculatePricing();
        $data = $this->validate();

        $planName = null;
        if (!empty($data['plan_id'])) {
            $planName = Plan::query()->whereKey($data['plan_id'])->value('name');
        }

        $serviceNames = [];
        $ids = array_values(array_filter(array_map('intval', $data['service_ids'] ?? [])));
        if (!empty($ids)) {
            $serviceNames = Service::query()->whereIn('id', $ids)->orderBy('name')->pluck('name')->all();
        }

        $dataToPersist = array_merge($data, [
            'plan' => $planName,
            'services' => !empty($serviceNames) ? implode(', ', $serviceNames) : null,
            'value' => $data['value_final'] ?? null,
        ]);

        if ($this->leadId) {
            $lead = Lead::findOrFail($this->leadId);

            if ($this->isLockedStage($lead->stage)) {
                $this->dispatch('notify', message: __('app.leads.messages.locked'), variant: 'danger', title: __('app.leads.messages.error_title'));
                return;
            }

            $lead->update($dataToPersist);

            $this->dispatch('notify', message: __('app.leads.messages.updated_success'), variant: 'success', title: __('app.leads.messages.success_title'));
        } else {
            $position = (int) (Lead::query()->where('stage', $data['stage'])->max('position') ?? 0);

            $lead = Lead::create(array_merge($dataToPersist, [
                'position' => $position + 1,
            ]));

            $this->dispatch('notify', message: __('app.leads.messages.created_success'), variant: 'success', title: __('app.leads.messages.success_title'));
        }

        $this->dispatch('leads-updated', columns: $this->buildColumns());

        $this->dispatch('close-lead-offcanvas');
        $this->reset([
            'leadId',
            'name',
            'whatsapp',
            'empresa',
            'cnpj',
            'plan_id',
            'service_ids',
            'value_base',
            'discount_type',
            'discount_value',
            'value_final',
            'responsible_user_id',
            'origin',
            'campaign',
        ]);
        $this->stage = 'Novo';
        $this->service_ids = [];
    }

    public function confirmDelete(int $id): void
    {
        $this->leadToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->resetErrorBag('deleteConfirmation');
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.leads.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $lead = Lead::find($this->leadToDeleteId);
        if (! $lead) {
            $this->dispatch('notify', message: __('app.leads.messages.error_not_found'), variant: 'danger', title: __('app.leads.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['leadToDeleteId', 'deleteConfirmation']);
            return;
        }

        if ($this->isLockedStage($lead->stage)) {
            $this->dispatch('notify', message: __('app.leads.messages.locked'), variant: 'danger', title: __('app.leads.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['leadToDeleteId', 'deleteConfirmation']);
            return;
        }

        $lead->delete();

        $this->dispatch('notify', message: __('app.leads.messages.deleted_success'), variant: 'success', title: __('app.leads.messages.success_title'));
        $this->dispatch('close-delete-modal');
        $this->reset(['leadToDeleteId', 'deleteConfirmation']);

        $this->dispatch('leads-updated', columns: $this->buildColumns());
    }

    public function moveLead(int $leadId, string $toStage, int $toIndex): void
    {
        if (! in_array($toStage, self::STAGES, true)) {
            return;
        }

        $lead = Lead::find($leadId);
        if (! $lead) {
            return;
        }

        if ($this->isLockedStage($lead->stage) && $lead->stage !== $toStage) {
            $this->dispatch('notify', message: __('app.leads.messages.locked'), variant: 'danger', title: __('app.leads.messages.error_title'));
            return;
        }

        DB::transaction(function () use ($lead, $toStage, $toIndex) {
            $lead->update([
                'stage' => $toStage,
                'position' => max(0, $toIndex),
            ]);

            if ($toStage === 'Ganho') {
                $clientName = (string) ($lead->empresa ?: $lead->name);
                $cnpj = $lead->cnpj ? (string) $lead->cnpj : null;

                $clientPayload = [
                    'name' => $clientName,
                    'plan_id' => $lead->plan_id,
                    'service_ids' => is_array($lead->service_ids) ? $lead->service_ids : [],
                    'contract_value' => $lead->value_final,
                    'origin' => $lead->origin,
                    'campaign' => $lead->campaign,
                ];

                if ($cnpj) {
                    $client = Client::query()->updateOrCreate(
                        ['cnpj' => $cnpj],
                        $clientPayload + ['cnpj' => $cnpj]
                    );
                } else {
                    $client = Client::query()->create([
                        'cnpj' => null,
                        ...$clientPayload,
                    ]);
                }

                $contactName = (string) $lead->name;
                $contactWhatsapp = $lead->whatsapp ? (string) $lead->whatsapp : null;

                if ($contactWhatsapp) {
                    Contact::query()->firstOrCreate([
                        'client_id' => $client->id,
                        'whatsapp' => $contactWhatsapp,
                    ], [
                        'name' => $contactName,
                        'role' => null,
                    ]);
                } else {
                    Contact::query()->firstOrCreate([
                        'client_id' => $client->id,
                        'name' => $contactName,
                    ], [
                        'whatsapp' => null,
                        'role' => null,
                    ]);
                }
            }
        });

        $this->dispatch('leads-updated', columns: $this->buildColumns());
    }

    public function render()
    {
        $columns = $this->buildColumns();

        $users = User::query()->orderBy('name')->get(['id', 'name']);

        $plans = Plan::query()->orderBy('name')->get(['id', 'name', 'price']);
        $services = Service::query()->orderBy('name')->get(['id', 'name', 'price']);

        $planOptions = $plans->map(fn ($p) => ['value' => $p->id, 'label' => $p->name])->prepend([
            'value' => '',
            'label' => __('app.leads.form.plan_custom'),
        ])->all();

        $serviceOptions = $services->map(fn ($s) => ['value' => (string) $s->id, 'label' => $s->name])->values()->all();

        return view('livewire.leads.index', [
            'columns' => $columns,
            'users' => $users,
            'planOptions' => $planOptions,
            'serviceOptions' => $serviceOptions,
        ])->layout('layouts.app');
    }
}
