<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                @if ($client->logo_url)
                    <img
                        src="{{ $client->logo_url }}"
                        alt="logo"
                        class="h-10 w-10 rounded-lg object-contain border border-(--border-subtle) bg-(--surface-card)"
                        loading="lazy"
                    />
                @endif

                <h1 class="text-2xl font-semibold text-(--text-primary)">{{ $client->name }}</h1>
            </div>

            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.clients.profile.subtitle') }}</p>
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

    <div
        x-data="{ active: 'profile' }"
        class="w-full"
    >
        <div class="flex flex-wrap items-center border-b border-(--border-default)" role="tablist" aria-label="tabs">
            <button
                type="button"
                role="tab"
                x-on:click="active = 'profile'"
                x-bind:aria-selected="active === 'profile' ? 'true' : 'false'"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20"
                x-bind:class="active === 'profile' ? 'border-b-2 border-(--color-primary) text-(--color-primary)' : 'border-b-2 border-transparent text-(--text-secondary) hover:text-(--text-primary)'"
            >
                {{ __('app.clients.profile.tabs.profile') }}
            </button>

            <button
                type="button"
                role="tab"
                x-on:click="active = 'web'"
                x-bind:aria-selected="active === 'web' ? 'true' : 'false'"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20"
                x-bind:class="active === 'web' ? 'border-b-2 border-(--color-primary) text-(--color-primary)' : 'border-b-2 border-transparent text-(--text-secondary) hover:text-(--text-primary)'"
            >
                {{ __('app.clients.profile.tabs.web') }}
            </button>

            <button
                type="button"
                role="tab"
                x-on:click="active = 'campaign'; $nextTick(() => { window.dispatchEvent(new CustomEvent('campaign-tab-activated')); window.dispatchEvent(new CustomEvent('ds-chart-rerender')); setTimeout(() => window.dispatchEvent(new CustomEvent('ds-chart-rerender')), 50); })"
                x-bind:aria-selected="active === 'campaign' ? 'true' : 'false'"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20"
                x-bind:class="active === 'campaign' ? 'border-b-2 border-(--color-primary) text-(--color-primary)' : 'border-b-2 border-transparent text-(--text-secondary) hover:text-(--text-primary)'"
            >
                {{ __('app.clients.profile.tabs.campaign') }}
            </button>

            <button
                type="button"
                role="tab"
                x-on:click="active = 'contacts'"
                x-bind:aria-selected="active === 'contacts' ? 'true' : 'false'"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20"
                x-bind:class="active === 'contacts' ? 'border-b-2 border-(--color-primary) text-(--color-primary)' : 'border-b-2 border-transparent text-(--text-secondary) hover:text-(--text-primary)'"
            >
                {{ __('app.clients.profile.tabs.contacts') }}
            </button>
        </div>

        <div class="mt-6">
            <div x-cloak x-show="active === 'profile'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-1" x-transition:enter-end="opacity-100 translate-x-0">
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
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <div class="text-xs font-medium text-(--text-muted)">{{ __('app.clients.form.plan') }}</div>
                                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">
                                        {{ $plan?->name ?: __('app.common.dash') }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs font-medium text-(--text-muted)">{{ __('app.clients.form.services') }}</div>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @if (($services?->count() ?? 0) > 0)
                                            @foreach ($services as $service)
                                                <x-ds::badge variant="secondary">{{ $service->name }}</x-ds::badge>
                                            @endforeach
                                        @else
                                            <span class="text-sm text-(--text-secondary)">{{ __('app.common.dash') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <div class="text-xs font-medium text-(--text-muted)">{{ __('app.clients.form.contract_value') }}</div>
                                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">
                                        @if (!is_null($client->contract_value) && $client->contract_value !== '')
                                            R$ {{ number_format((float) $client->contract_value, 2, ',', '.') }}
                                        @else
                                            {{ __('app.common.dash') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </x-ds::card>

                        <x-ds::card title="{{ __('app.clients.profile.cards.campaign.title') }}" description="{{ __('app.clients.profile.cards.campaign.description') }}">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex flex-col gap-2">
                                    <div>
                                        @if ($googleAdsActive)
                                            <x-ds::badge variant="success" style="soft" dot>Google ADS ativo</x-ds::badge>
                                        @else
                                            <x-ds::badge variant="warning" style="soft" dot>Google ADS pendente</x-ds::badge>
                                        @endif
                                    </div>

                                    <div class="text-sm text-(--text-secondary)">
                                        {{ __('app.clients.profile.cards.campaign.description') }}
                                    </div>
                                </div>

                                <x-ds::modal size="lg">
                                    <x-slot:trigger>
                                        <x-ds::button variant="secondary" icon="solar:pen-linear">
                                            {{ __('app.common.edit') }}
                                        </x-ds::button>
                                    </x-slot:trigger>

                                    <x-slot:title>
                                        {{ __('app.campaigns.profile_card.title') }}
                                    </x-slot:title>

                                    <x-slot:description>
                                        {{ __('app.campaigns.profile_card.description') }}
                                    </x-slot:description>

                                    <livewire:clients.campaign-profile :client="$client" :key="'client-campaign-profile-' . $client->id" />
                                </x-ds::modal>
                            </div>
                        </x-ds::card>
                    </div>
                </div>
            </div>

            <div x-cloak x-show="active === 'web'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-1" x-transition:enter-end="opacity-100 translate-x-0">
                <livewire:clients.webs :client="$client" :key="'client-webs-' . $client->id" />
            </div>

            <div x-cloak x-show="active === 'campaign'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-1" x-transition:enter-end="opacity-100 translate-x-0">
                <livewire:clients.campaign-tab :client="$client" :key="'client-campaign-tab-' . $client->id" />
            </div>

            <div x-cloak x-show="active === 'contacts'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-x-1" x-transition:enter-end="opacity-100 translate-x-0">
                <livewire:clients.contacts :client="$client" :key="'client-contacts-' . $client->id" />
            </div>
        </div>
    </div>
</div>
