@extends('layouts.layout-ds')

@section('content')
    <div>
        <!-- Header -->
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-(--text-primary)">
                    {{ __('ds.dashboard.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--text-secondary)">
                    {{ __('ds.dashboard.subtitle') }}
                </div>
            </div>

            <x-ds::button href="{{ url('/design-system/blank') }}" variant="secondary" icon="solar:document-text-outline">
                {{ __('ds.dashboard.open_template') }}
            </x-ds::button>
        </div>

        <!-- Stats Cards Grid -->
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Stats Card 1 - New Users -->
            <x-ds::card class="h-full" :padded="false" :hoverable="true">
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-(--color-primary) text-white">
                                <iconify-icon icon="mingcute:user-follow-fill" class="text-xl"></iconify-icon>
                            </span>
                            <div>
                                <div class="text-sm text-(--text-secondary)">{{ __('ds.dashboard.cards.new_users') }}
                                </div>
                                <div class="text-xl font-semibold text-(--text-primary)">15,000</div>
                            </div>
                        </div>
                        <div class="h-10 w-20 rounded-lg bg-(--surface-hover)"></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm text-(--text-secondary)">
                        <span>{{ __('ds.dashboard.cards.increase_by') }}</span>
                        <x-ds::badge variant="green" size="sm">+200</x-ds::badge>
                        <span>{{ __('ds.dashboard.cards.this_week') }}</span>
                    </div>
                </div>
            </x-ds::card>

            <!-- Stats Card 2 - Active Users -->
            <x-ds::card class="h-full" :padded="false" :hoverable="true">
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-(--status-success) text-white">
                                <iconify-icon icon="mingcute:user-follow-fill" class="text-xl"></iconify-icon>
                            </span>
                            <div>
                                <div class="text-sm text-(--text-secondary)">
                                    {{ __('ds.dashboard.cards.active_users') }}</div>
                                <div class="text-xl font-semibold text-(--text-primary)">8,000</div>
                            </div>
                        </div>
                        <div class="h-10 w-20 rounded-lg bg-(--surface-hover)"></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm text-(--text-secondary)">
                        <span>{{ __('ds.dashboard.cards.increase_by') }}</span>
                        <x-ds::badge variant="green" size="sm">+200</x-ds::badge>
                        <span>{{ __('ds.dashboard.cards.this_week') }}</span>
                    </div>
                </div>
            </x-ds::card>

            <!-- Stats Card 3 - Total Sales -->
            <x-ds::card class="h-full" :padded="false" :hoverable="true">
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-(--status-warning) text-white">
                                <iconify-icon icon="iconamoon:discount-fill" class="text-xl"></iconify-icon>
                            </span>
                            <div>
                                <div class="text-sm text-(--text-secondary)">{{ __('ds.dashboard.cards.total_sales') }}
                                </div>
                                <div class="text-xl font-semibold text-(--text-primary)">$500,000</div>
                            </div>
                        </div>
                        <div class="h-10 w-20 rounded-lg bg-(--surface-hover)"></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm text-(--text-secondary)">
                        <span>{{ __('ds.dashboard.cards.increase_by') }}</span>
                        <x-ds::badge variant="red" size="sm">-$10k</x-ds::badge>
                        <span>{{ __('ds.dashboard.cards.this_week') }}</span>
                    </div>
                </div>
            </x-ds::card>

            <!-- Stats Card 4 - Conversion -->
            <x-ds::card class="h-full" :padded="false" :hoverable="true">
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-(--tag-purple-text) text-white">
                                <iconify-icon icon="mdi:message-text" class="text-xl"></iconify-icon>
                            </span>
                            <div>
                                <div class="text-sm text-(--text-secondary)">{{ __('ds.dashboard.cards.conversion') }}
                                </div>
                                <div class="text-xl font-semibold text-(--text-primary)">25%</div>
                            </div>
                        </div>
                        <div class="h-10 w-20 rounded-lg bg-(--surface-hover)"></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm text-(--text-secondary)">
                        <span>{{ __('ds.dashboard.cards.increase_by') }}</span>
                        <x-ds::badge variant="green" size="sm">+5%</x-ds::badge>
                        <span>{{ __('ds.dashboard.cards.this_week') }}</span>
                    </div>
                </div>
            </x-ds::card>

            <!-- Stats Card 5 - Leads -->
            <x-ds::card class="h-full" :padded="false" :hoverable="true">
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-(--tag-pink-text) text-white">
                                <iconify-icon icon="mdi:leads" class="text-xl"></iconify-icon>
                            </span>
                            <div>
                                <div class="text-sm text-(--text-secondary)">{{ __('ds.dashboard.cards.leads') }}</div>
                                <div class="text-xl font-semibold text-(--text-primary)">250</div>
                            </div>
                        </div>
                        <div class="h-10 w-20 rounded-lg bg-(--surface-hover)"></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm text-(--text-secondary)">
                        <span>{{ __('ds.dashboard.cards.increase_by') }}</span>
                        <x-ds::badge variant="green" size="sm">+20</x-ds::badge>
                        <span>{{ __('ds.dashboard.cards.this_week') }}</span>
                    </div>
                </div>
            </x-ds::card>

            <!-- Stats Card 6 - Total Profit -->
            <x-ds::card class="h-full" :padded="false" :hoverable="true">
                <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex h-11 w-11 items-center justify-center rounded-lg bg-(--status-info) text-white">
                                <iconify-icon icon="streamline:bag-dollar-solid" class="text-xl"></iconify-icon>
                            </span>
                            <div>
                                <div class="text-sm text-(--text-secondary)">
                                    {{ __('ds.dashboard.cards.total_profit') }}</div>
                                <div class="text-xl font-semibold text-(--text-primary)">$300,700</div>
                            </div>
                        </div>
                        <div class="h-10 w-20 rounded-lg bg-(--surface-hover)"></div>
                    </div>
                    <div class="mt-4 flex items-center gap-2 text-sm text-(--text-secondary)">
                        <span>{{ __('ds.dashboard.cards.increase_by') }}</span>
                        <x-ds::badge variant="green" size="sm">+$15k</x-ds::badge>
                        <span>{{ __('ds.dashboard.cards.this_week') }}</span>
                    </div>
                </div>
            </x-ds::card>
        </div>

        <!-- Charts Section -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-12">
            <!-- Revenue Card -->
            <div class="lg:col-span-5">
                <x-ds::card class="h-full" :padded="false">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-lg font-semibold text-(--text-primary)">
                                    {{ __('ds.dashboard.revenue.title') }}</div>
                                <div class="mt-1 text-sm text-(--text-secondary)">
                                    {{ __('ds.dashboard.revenue.subtitle') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xl font-semibold text-(--text-primary)">$50,000.00</div>
                                <x-ds::badge variant="green" class="mt-2">+$10k</x-ds::badge>
                            </div>
                        </div>
                        <div class="mt-6 h-48 rounded-lg bg-(--surface-hover)"></div>
                    </div>
                </x-ds::card>
            </div>

            <!-- Earning Card -->
            <div class="lg:col-span-7">
                <x-ds::card class="h-full" :padded="false">
                    <div class="p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <div class="text-lg font-semibold text-(--text-primary)">
                                    {{ __('ds.dashboard.earning.title') }}</div>
                                <div class="mt-1 text-sm text-(--text-secondary)">
                                    {{ __('ds.dashboard.earning.subtitle') }}</div>
                            </div>
                            <select
                                class="h-10 rounded-md border border-(--border-default) bg-(--surface-card) px-4 text-sm text-(--text-primary) transition-colors focus:border-(--border-focus) focus:outline-none focus:ring-2 focus:ring-(--color-primary)/10">
                                <option>{{ __('ds.dashboard.filters.yearly') }}</option>
                                <option>{{ __('ds.dashboard.filters.monthly') }}</option>
                                <option>{{ __('ds.dashboard.filters.weekly') }}</option>
                                <option>{{ __('ds.dashboard.filters.today') }}</option>
                            </select>
                        </div>

                        <!-- Mini stat cards -->
                        <div class="mt-5 flex flex-wrap gap-3">
                            <div
                                class="group flex items-center gap-3 rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3 transition-all duration-200 hover:border-(--color-primary) hover:shadow-(--shadow-sm)">
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-(--surface-hover) text-(--text-secondary) transition-colors group-hover:bg-(--color-primary) group-hover:text-white">
                                    <iconify-icon icon="fluent:cart-16-filled" class="text-lg"></iconify-icon>
                                </span>
                                <div>
                                    <div class="text-xs text-(--text-muted)">{{ __('ds.dashboard.earning.sales') }}
                                    </div>
                                    <div class="text-sm font-semibold text-(--text-primary)">$200k</div>
                                </div>
                            </div>

                            <div
                                class="group flex items-center gap-3 rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3 transition-all duration-200 hover:border-(--color-primary) hover:shadow-(--shadow-sm)">
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-(--surface-hover) text-(--text-secondary) transition-colors group-hover:bg-(--color-primary) group-hover:text-white">
                                    <iconify-icon icon="uis:chart" class="text-lg"></iconify-icon>
                                </span>
                                <div>
                                    <div class="text-xs text-(--text-muted)">{{ __('ds.dashboard.earning.income') }}
                                    </div>
                                    <div class="text-sm font-semibold text-(--text-primary)">$200k</div>
                                </div>
                            </div>

                            <div
                                class="group flex items-center gap-3 rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3 transition-all duration-200 hover:border-(--color-primary) hover:shadow-(--shadow-sm)">
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-(--surface-hover) text-(--text-secondary) transition-colors group-hover:bg-(--color-primary) group-hover:text-white">
                                    <iconify-icon icon="ph:arrow-fat-up-fill" class="text-lg"></iconify-icon>
                                </span>
                                <div>
                                    <div class="text-xs text-(--text-muted)">{{ __('ds.dashboard.earning.profit') }}
                                    </div>
                                    <div class="text-sm font-semibold text-(--text-primary)">$200k</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 h-48 rounded-lg bg-(--surface-hover)"></div>
                    </div>
                </x-ds::card>
            </div>
        </div>
    </div>
@endsection