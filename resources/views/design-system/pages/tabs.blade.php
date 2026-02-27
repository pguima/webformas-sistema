@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.tabs.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.tabs.subtitle') }}
                </div>
            </div>
        </div>

        @php
            $tabsBasic = [
                [
                    'label' => __('ds.pages.tabs.items.home.label'),
                    'icon' => 'solar:home-smile-angle-outline',
                    'content' => __('ds.pages.tabs.items.home.content'),
                ],
                [
                    'label' => __('ds.pages.tabs.items.details.label'),
                    'icon' => 'solar:document-text-outline',
                    'content' => __('ds.pages.tabs.items.details.content'),
                ],
                [
                    'label' => __('ds.pages.tabs.items.profile.label'),
                    'icon' => 'solar:user-outline',
                    'content' => __('ds.pages.tabs.items.profile.content'),
                    'badge' => __('ds.pages.tabs.items.profile.badge'),
                ],
                [
                    'label' => __('ds.pages.tabs.items.settings.label'),
                    'icon' => 'solar:settings-outline',
                    'content' => __('ds.pages.tabs.items.settings.content'),
                    'disabled' => true,
                ],
            ];
        @endphp

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.tabs.sections.underline')" :description="__('ds.pages.tabs.sections.underline_description')">
                <div class="mt-4">
                    <x-ds::tabs :tabs="$tabsBasic" variant="underline" :defaultTab="0" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.tabs.sections.pill')" :description="__('ds.pages.tabs.sections.pill_description')">
                <div class="mt-4">
                    <x-ds::tabs :tabs="$tabsBasic" variant="pill" :defaultTab="1" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.tabs.sections.button')" :description="__('ds.pages.tabs.sections.button_description')">
                <div class="mt-4">
                    <x-ds::tabs :tabs="$tabsBasic" variant="button" :defaultTab="0" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.tabs.sections.full_width')" :description="__('ds.pages.tabs.sections.full_width_description')">
                <div class="mt-4">
                    <x-ds::tabs :tabs="$tabsBasic" variant="pill" :fullWidth="true" :defaultTab="2" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full lg:col-span-2" :title="__('ds.pages.tabs.sections.composition')" :description="__('ds.pages.tabs.sections.composition_description')">
                <div class="mt-4 space-y-4">
                    <x-ds::alert variant="info" style="soft" :title="__('ds.pages.tabs.labels.alert_title')" icon="solar:info-circle-linear" :dismissible="false">
                        <div class="flex flex-wrap items-center gap-2">
                            <span>{{ __('ds.pages.tabs.labels.alert_body') }}</span>
                            <x-ds::tooltip text="{{ __('ds.pages.tabs.labels.tip_help') }}" placement="top" variant="dark">
                                <button type="button" class="inline-flex items-center text-(--ds-primary) hover:underline">
                                    {{ __('ds.pages.tabs.labels.help') }}
                                </button>
                            </x-ds::tooltip>
                        </div>
                    </x-ds::alert>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="rounded-2xl border border-(--ds-border) bg-(--ds-surface) p-5">
                            <div class="text-sm font-semibold">{{ __('ds.pages.tabs.labels.accordion_title') }}</div>
                            <div class="mt-3">
                                <x-ds::accordion
                                    :items="[
                                        ['title' => __('ds.pages.tabs.labels.acc_item_1'), 'content' => __('ds.pages.tabs.labels.acc_body_1')],
                                        ['title' => __('ds.pages.tabs.labels.acc_item_2'), 'content' => __('ds.pages.tabs.labels.acc_body_2')],
                                    ]"
                                    :defaultOpen="0"
                                />
                            </div>
                        </div>

                        <div class="rounded-2xl border border-(--ds-border) bg-(--ds-surface) p-5">
                            <div class="text-sm font-semibold">{{ __('ds.pages.tabs.labels.loading_title') }}</div>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <x-ds::button :loading="true">{{ __('ds.pages.tabs.labels.loading') }}</x-ds::button>
                                <x-ds::spinner size="sm" variant="info" />
                                <x-ds::badge style="soft" variant="info">{{ __('ds.pages.tabs.labels.badge') }}</x-ds::badge>
                            </div>
                        </div>
                    </div>

                    <x-ds::toast
                        variant="info"
                        style="soft"
                        :duration="0"
                        :dismissible="false"
                        :title="__('ds.pages.tabs.labels.toast_title')"
                        icon="solar:info-circle-linear"
                    >
                        {{ __('ds.pages.tabs.labels.toast_body') }}
                    </x-ds::toast>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.tabs.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.tabs.docs.usage_title')" :description="__('ds.pages.tabs.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tabs.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::tabs :tabs="$tabsBasic" variant="underline" :defaultTab="0" />
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tabs.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::tabs
    :tabs="[
        ['label' => 'Home', 'content' => 'Tab content'],
        ['label' => 'Details', 'content' => 'More content', 'badge' => 'New'],
        ['label' => 'Settings', 'content' => 'Disabled', 'disabled' => true],
    ]"
    :defaultTab="0"
    variant="underline"
/>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tabs.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">tabs</span> — {{ __('ds.pages.tabs.docs.props.tabs') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">defaultTab</span> — {{ __('ds.pages.tabs.docs.props.default_tab') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.tabs.docs.props.variant') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">size</span> — {{ __('ds.pages.tabs.docs.props.size') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">fullWidth</span> — {{ __('ds.pages.tabs.docs.props.full_width') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tabs.docs.accessibility_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div>{{ __('ds.pages.tabs.docs.accessibility.body') }}</div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
