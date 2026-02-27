@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.alerts.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.alerts.subtitle') }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.alerts.sections.default')" :description="__('ds.pages.alerts.sections.default_description')">
                <div class="mt-4 flex flex-col gap-4">
                    <x-ds::alert variant="primary">{{ __('ds.pages.alerts.messages.primary') }}</x-ds::alert>
                    <x-ds::alert variant="secondary">{{ __('ds.pages.alerts.messages.secondary') }}</x-ds::alert>
                    <x-ds::alert variant="warning">{{ __('ds.pages.alerts.messages.warning') }}</x-ds::alert>
                    <x-ds::alert variant="info">{{ __('ds.pages.alerts.messages.info') }}</x-ds::alert>
                    <x-ds::alert variant="danger">{{ __('ds.pages.alerts.messages.danger') }}</x-ds::alert>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.alerts.sections.outline')" :description="__('ds.pages.alerts.sections.outline_description')">
                <div class="mt-4 flex flex-col gap-4">
                    <x-ds::alert style="outline" variant="primary">{{ __('ds.pages.alerts.messages.primary') }}</x-ds::alert>
                    <x-ds::alert style="outline" variant="secondary">{{ __('ds.pages.alerts.messages.secondary') }}</x-ds::alert>
                    <x-ds::alert style="outline" variant="warning">{{ __('ds.pages.alerts.messages.warning') }}</x-ds::alert>
                    <x-ds::alert style="outline" variant="info">{{ __('ds.pages.alerts.messages.info') }}</x-ds::alert>
                    <x-ds::alert style="outline" variant="danger">{{ __('ds.pages.alerts.messages.danger') }}</x-ds::alert>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full lg:col-span-2" :title="__('ds.pages.alerts.sections.solid')" :description="__('ds.pages.alerts.sections.solid_description')">
                <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="flex flex-col gap-4">
                        <x-ds::alert style="solid" variant="primary">{{ __('ds.pages.alerts.messages.primary') }}</x-ds::alert>
                        <x-ds::alert style="solid" variant="success">{{ __('ds.pages.alerts.messages.success') }}</x-ds::alert>
                        <x-ds::alert style="solid" variant="info">{{ __('ds.pages.alerts.messages.info') }}</x-ds::alert>
                    </div>
                    <div class="flex flex-col gap-4">
                        <x-ds::alert style="solid" variant="secondary">{{ __('ds.pages.alerts.messages.secondary') }}</x-ds::alert>
                        <x-ds::alert style="solid" variant="warning">{{ __('ds.pages.alerts.messages.warning') }}</x-ds::alert>
                        <x-ds::alert style="solid" variant="danger">{{ __('ds.pages.alerts.messages.danger') }}</x-ds::alert>
                    </div>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.alerts.sections.with_icon')" :description="__('ds.pages.alerts.sections.with_icon_description')">
                <div class="mt-4 flex flex-col gap-4">
                    <x-ds::alert variant="primary" icon="mingcute:emoji-line">{{ __('ds.pages.alerts.messages.primary') }}</x-ds::alert>
                    <x-ds::alert variant="success" icon="akar-icons:double-check">{{ __('ds.pages.alerts.messages.success') }}</x-ds::alert>
                    <x-ds::alert variant="warning" icon="mdi:alert-circle-outline">{{ __('ds.pages.alerts.messages.warning') }}</x-ds::alert>
                    <x-ds::alert variant="info" icon="ci:link">{{ __('ds.pages.alerts.messages.info') }}</x-ds::alert>
                    <x-ds::alert variant="danger" icon="mingcute:delete-2-line">{{ __('ds.pages.alerts.messages.danger') }}</x-ds::alert>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.alerts.sections.left_border')" :description="__('ds.pages.alerts.sections.left_border_description')">
                <div class="mt-4 flex flex-col gap-4">
                    <x-ds::alert variant="primary" :leftBorder="true" icon="mingcute:emoji-line">{{ __('ds.pages.alerts.messages.primary') }}</x-ds::alert>
                    <x-ds::alert variant="secondary" :leftBorder="true" icon="mingcute:emoji-line">{{ __('ds.pages.alerts.messages.secondary') }}</x-ds::alert>
                    <x-ds::alert variant="success" :leftBorder="true" icon="akar-icons:double-check">{{ __('ds.pages.alerts.messages.success') }}</x-ds::alert>
                    <x-ds::alert variant="warning" :leftBorder="true" icon="mdi:alert-circle-outline">{{ __('ds.pages.alerts.messages.warning') }}</x-ds::alert>
                    <x-ds::alert variant="info" :leftBorder="true" icon="ci:link">{{ __('ds.pages.alerts.messages.info') }}</x-ds::alert>
                    <x-ds::alert variant="danger" :leftBorder="true" icon="mingcute:delete-2-line">{{ __('ds.pages.alerts.messages.danger') }}</x-ds::alert>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full lg:col-span-2" :title="__('ds.pages.alerts.sections.with_description')" :description="__('ds.pages.alerts.sections.with_description_description')">
                <div class="mt-4 flex flex-col gap-4">
                    <x-ds::alert
                        variant="primary"
                        :title="__('ds.pages.alerts.long.primary_title')"
                        icon="mingcute:emoji-line"
                    >
                        {{ __('ds.pages.alerts.long.body') }}
                        <x-slot:actions>
                            <x-ds::card-actions>
                                <x-ds::badge style="soft" variant="info">{{ __('ds.pages.alerts.long.badge') }}</x-ds::badge>
                                <x-ds::button size="sm" variant="outline">{{ __('ds.pages.alerts.long.action') }}</x-ds::button>
                            </x-ds::card-actions>
                        </x-slot:actions>
                    </x-ds::alert>

                    <x-ds::alert
                        variant="success"
                        :title="__('ds.pages.alerts.long.success_title')"
                        icon="bi:patch-check"
                    >
                        {{ __('ds.pages.alerts.long.body') }}
                    </x-ds::alert>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.alerts.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.alerts.docs.usage_title')" :description="__('ds.pages.alerts.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.alerts.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::card :title="__('ds.pages.alerts.docs.example.card_title')" :description="__('ds.pages.alerts.docs.example.card_description')">
                                <div class="mt-4 flex flex-col gap-4">
                                    <x-ds::alert
                                        variant="warning"
                                        :title="__('ds.pages.alerts.docs.example.alert_title')"
                                        icon="mdi:alert-circle-outline"
                                    >
                                        {{ __('ds.pages.alerts.docs.example.alert_body') }}
                                        <x-slot:actions>
                                            <x-ds::card-actions>
                                                <x-ds::badge style="soft" variant="warning">{{ __('ds.pages.alerts.docs.example.badge') }}</x-ds::badge>
                                                <x-ds::button size="sm" variant="outline">{{ __('ds.pages.alerts.docs.example.action') }}</x-ds::button>
                                            </x-ds::card-actions>
                                        </x-slot:actions>
                                    </x-ds::alert>
                                </div>
                            </x-ds::card>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.alerts.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::alert
    variant="warning"
    title="Alert title"
    icon="mdi:alert-circle-outline"
>
    Alert body

    <x-slot:actions>
        <x-ds::card-actions>
            <x-ds::badge style="soft" variant="warning">Badge</x-ds::badge>
            <x-ds::button size="sm" variant="outline">Action</x-ds::button>
        </x-ds::card-actions>
    </x-slot:actions>
</x-ds::alert>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.alerts.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.alerts.docs.props.variant') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">style</span> — {{ __('ds.pages.alerts.docs.props.style') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">title</span> — {{ __('ds.pages.alerts.docs.props.title') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">icon</span> — {{ __('ds.pages.alerts.docs.props.icon') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">dismissible</span> — {{ __('ds.pages.alerts.docs.props.dismissible') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">bordered</span> — {{ __('ds.pages.alerts.docs.props.bordered') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">leftBorder</span> — {{ __('ds.pages.alerts.docs.props.leftBorder') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.alerts.docs.variants_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">primary</span>, <span class="font-semibold text-(--ds-foreground)">secondary</span>, <span class="font-semibold text-(--ds-foreground)">success</span></div>
                            <div><span class="font-semibold text-(--ds-foreground)">warning</span>, <span class="font-semibold text-(--ds-foreground)">info</span>, <span class="font-semibold text-(--ds-foreground)">danger</span></div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
