<?php

namespace App\Livewire\Clients;

use App\Models\Campaign;
use App\Models\Client;
use Livewire\Component;

class CampaignTab extends Component
{
    public Client $client;

    public Campaign $campaign;

    public function mount(Client $client): void
    {
        $this->client = $client;

        $this->campaign = Campaign::query()->firstOrCreate(
            ['client_id' => $this->client->id],
            ['manager_customer_id' => null, 'client_customer_id' => null]
        );
    }

    public function render()
    {
        return view('livewire.clients.campaign-tab', [
            'campaign' => $this->campaign,
            'client' => $this->client,
        ]);
    }
}
