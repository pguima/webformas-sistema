@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.tags.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.tags.subtitle') }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.tags.sections.default')" :description="__('ds.pages.tags.sections.default_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::tag>{{ __('ds.pages.tags.labels.label') }}</x-ds::tag>
                    <x-ds::tag>{{ __('ds.pages.tags.labels.label') }}</x-ds::tag>
                    <x-ds::tag>{{ __('ds.pages.tags.labels.label') }}</x-ds::tag>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <x-ds::tag :removable="true">{{ __('ds.pages.tags.labels.removable') }}</x-ds::tag>
                    <x-ds::tag :removable="true">{{ __('ds.pages.tags.labels.removable') }}</x-ds::tag>
                    <x-ds::tag :removable="true">{{ __('ds.pages.tags.labels.removable') }}</x-ds::tag>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.tags.sections.variants')" :description="__('ds.pages.tags.sections.variants_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::tag variant="primary" style="soft">{{ __('ds.pages.tags.labels.primary') }}</x-ds::tag>
                    <x-ds::tag variant="success" style="soft">{{ __('ds.pages.tags.labels.success') }}</x-ds::tag>
                    <x-ds::tag variant="info" style="soft">{{ __('ds.pages.tags.labels.info') }}</x-ds::tag>
                    <x-ds::tag variant="warning" style="soft">{{ __('ds.pages.tags.labels.warning') }}</x-ds::tag>
                    <x-ds::tag variant="danger" style="soft">{{ __('ds.pages.tags.labels.danger') }}</x-ds::tag>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <x-ds::tag variant="primary" style="outline">{{ __('ds.pages.tags.labels.outline') }}</x-ds::tag>
                    <x-ds::tag variant="primary" style="solid">{{ __('ds.pages.tags.labels.solid') }}</x-ds::tag>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.tags.sections.sizes')" :description="__('ds.pages.tags.sections.sizes_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::tag size="sm">{{ __('ds.pages.tags.labels.small') }}</x-ds::tag>
                    <x-ds::tag size="md">{{ __('ds.pages.tags.labels.medium') }}</x-ds::tag>
                    <x-ds::tag size="lg">{{ __('ds.pages.tags.labels.large') }}</x-ds::tag>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <x-ds::tag :pill="true">{{ __('ds.pages.tags.labels.pill') }}</x-ds::tag>
                    <x-ds::tag :pill="true" variant="info" style="soft">{{ __('ds.pages.tags.labels.pill') }}</x-ds::tag>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.tags.sections.with_icon')" :description="__('ds.pages.tags.sections.with_icon_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::tag icon="solar:user-outline">{{ __('ds.pages.tags.labels.with_icon') }}</x-ds::tag>
                    <x-ds::tag icon="solar:pin-outline" variant="primary" style="soft">{{ __('ds.pages.tags.labels.with_icon') }}</x-ds::tag>
                    <x-ds::tag icon="solar:calendar-outline" iconPosition="right" variant="warning" style="soft">{{ __('ds.pages.tags.labels.with_icon') }}</x-ds::tag>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <x-ds::tag :dot="true" variant="success" style="outline">{{ __('ds.pages.tags.labels.indicator') }}</x-ds::tag>
                    <x-ds::tag :dot="true" :removable="true" variant="success" style="outline">{{ __('ds.pages.tags.labels.indicator') }}</x-ds::tag>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full lg:col-span-2" :title="__('ds.pages.tags.sections.composition')" :description="__('ds.pages.tags.sections.composition_description')">
                <div class="mt-4 space-y-4">
                    <x-ds::alert variant="info" style="soft" :title="__('ds.pages.tags.labels.alert_title')" icon="solar:info-circle-linear" :dismissible="false">
                        <div class="flex flex-wrap items-center gap-2">
                            <span>{{ __('ds.pages.tags.labels.alert_body') }}</span>
                            <x-ds::tooltip text="{{ __('ds.pages.tags.labels.tip_help') }}" placement="top" variant="dark">
                                <button type="button" class="inline-flex items-center text-(--ds-primary) hover:underline">
                                    {{ __('ds.pages.tags.labels.help') }}
                                </button>
                            </x-ds::tooltip>
                        </div>
                    </x-ds::alert>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-ds::tag variant="info" style="soft" :pill="true">{{ __('ds.pages.tags.labels.active') }}</x-ds::tag>
                        <x-ds::button size="sm" variant="outline">{{ __('ds.pages.tags.labels.filter') }}</x-ds::button>
                        <x-ds::button size="sm" :loading="true">{{ __('ds.pages.tags.labels.loading') }}</x-ds::button>
                        <x-ds::spinner size="sm" variant="info" />
                        <x-ds::badge style="soft" variant="info">{{ __('ds.pages.tags.labels.badge') }}</x-ds::badge>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div class="rounded-2xl border border-(--ds-border) bg-(--ds-surface) p-5">
                            <div class="text-sm font-semibold">{{ __('ds.pages.tags.labels.tabs_title') }}</div>
                            <div class="mt-3">
                                <x-ds::tabs
                                    variant="pill"
                                    :tabs="[
                                        ['label' => __('ds.pages.tags.labels.tab_one'), 'content' => __('ds.pages.tags.labels.tab_one_body')],
                                        ['label' => __('ds.pages.tags.labels.tab_two'), 'content' => __('ds.pages.tags.labels.tab_two_body'), 'badge' => '2'],
                                    ]"
                                />
                            </div>
                        </div>

                        <div class="rounded-2xl border border-(--ds-border) bg-(--ds-surface) p-5">
                            <div class="text-sm font-semibold">{{ __('ds.pages.tags.labels.accordion_title') }}</div>
                            <div class="mt-3">
                                <x-ds::accordion
                                    :items="[
                                        ['title' => __('ds.pages.tags.labels.acc_one'), 'content' => __('ds.pages.tags.labels.acc_one_body')],
                                        ['title' => __('ds.pages.tags.labels.acc_two'), 'content' => __('ds.pages.tags.labels.acc_two_body')],
                                    ]"
                                    :defaultOpen="0"
                                />
                            </div>
                        </div>
                    </div>

                    <x-ds::toast
                        variant="info"
                        style="soft"
                        :duration="0"
                        :dismissible="false"
                        :title="__('ds.pages.tags.labels.toast_title')"
                        icon="solar:info-circle-linear"
                    >
                        {{ __('ds.pages.tags.labels.toast_body') }}
                    </x-ds::toast>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.tags.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.tags.docs.usage_title')" :description="__('ds.pages.tags.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tags.docs.example_title') }}</div>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <x-ds::tag variant="primary" style="soft" icon="solar:tag-outline" :removable="true">{{ __('ds.pages.tags.labels.label') }}</x-ds::tag>
                            <x-ds::tag variant="warning" style="outline" :dot="true">{{ __('ds.pages.tags.labels.label') }}</x-ds::tag>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tags.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::tag variant="info" style="soft" icon="solar:tag-outline" :removable="true">
    Label
</x-ds::tag>

<x-ds::tag variant="success" style="outline" :dot="true" :pill="true">
    Active
</x-ds::tag>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tags.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.tags.docs.props.variant') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">style</span> — {{ __('ds.pages.tags.docs.props.style') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">size</span> — {{ __('ds.pages.tags.docs.props.size') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">pill</span> — {{ __('ds.pages.tags.docs.props.pill') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">icon</span> — {{ __('ds.pages.tags.docs.props.icon') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">iconPosition</span> — {{ __('ds.pages.tags.docs.props.icon_position') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">dot</span> — {{ __('ds.pages.tags.docs.props.dot') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">removable</span> — {{ __('ds.pages.tags.docs.props.removable') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">disabled</span> — {{ __('ds.pages.tags.docs.props.disabled') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.tags.docs.accessibility_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div>{{ __('ds.pages.tags.docs.accessibility.body') }}</div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
