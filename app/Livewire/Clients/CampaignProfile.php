<?php

namespace App\Livewire\Clients;

use App\Models\Campaign;
use App\Models\Client;
use Livewire\Component;

class CampaignProfile extends Component
{
    public Client $client;

    public Campaign $campaign;

    public ?string $manager_customer_id = null;

    public ?string $client_customer_id = null;

    public function mount(Client $client): void
    {
        $this->client = $client;

        $this->campaign = Campaign::query()->firstOrCreate(
            ['client_id' => $this->client->id],
            ['manager_customer_id' => null, 'client_customer_id' => null]
        );

        $this->manager_customer_id = $this->campaign->manager_customer_id;
        $this->client_customer_id = $this->campaign->client_customer_id;
    }

    public function rules(): array
    {
        return [
            'manager_customer_id' => ['nullable', 'string', 'max:255'],
            'client_customer_id' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $this->campaign->update($data);

        $this->dispatch('notify', message: __('app.campaigns.messages.updated_success'), variant: 'success', title: __('app.campaigns.messages.success_title'));
    }

    public function render()
    {
        return view('livewire.clients.campaign-profile');
    }
}
