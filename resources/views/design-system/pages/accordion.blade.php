@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.accordion.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.accordion.subtitle') }}
                </div>
            </div>
        </div>

        @php
            $itemsBasic = [
                [
                    'title' => __('ds.pages.accordion.items.q1.title'),
                    'content' => __('ds.pages.accordion.items.q1.content'),
                    'badge' => __('ds.pages.accordion.items.q1.badge'),
                    'icon' => 'solar:question-circle-linear',
                ],
                [
                    'title' => __('ds.pages.accordion.items.q2.title'),
                    'content' => __('ds.pages.accordion.items.q2.content'),
                    'badge' => __('ds.pages.accordion.items.q2.badge'),
                    'icon' => 'solar:shield-check-linear',
                ],
                [
                    'title' => __('ds.pages.accordion.items.q3.title'),
                    'content' => __('ds.pages.accordion.items.q3.content'),
                    'badge' => __('ds.pages.accordion.items.q3.badge'),
                    'icon' => 'solar:bill-list-linear',
                ],
            ];
        @endphp

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.accordion.sections.single')" :description="__('ds.pages.accordion.sections.single_description')">
                <div class="mt-4">
                    <x-ds::accordion :items="$itemsBasic" :defaultOpen="0" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.accordion.sections.multiple')" :description="__('ds.pages.accordion.sections.multiple_description')">
                <div class="mt-4">
                    <x-ds::accordion :items="$itemsBasic" :multiple="true" :defaultOpen="[0, 2]" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.accordion.sections.sizes')" :description="__('ds.pages.accordion.sections.sizes_description')">
                <div class="mt-4 space-y-4">
                    <x-ds::accordion :items="$itemsBasic" size="sm" :defaultOpen="0" />
                    <x-ds::accordion :items="$itemsBasic" size="md" :defaultOpen="0" />
                    <x-ds::accordion :items="$itemsBasic" size="lg" :defaultOpen="0" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.accordion.sections.composition')" :description="__('ds.pages.accordion.sections.composition_description')">
                <div class="mt-4 space-y-4">
                    <x-ds::alert variant="info" style="soft" :title="__('ds.pages.accordion.labels.alert_title')" icon="solar:info-circle-linear" :dismissible="false">
                        <div class="flex flex-wrap items-center gap-2">
                            <span>{{ __('ds.pages.accordion.labels.alert_body') }}</span>
                            <x-ds::tooltip text="{{ __('ds.pages.accordion.labels.tip_help') }}" placement="top" variant="dark">
                                <button type="button" class="inline-flex items-center text-(--ds-primary) hover:underline">
                                    {{ __('ds.pages.accordion.labels.help') }}
                                </button>
                            </x-ds::tooltip>
                        </div>
                    </x-ds::alert>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-ds::button icon="solar:refresh-linear" :loading="true">{{ __('ds.pages.accordion.labels.loading') }}</x-ds::button>
                        <x-ds::badge style="soft" variant="info">{{ __('ds.pages.accordion.labels.badge') }}</x-ds::badge>
                    </div>

                    <x-ds::toast
                        variant="info"
                        style="soft"
                        :duration="0"
                        :dismissible="false"
                        :title="__('ds.pages.accordion.labels.toast_title')"
                        icon="solar:info-circle-linear"
                    >
                        {{ __('ds.pages.accordion.labels.toast_body') }}
                    </x-ds::toast>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.accordion.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.accordion.docs.usage_title')" :description="__('ds.pages.accordion.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.accordion.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::accordion :items="$itemsBasic" :defaultOpen="1" />
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.accordion.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::accordion
    :items="[
        ['title' => 'Question 1', 'content' => 'Answer 1'],
        ['title' => 'Question 2', 'content' => 'Answer 2'],
    ]"
    :defaultOpen="0"
/>

<x-ds::accordion
    :items="[... ]"
    :multiple="true"
    :defaultOpen="[0, 2]"
    size="lg"
/>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.accordion.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">items</span> — {{ __('ds.pages.accordion.docs.props.items') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">multiple</span> — {{ __('ds.pages.accordion.docs.props.multiple') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">defaultOpen</span> — {{ __('ds.pages.accordion.docs.props.default_open') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">size</span> — {{ __('ds.pages.accordion.docs.props.size') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.accordion.docs.props.variant') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.accordion.docs.accessibility_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div>{{ __('ds.pages.accordion.docs.accessibility.body') }}</div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
