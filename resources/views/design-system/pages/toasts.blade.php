@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.toasts.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.toasts.subtitle') }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.toasts.sections.variants')" :description="__('ds.pages.toasts.sections.variants_description')">
                <div class="mt-4 flex flex-col gap-4">
                    <x-ds::toast variant="primary" title="{{ __('ds.pages.toasts.labels.primary') }}" icon="mingcute:emoji-line">
                        {{ __('ds.pages.toasts.messages.primary') }}
                    </x-ds::toast>

                    <x-ds::toast variant="success" title="{{ __('ds.pages.toasts.labels.success') }}" icon="akar-icons:double-check">
                        {{ __('ds.pages.toasts.messages.success') }}
                    </x-ds::toast>

                    <x-ds::toast variant="warning" title="{{ __('ds.pages.toasts.labels.warning') }}" icon="mdi:alert-circle-outline">
                        {{ __('ds.pages.toasts.messages.warning') }}
                    </x-ds::toast>

                    <x-ds::toast variant="danger" title="{{ __('ds.pages.toasts.labels.danger') }}" icon="mingcute:delete-2-line">
                        {{ __('ds.pages.toasts.messages.danger') }}
                    </x-ds::toast>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.toasts.sections.styles')" :description="__('ds.pages.toasts.sections.styles_description')">
                <div class="mt-4 flex flex-col gap-4">
                    <x-ds::toast style="soft" variant="info" title="{{ __('ds.pages.toasts.labels.soft') }}" icon="solar:info-circle-linear">
                        {{ __('ds.pages.toasts.messages.info') }}
                    </x-ds::toast>

                    <x-ds::toast style="outline" variant="info" title="{{ __('ds.pages.toasts.labels.outline') }}" icon="solar:link-linear">
                        {{ __('ds.pages.toasts.messages.info') }}
                    </x-ds::toast>

                    <x-ds::toast style="solid" variant="info" title="{{ __('ds.pages.toasts.labels.solid') }}" icon="solar:bolt-linear">
                        {{ __('ds.pages.toasts.messages.info') }}
                    </x-ds::toast>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.toasts.sections.actions')" :description="__('ds.pages.toasts.sections.actions_description')">
                <div class="mt-4 flex flex-col gap-4">
                    <x-ds::toast variant="warning" :duration="0" :title="__('ds.pages.toasts.actions.toast_title')" icon="mdi:alert-circle-outline">
                        {{ __('ds.pages.toasts.actions.toast_body') }}
                        <x-slot:actions>
                            <x-ds::card-actions>
                                <x-ds::badge style="soft" variant="warning">{{ __('ds.pages.toasts.actions.badge') }}</x-ds::badge>
                                <x-ds::button size="sm" variant="outline">{{ __('ds.pages.toasts.actions.cta') }}</x-ds::button>
                            </x-ds::card-actions>
                        </x-slot:actions>
                    </x-ds::toast>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.toasts.sections.stack')" :description="__('ds.pages.toasts.sections.stack_description')">
                <div class="mt-4 flex flex-col gap-3">
                    <x-ds::toast variant="success" :dismissible="false" :duration="0" title="{{ __('ds.pages.toasts.stack.one_title') }}" icon="akar-icons:double-check">
                        {{ __('ds.pages.toasts.stack.one_body') }}
                    </x-ds::toast>
                    <x-ds::toast variant="info" :dismissible="false" :duration="0" title="{{ __('ds.pages.toasts.stack.two_title') }}" icon="solar:info-circle-linear">
                        {{ __('ds.pages.toasts.stack.two_body') }}
                    </x-ds::toast>
                    <x-ds::toast variant="warning" :dismissible="false" :duration="0" title="{{ __('ds.pages.toasts.stack.three_title') }}" icon="mdi:alert-circle-outline">
                        {{ __('ds.pages.toasts.stack.three_body') }}
                    </x-ds::toast>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full lg:col-span-2" :title="__('ds.pages.toasts.sections.trigger')" :description="__('ds.pages.toasts.sections.trigger_description')">
                <div class="mt-4" x-data="{ show: false }">
                    <x-ds::button
                        variant="primary"
                        icon="solar:bolt-linear"
                        x-on:click="show = true"
                    >
                        {{ __('ds.pages.toasts.trigger.button') }}
                    </x-ds::button>

                    <div
                        class="pointer-events-none fixed right-6 top-24 z-50 flex w-full max-w-sm flex-col gap-3"
                        x-cloak
                        x-show="show"
                        x-transition.opacity
                    >
                        <div class="pointer-events-auto">
                            <x-ds::toast
                                variant="success"
                                style="soft"
                                :duration="3000"
                                :dismissible="true"
                                title="{{ __('ds.pages.toasts.trigger.toast_title') }}"
                                icon="akar-icons:double-check"
                                x-data="{ open: true, _t: null }"
                                x-show="open"
                                x-init="_t = setTimeout(() => { open = false; show = false }, 3000)"
                                x-on:click.outside="open = false; show = false"
                            >
                                {{ __('ds.pages.toasts.trigger.toast_body') }}
                            </x-ds::toast>
                        </div>
                    </div>

                    <div class="mt-4 text-xs text-(--ds-muted-foreground)">
                        {{ __('ds.pages.toasts.trigger.hint') }}
                    </div>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.toasts.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.toasts.docs.usage_title')" :description="__('ds.pages.toasts.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.toasts.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::card :title="__('ds.pages.toasts.docs.example.card_title')" :description="__('ds.pages.toasts.docs.example.card_description')">
                                <div class="mt-4 flex flex-col gap-4">
                                    <x-ds::toast
                                        variant="info"
                                        :title="__('ds.pages.toasts.docs.example.toast_title')"
                                        icon="solar:info-circle-linear"
                                        :duration="0"
                                    >
                                        {{ __('ds.pages.toasts.docs.example.toast_body') }}
                                        <x-slot:actions>
                                            <x-ds::card-actions>
                                                <x-ds::badge style="soft" variant="info">{{ __('ds.pages.toasts.docs.example.badge') }}</x-ds::badge>
                                                <x-ds::button size="sm" variant="outline">{{ __('ds.pages.toasts.docs.example.action') }}</x-ds::button>
                                            </x-ds::card-actions>
                                        </x-slot:actions>
                                    </x-ds::toast>
                                </div>
                            </x-ds::card>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.toasts.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::toast
    variant="info"
    title="Toast title"
    icon="solar:info-circle-linear"
    :duration="0"
>
    Toast body

    <x-slot:actions>
        <x-ds::card-actions>
            <x-ds::badge style="soft" variant="info">Badge</x-ds::badge>
            <x-ds::button size="sm" variant="outline">Action</x-ds::button>
        </x-ds::card-actions>
    </x-slot:actions>
</x-ds::toast>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.toasts.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.toasts.docs.props.variant') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">style</span> — {{ __('ds.pages.toasts.docs.props.style') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">title</span> — {{ __('ds.pages.toasts.docs.props.title') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">icon</span> — {{ __('ds.pages.toasts.docs.props.icon') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">dismissible</span> — {{ __('ds.pages.toasts.docs.props.dismissible') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">duration</span> — {{ __('ds.pages.toasts.docs.props.duration') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.toasts.docs.variants_title') }}</div>
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
