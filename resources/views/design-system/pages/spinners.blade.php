@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.spinners.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.spinners.subtitle') }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.spinners.sections.sizes')" :description="__('ds.pages.spinners.sections.sizes_description')">
                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <x-ds::spinner size="xs" />
                    <x-ds::spinner size="sm" />
                    <x-ds::spinner size="md" />
                    <x-ds::spinner size="lg" />
                    <x-ds::spinner size="xl" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.spinners.sections.variants')" :description="__('ds.pages.spinners.sections.variants_description')">
                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <x-ds::spinner variant="primary" />
                    <x-ds::spinner variant="secondary" />
                    <x-ds::spinner variant="success" />
                    <x-ds::spinner variant="info" />
                    <x-ds::spinner variant="warning" />
                    <x-ds::spinner variant="danger" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.spinners.sections.thickness')" :description="__('ds.pages.spinners.sections.thickness_description')">
                <div class="mt-4 flex flex-wrap items-center gap-4">
                    <x-ds::spinner :thickness="1" />
                    <x-ds::spinner :thickness="2" />
                    <x-ds::spinner :thickness="3" />
                    <x-ds::spinner :thickness="4" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.spinners.sections.with_label')" :description="__('ds.pages.spinners.sections.with_label_description')">
                <div class="mt-4 flex flex-col gap-4">
                    <div>
                        <x-ds::spinner variant="info" label="{{ __('ds.pages.spinners.labels.loading') }}" />
                    </div>

                    <x-ds::alert variant="info" :title="__('ds.pages.spinners.labels.alert_title')" icon="solar:info-circle-linear" :dismissible="false">
                        {{ __('ds.pages.spinners.labels.alert_body') }}
                        <x-slot:actions>
                            <x-ds::card-actions>
                                <x-ds::badge style="soft" variant="info">{{ __('ds.pages.spinners.labels.badge') }}</x-ds::badge>
                                <x-ds::button size="sm" variant="outline">{{ __('ds.pages.spinners.labels.action') }}</x-ds::button>
                            </x-ds::card-actions>
                        </x-slot:actions>
                    </x-ds::alert>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full lg:col-span-2" :title="__('ds.pages.spinners.sections.button')" :description="__('ds.pages.spinners.sections.button_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::button :loading="true">{{ __('ds.pages.spinners.labels.button_loading') }}</x-ds::button>
                    <x-ds::button variant="outline" :loading="true">{{ __('ds.pages.spinners.labels.button_loading') }}</x-ds::button>
                    <x-ds::button variant="secondary" :loading="true">{{ __('ds.pages.spinners.labels.button_loading') }}</x-ds::button>
                </div>

                <div class="mt-6">
                    <x-ds::toast
                        variant="info"
                        style="soft"
                        :duration="0"
                        :dismissible="false"
                        :title="__('ds.pages.spinners.labels.toast_title')"
                        icon="solar:info-circle-linear"
                    >
                        {{ __('ds.pages.spinners.labels.toast_body') }}
                    </x-ds::toast>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.spinners.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.spinners.docs.usage_title')" :description="__('ds.pages.spinners.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.spinners.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::card :title="__('ds.pages.spinners.docs.example.card_title')" :description="__('ds.pages.spinners.docs.example.card_description')">
                                <div class="mt-4 flex flex-wrap items-center gap-4">
                                    <x-ds::spinner variant="primary" label="{{ __('ds.pages.spinners.labels.loading') }}" />
                                    <x-ds::spinner variant="success" size="lg" />
                                    <x-ds::spinner variant="warning" :thickness="3" />
                                </div>
                            </x-ds::card>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.spinners.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::spinner size="md" variant="primary" :thickness="2" />

<x-ds::spinner size="md" variant="info" label="Loading" />
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.spinners.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">size</span> — {{ __('ds.pages.spinners.docs.props.size') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.spinners.docs.props.variant') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">thickness</span> — {{ __('ds.pages.spinners.docs.props.thickness') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">label</span> — {{ __('ds.pages.spinners.docs.props.label') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.spinners.docs.sizes_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">xs</span>, <span class="font-semibold text-(--ds-foreground)">sm</span>, <span class="font-semibold text-(--ds-foreground)">md</span></div>
                            <div><span class="font-semibold text-(--ds-foreground)">lg</span>, <span class="font-semibold text-(--ds-foreground)">xl</span></div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
