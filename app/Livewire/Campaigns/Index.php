<?php

namespace App\Livewire\Campaigns;

use App\Livewire\Concerns\HasViewMode;
use App\Models\Campaign;
use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, HasViewMode;

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

    // Form (no create on this page)
    public ?int $campaignId = null;

    public ?int $client_id = null;

    public ?string $manager_customer_id = null;

    public ?string $client_customer_id = null;

    /** @var array<int, array<string, mixed>> */
    public array $prefetchedCampaigns = [];

    // Delete
    public ?int $campaignToDeleteId = null;

    public string $deleteConfirmation = '';

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id', 'unique:campaigns,client_id,' . ($this->campaignId ?? 'NULL') . ',id'],
            'manager_customer_id' => ['nullable', 'string', 'max:255'],
            'client_customer_id' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function prefetch(int $id): void
    {
        if (isset($this->prefetchedCampaigns[$id])) {
            return;
        }

        $campaign = Campaign::query()
            ->select(['id', 'client_id', 'manager_customer_id', 'client_customer_id'])
            ->find($id);

        if (!$campaign) {
            return;
        }

        $this->prefetchedCampaigns[$id] = [
            'client_id' => $campaign->client_id,
            'manager_customer_id' => $campaign->manager_customer_id,
            'client_customer_id' => $campaign->client_customer_id,
        ];
    }

    public function edit(int $id): void
    {
        if (isset($this->prefetchedCampaigns[$id])) {
            $this->campaignId = $id;
            $this->fill($this->prefetchedCampaigns[$id]);
            return;
        }

        $campaign = Campaign::findOrFail($id);

        $this->campaignId = $campaign->id;
        $this->client_id = $campaign->client_id;
        $this->manager_customer_id = $campaign->manager_customer_id;
        $this->client_customer_id = $campaign->client_customer_id;
    }

    public function save(): void
    {
        if (!$this->campaignId) {
            $this->dispatch('notify', message: __('app.campaigns.messages.create_disabled'), variant: 'warning', title: __('app.campaigns.messages.error_title'));
            return;
        }

        $data = $this->validate();

        $campaign = Campaign::findOrFail($this->campaignId);
        $campaign->update($data);

        $this->dispatch('notify', message: __('app.campaigns.messages.updated_success'), variant: 'success', title: __('app.campaigns.messages.success_title'));
        $this->dispatch('close-campaign-offcanvas');

        $this->reset(['campaignId', 'client_id', 'manager_customer_id', 'client_customer_id']);
    }

    public function confirmDelete(int $id): void
    {
        $this->campaignToDeleteId = $id;
        $this->deleteConfirmation = '';
        $this->dispatch('open-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteConfirmation !== 'DELETE') {
            $this->addError('deleteConfirmation', __('app.campaigns.delete.placeholder', ['word' => 'DELETE']));
            return;
        }

        $campaign = Campaign::find($this->campaignToDeleteId);
        if (!$campaign) {
            $this->dispatch('notify', message: __('app.campaigns.messages.error_not_found'), variant: 'danger', title: __('app.campaigns.messages.error_title'));
            $this->dispatch('close-delete-modal');
            $this->reset(['campaignToDeleteId', 'deleteConfirmation']);
            return;
        }

        $campaign->delete();

        $this->dispatch('notify', message: __('app.campaigns.messages.deleted_success'), variant: 'success', title: __('app.campaigns.messages.success_title'));
        $this->dispatch('close-delete-modal');
        $this->reset(['campaignToDeleteId', 'deleteConfirmation']);
    }

    public function render()
    {
        $campaigns = Campaign::query()
            ->with('client:id,name')
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('manager_customer_id', 'like', '%' . $this->search . '%')
                    ->orWhere('client_customer_id', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        $clientOptions = Client::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['value' => (string) $c->id, 'label' => $c->name])
            ->values()
            ->all();

        return view('livewire.campaigns.index', [
            'campaigns' => $campaigns,
            'clientOptions' => $clientOptions,
        ])->layout('layouts.app');
    }
}
