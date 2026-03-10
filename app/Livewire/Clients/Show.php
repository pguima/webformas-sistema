<?php

namespace App\Livewire\Clients;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\Service;
use Livewire\Component;

class Show extends Component
{
    public Client $client;

    public function mount(Client $client): void
    {
        $this->client = $client;
    }

    public function render()
    {
        $serviceIds = $this->client->service_ids;
        if (!is_array($serviceIds)) {
            $serviceIds = [];
        }

        $services = !empty($serviceIds)
            ? Service::query()->whereIn('id', $serviceIds)->orderBy('name')->get()
            : collect();

        $campaign = Campaign::query()->where('client_id', $this->client->id)->first();
        $hasManagerId = (bool) ($campaign?->manager_customer_id && trim((string) $campaign->manager_customer_id) !== '');
        $hasClientId = (bool) ($campaign?->client_customer_id && trim((string) $campaign->client_customer_id) !== '');

        $googleAdsActive = $hasManagerId && $hasClientId;

        return view('livewire.clients.show', [
            'client' => $this->client,
            'plan' => $this->client->plan,
            'services' => $services,
            'campaign' => $campaign,
            'googleAdsActive' => $googleAdsActive,
        ])->layout('layouts.app');
    }
}
