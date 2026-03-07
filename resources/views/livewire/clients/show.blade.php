<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.clients.profile.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.clients.profile.subtitle', ['name' => $client->name]) }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                variant="secondary"
                href="/clients"
                icon="solar:arrow-left-linear"
            >
                {{ __('app.clients.profile.back') }}
            </x-ds::button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-ds::card class="lg:col-span-1" title="{{ __('app.clients.profile.cards.about.title') }}" description="{{ __('app.clients.profile.cards.about.description') }}">
            <div class="space-y-4">
                <div>
                    <div class="text-xs font-medium text-(--text-muted)">{{ __('app.clients.profile.fields.name') }}</div>
                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">{{ $client->name }}</div>
                </div>

                <div>
                    <div class="text-xs font-medium text-(--text-muted)">{{ __('app.clients.profile.fields.cnpj') }}</div>
                    <div class="mt-1 text-sm text-(--text-secondary)">{{ $client->cnpj ?: __('app.common.dash') }}</div>
                </div>

                <div>
                    <div class="text-xs font-medium text-(--text-muted)">{{ __('app.clients.profile.fields.category') }}</div>
                    <div class="mt-1">
                        <x-ds::badge variant="secondary">{{ $client->category ?: __('app.common.dash') }}</x-ds::badge>
                    </div>
                </div>
            </div>
        </x-ds::card>

        <div class="space-y-6 lg:col-span-2">
            <x-ds::card title="{{ __('app.clients.profile.cards.contracted_services.title') }}" description="{{ __('app.clients.profile.cards.contracted_services.description') }}">
                <div class="text-sm text-(--text-secondary)">
                    {{ __('app.clients.profile.cards.contracted_services.empty') }}
                </div>
            </x-ds::card>

            <x-ds::card title="{{ __('app.clients.profile.cards.timeline.title') }}" description="{{ __('app.clients.profile.cards.timeline.description') }}">
                <div class="text-sm text-(--text-secondary)">
                    {{ __('app.clients.profile.cards.timeline.empty') }}
                </div>
            </x-ds::card>
        </div>
    </div>
</div>
