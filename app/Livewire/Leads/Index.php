<?php

namespace App\Livewire\Leads;

use App\Models\Lead;
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

    public ?string $plan = null;

    public ?string $services = null;

    public $value = null;

    public ?int $responsible_user_id = null;

    public ?string $origin = null;

    public ?string $campaign = null;

    public string $stage = 'Novo';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'plan' => ['nullable', 'string', 'max:255'],
            'services' => ['nullable', 'string'],
            'value' => ['nullable', 'numeric', 'min:0'],
            'responsible_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'origin' => ['nullable', 'string', 'max:255'],
            'campaign' => ['nullable', 'string', 'max:255'],
            'stage' => ['required', Rule::in(self::STAGES)],
        ];
    }

    public function create(): void
    {
        $this->reset(['leadId', 'name', 'whatsapp', 'plan', 'services', 'value', 'responsible_user_id', 'origin', 'campaign']);
        $this->stage = 'Novo';
    }

    public function edit(int $id): void
    {
        $lead = Lead::findOrFail($id);

        $this->leadId = $lead->id;
        $this->name = $lead->name;
        $this->whatsapp = $lead->whatsapp;
        $this->plan = $lead->plan;
        $this->services = $lead->services;
        $this->value = $lead->value;
        $this->responsible_user_id = $lead->responsible_user_id;
        $this->origin = $lead->origin;
        $this->campaign = $lead->campaign;
        $this->stage = $lead->stage;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->leadId) {
            $lead = Lead::findOrFail($this->leadId);
            $lead->update($data);

            $this->dispatch('notify', message: __('app.leads.messages.updated_success'), variant: 'success', title: __('app.leads.messages.success_title'));
        } else {
            $position = (int) (Lead::query()->where('stage', $data['stage'])->max('position') ?? 0);

            $lead = Lead::create(array_merge($data, [
                'position' => $position + 1,
            ]));

            $this->dispatch('notify', message: __('app.leads.messages.created_success'), variant: 'success', title: __('app.leads.messages.success_title'));
        }

        $this->dispatch('close-lead-offcanvas');
        $this->reset(['leadId', 'name', 'whatsapp', 'plan', 'services', 'value', 'responsible_user_id', 'origin', 'campaign']);
        $this->stage = 'Novo';
    }

    public function delete(int $id): void
    {
        $lead = Lead::find($id);
        if (! $lead) {
            $this->dispatch('notify', message: __('app.leads.messages.error_not_found'), variant: 'danger', title: __('app.leads.messages.error_title'));
            return;
        }

        $lead->delete();

        $this->dispatch('notify', message: __('app.leads.messages.deleted_success'), variant: 'success', title: __('app.leads.messages.success_title'));
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

        DB::transaction(function () use ($lead, $toStage, $toIndex) {
            $lead->update([
                'stage' => $toStage,
                'position' => max(0, $toIndex),
            ]);
        });
    }

    public function render()
    {
        $leads = Lead::query()
            ->with(['responsibleUser:id,name'])
            ->orderBy('stage')
            ->orderBy('position')
            ->get();

        $users = User::query()->orderBy('name')->get(['id', 'name']);

        $columns = collect(self::STAGES)
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
                            'whatsapp' => $lead->whatsapp,
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

        return view('livewire.leads.index', [
            'columns' => $columns,
            'users' => $users,
        ])->layout('layouts.app');
    }
}
