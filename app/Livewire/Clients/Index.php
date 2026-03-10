<?php

namespace App\Livewire\Clients;

use App\Livewire\Concerns\HasViewMode;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, HasViewMode, WithFileUploads;

    // Search
    public string $search = '';

    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    // Form
    public ?int $clientId = null;

    public ?string $name = null;

    public ?string $cnpj = null;

    public ?string $category = null;

    /** @var TemporaryUploadedFile|null */
    public $logo = null;

    public ?string $current_logo_path = null;

    public ?int $plan_id = null;

    /** @var array<int, string> */
    public array $service_ids = [];

    public $contract_value = null;

    public ?string $origin = null;

    public ?string $campaign = null;

    public array $prefetchedClients = [];

    // Delete
    public ?int $clientToDeleteId = null;

    public string $deleteConfirmation = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:18'],
            'category' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg,gif', 'max:4096'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'service_ids' => ['array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'contract_value' => ['nullable', 'numeric', 'min:0'],
            'origin' => ['nullable', 'string', 'max:255'],
            'campaign' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function updatedPlanId(): void
    {
        $this->syncServicesFromPlan();
    }

    private function syncServicesFromPlan(): void
    {
        if (! $this->plan_id) {
            return;
        }

        $plan = Plan::query()->with('services:id')->find($this->plan_id);
        if (! $plan) {
            return;
        }

        $this->service_ids = $plan->services
            ->pluck('id')
            ->map(fn ($v) => (string) $v)
            ->values()
            ->all();
    }

    public function create(): void
    {
        $this->reset([
            'clientId',
            'name',
            'cnpj',
            'category',
            'logo',
            'current_logo_path',
            'plan_id',
            'service_ids',
            'contract_value',
            'origin',
            'campaign',
        ]);

        $this->service_ids = [];
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedClients[$id])) {
            return;
        }

        $client = Client::query()
            ->select(['id', 'name', 'cnpj', 'category', 'logo_path', 'plan_id', 'service_ids', 'contract_value', 'origin', 'campaign'])
            ->find($id);

        if (!$client) {
            return;
        }

        $this->prefetchedClients[$id] = [
            'name' => $client->name,
            'cnpj' => $client->cnpj,
            'category' => $client->category,
            'logo_path' => $client->logo_path,
            'plan_id' => $client->plan_id,
            'service_ids' => is_array($client->service_ids) ? $client->service_ids : [],
            'contract_value' => $client->contract_value,
            'origin' => $client->origin,
            'campaign' => $client->campaign,
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedClients[$id])) {
            $this->clientId = $id;
            $this->name = $this->prefetchedClients[$id]['name'] ?? null;
            $this->cnpj = $this->prefetchedClients[$id]['cnpj'] ?? null;
            $this->category = $this->prefetchedClients[$id]['category'] ?? null;
            $this->logo = null;
            $this->current_logo_path = $this->prefetchedClients[$id]['logo_path'] ?? null;
            $this->plan_id = $this->prefetchedClients[$id]['plan_id'] ?? null;
            $this->service_ids = array_map('strval', $this->prefetchedClients[$id]['service_ids'] ?? []);
            $this->contract_value = $this->prefetchedClients[$id]['contract_value'] ?? null;
            $this->origin = $this->prefetchedClients[$id]['origin'] ?? null;
            $this->campaign = $this->prefetchedClients[$id]['campaign'] ?? null;
            return;
        }

        $client = Client::findOrFail($id);
        $this->clientId = $client->id;
        $this->name = $client->name;
        $this->cnpj = $client->cnpj;
        $this->category = $client->category;
        $this->logo = null;
        $this->current_logo_path = $client->logo_path;
        $this->plan_id = $client->plan_id;
        $this->service_ids = array_map('strval', $client->service_ids ?? []);
        $this->contract_value = $client->contract_value;
        $this->origin = $client->origin;
        $this->campaign = $client->campaign;
    }

    public function save(): void
    {
        $data = $this->validate();

        $logoPath = null;
        if (! empty($data['logo'])) {
            $logoPath = $data['logo']->store('clients', 'public');
        }

        if ($this->clientId) {
            $client = Client::findOrFail($this->clientId);

            $oldLogoPath = $client->logo_path;

            $client->update([
                'name' => $this->name,
                'cnpj' => $this->cnpj,
                'category' => $this->category,
                'logo_path' => $logoPath ?: $client->logo_path,
                'plan_id' => $data['plan_id'] ?? null,
                'service_ids' => $data['service_ids'] ?? [],
                'contract_value' => $data['contract_value'] ?? null,
                'origin' => $data['origin'] ?? null,
                'campaign' => $data['campaign'] ?? null,
            ]);

            if ($logoPath && $oldLogoPath && str_starts_with($oldLogoPath, 'clients/')) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            $this->dispatch('notify', message: __('app.clients.messages.updated_success'), variant: 'success', title: __('app.clients.messages.success_title'));
        } else {
            $client = Client::create([
                'name' => $this->name,
                'cnpj' => $this->cnpj,
                'category' => $this->category,
                'logo_path' => $logoPath,
                'plan_id' => $data['plan_id'] ?? null,
                'service_ids' => $data['service_ids'] ?? [],
                'contract_value' => $data['contract_value'] ?? null,
                'origin' => $data['origin'] ?? null,
                'campaign' => $data['campaign'] ?? null,
            ]);

            $this->dispatch('notify', message: __('app.clients.messages.created_success'), variant: 'success', title: __('app.clients.messages.success_title'));
        }

        $this->dispatch('close-client-offcanvas');
        $this->reset([
            'clientId',
            'name',
            'cnpj',
            'category',
            'logo',
            'current_logo_path',
            'plan_id',
            'service_ids',
            'contract_value',
            'origin',
            'campaign',
        ]);

        $this->service_ids = [];
    }

    public function confirmDelete(int $id): void
    {
        $this->clientToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.clients.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $client = Client::find($this->clientToDeleteId);

        if (!$client) {
            $this->dispatch('notify', message: __('app.clients.messages.error_not_found'), variant: 'danger', title: __('app.clients.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['clientToDeleteId', 'deleteConfirmation']);
            return;
        }

        $client->delete();

        $this->dispatch('notify', message: __('app.clients.messages.deleted_success'), variant: 'success', title: __('app.clients.messages.success_title'));
        $this->dispatch('close-delete-modal');
        $this->reset(['clientToDeleteId', 'deleteConfirmation']);
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('cnpj', 'like', '%' . $this->search . '%')
                        ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        $plans = Plan::query()->orderBy('name')->get(['id', 'name', 'price']);
        $services = Service::query()->orderBy('name')->get(['id', 'name', 'price']);

        $planOptions = $plans->map(fn ($p) => ['value' => $p->id, 'label' => $p->name])->prepend([
            'value' => '',
            'label' => __('app.leads.form.plan_custom'),
        ])->all();

        $serviceOptions = $services->map(fn ($s) => ['value' => (string) $s->id, 'label' => $s->name])->values()->all();

        return view('livewire.clients.index', [
            'clients' => $clients,
            'planOptions' => $planOptions,
            'serviceOptions' => $serviceOptions,
        ])->layout('layouts.app');
    }
}
