@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.tooltips.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.tooltips.subtitle') }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.tooltips.sections.default')" :description="__('ds.pages.tooltips.sections.default_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_secondary') }}" variant="secondary">
                        <x-ds::button variant="secondary">{{ __('ds.pages.tooltips.labels.secondary') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_success') }}" variant="success">
                        <x-ds::button variant="success">{{ __('ds.pages.tooltips.labels.success') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_info') }}" variant="info">
                        <x-ds::button variant="outline">{{ __('ds.pages.tooltips.labels.info') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_warning') }}" variant="warning">
                        <x-ds::button variant="warning">{{ __('ds.pages.tooltips.labels.warning') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_danger') }}" variant="danger">
                        <x-ds::button variant="danger">{{ __('ds.pages.tooltips.labels.danger') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_dark') }}" variant="dark">
                        <x-ds::button variant="ghost">{{ __('ds.pages.tooltips.labels.dark') }}</x-ds::button>
                    </x-ds::tooltip>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.tooltips.sections.placement')" :description="__('ds.pages.tooltips.sections.placement_description')">
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_top') }}" placement="top">
                        <x-ds::button variant="outline">{{ __('ds.pages.tooltips.labels.top') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_right') }}" placement="right">
                        <x-ds::button variant="outline">{{ __('ds.pages.tooltips.labels.right') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_bottom') }}" placement="bottom">
                        <x-ds::button variant="outline">{{ __('ds.pages.tooltips.labels.bottom') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_left') }}" placement="left">
                        <x-ds::button variant="outline">{{ __('ds.pages.tooltips.labels.left') }}</x-ds::button>
                    </x-ds::tooltip>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.tooltips.sections.triggers')" :description="__('ds.pages.tooltips.sections.triggers_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_hover') }}" trigger="hover" placement="top">
                        <x-ds::badge>{{ __('ds.pages.tooltips.labels.hover') }}</x-ds::badge>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_focus') }}" trigger="focus" placement="top">
                        <x-ds::button variant="outline">{{ __('ds.pages.tooltips.labels.focus') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_click') }}" trigger="click" placement="top">
                        <x-ds::button>{{ __('ds.pages.tooltips.labels.click') }}</x-ds::button>
                    </x-ds::tooltip>

                    <x-ds::toast
                        variant="info"
                        style="soft"
                        :duration="0"
                        :dismissible="false"
                        :title="__('ds.pages.tooltips.labels.toast_title')"
                        icon="solar:info-circle-linear"
                    >
                        {{ __('ds.pages.tooltips.labels.toast_body') }}
                    </x-ds::toast>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.tooltips.sections.composition')" :description="__('ds.pages.tooltips.sections.composition_description')">
                <div class="mt-4 space-y-4">
                    <x-ds::alert variant="info" style="soft" :title="__('ds.pages.tooltips.labels.alert_title')" icon="solar:info-circle-linear" :dismissible="false">
                        <div class="flex flex-wrap items-center gap-2">
                            <span>{{ __('ds.pages.tooltips.labels.alert_body') }}</span>
                            <x-ds::tooltip placement="top" trigger="hover" variant="dark" text="{{ __('ds.pages.tooltips.labels.tip_help') }}">
                                <button type="button" class="inline-flex items-center text-(--ds-primary) hover:underline">
                                    {{ __('ds.pages.tooltips.labels.help') }}
                                </button>
                            </x-ds::tooltip>
                        </div>
                    </x-ds::alert>

                    <div class="flex items-center gap-3">
                        <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_loading') }}" placement="right" variant="light" size="sm">
                            <div class="inline-flex items-center gap-2 rounded-lg border border-(--ds-border) bg-(--ds-surface-2) px-3 py-2">
                                <x-ds::spinner size="sm" variant="info" />
                                <span class="text-sm">{{ __('ds.pages.tooltips.labels.loading') }}</span>
                            </div>
                        </x-ds::tooltip>
                    </div>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.tooltips.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.tooltips.docs.usage_title')" :description="__('ds.pages.tooltips.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tooltips.docs.example_title') }}</div>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <x-ds::tooltip text="{{ __('ds.pages.tooltips.labels.tip_primary') }}" variant="primary">
                                <x-ds::button icon="solar:info-circle-linear">{{ __('ds.pages.tooltips.labels.button') }}</x-ds::button>
                            </x-ds::tooltip>

                            <x-ds::tooltip placement="bottom" trigger="click" variant="dark">
                                <x-slot:content>
                                    <div class="flex items-center gap-2">
                                        <x-ds::badge style="soft" variant="info">{{ __('ds.pages.tooltips.labels.badge') }}</x-ds::badge>
                                        <span>{{ __('ds.pages.tooltips.labels.rich') }}</span>
                                    </div>
                                </x-slot:content>
                                <x-ds::button variant="outline">{{ __('ds.pages.tooltips.labels.rich_tooltip') }}</x-ds::button>
                            </x-ds::tooltip>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tooltips.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::tooltip text="Tooltip text" placement="top" trigger="hover" variant="dark">
    <x-ds::button variant="outline">Hover me</x-ds::button>
</x-ds::tooltip>

<x-ds::tooltip placement="bottom" trigger="click" variant="dark">
    <x-slot:content>
        <div class="flex items-center gap-2">
            <x-ds::badge style="soft" variant="info">Info</x-ds::badge>
            <span>Rich content</span>
        </div>
    </x-slot:content>
    <x-ds::button>Click me</x-ds::button>
</x-ds::tooltip>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tooltips.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">text</span> — {{ __('ds.pages.tooltips.docs.props.text') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">placement</span> — {{ __('ds.pages.tooltips.docs.props.placement') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">trigger</span> — {{ __('ds.pages.tooltips.docs.props.trigger') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.tooltips.docs.props.variant') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">size</span> — {{ __('ds.pages.tooltips.docs.props.size') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">content</span> — {{ __('ds.pages.tooltips.docs.props.content') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tooltips.docs.accessibility_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div>{{ __('ds.pages.tooltips.docs.accessibility.body') }}</div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
