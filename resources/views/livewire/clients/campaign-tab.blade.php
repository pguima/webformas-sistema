<div class="space-y-6">
    <div>
        <h2 class="text-lg font-semibold text-(--text-primary)">{{ __('app.campaigns.client_tab.title') }}</h2>
        <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.campaigns.client_tab.subtitle', ['name' => $client->name]) }}</p>
    </div>

    <livewire:campaigns.dashboard :campaign="$campaign" :key="'campaign-dashboard-client-' . $campaign->id" />
</div>
