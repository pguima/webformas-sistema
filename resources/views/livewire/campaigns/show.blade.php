<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.campaigns.dashboard.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.campaigns.dashboard.subtitle', ['name' => $campaign->client?->name]) }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                variant="secondary"
                href="/campaigns"
                icon="solar:arrow-left-linear"
            >
                {{ __('app.campaigns.dashboard.back') }}
            </x-ds::button>
        </div>
    </div>

    <livewire:campaigns.dashboard :campaign="$campaign" :key="'campaign-dashboard-' . $campaign->id" />
</div>
