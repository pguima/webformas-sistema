@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.modals.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.modals.subtitle') }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.modals.sections.basic')" :description="__('ds.pages.modals.sections.basic_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::modal>
                        <x-slot:trigger>
                            <x-ds::button icon="solar:window-frame-linear">{{ __('ds.pages.modals.labels.open_basic') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.modals.labels.basic_title') }}</x-slot:title>
                        <x-slot:description>{{ __('ds.pages.modals.labels.basic_description') }}</x-slot:description>

                        <div class="space-y-3 text-sm text-(--ds-muted-foreground)">
                            <div>{{ __('ds.pages.modals.labels.basic_body') }}</div>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-ds::tag variant="info" style="soft" :pill="true">{{ __('ds.pages.modals.labels.tag') }}</x-ds::tag>
                                <x-ds::badge style="soft" variant="info">{{ __('ds.pages.modals.labels.badge') }}</x-ds::badge>
                                <x-ds::spinner size="sm" variant="info" />
                            </div>
                        </div>

                        <x-slot:footer>
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <x-ds::button variant="outline">{{ __('ds.pages.modals.labels.cancel') }}</x-ds::button>
                                <x-ds::button>{{ __('ds.pages.modals.labels.confirm') }}</x-ds::button>
                            </div>
                        </x-slot:footer>
                    </x-ds::modal>

                    <x-ds::modal size="lg">
                        <x-slot:trigger>
                            <x-ds::button variant="outline" icon="solar:maximize-linear">{{ __('ds.pages.modals.labels.open_large') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.modals.labels.large_title') }}</x-slot:title>

                        <div class="space-y-3 text-sm text-(--ds-muted-foreground)">
                            <div>{{ __('ds.pages.modals.labels.large_body') }}</div>
                            <x-ds::tabs
                                variant="pill"
                                :tabs="[
                                    ['label' => __('ds.pages.modals.labels.tab_one'), 'content' => __('ds.pages.modals.labels.tab_one_body')],
                                    ['label' => __('ds.pages.modals.labels.tab_two'), 'content' => __('ds.pages.modals.labels.tab_two_body'), 'badge' => '2'],
                                ]"
                            />
                        </div>

                        <x-slot:footer>
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <x-ds::button variant="outline">{{ __('ds.pages.modals.labels.close') }}</x-ds::button>
                                <x-ds::button icon="solar:check-circle-linear">{{ __('ds.pages.modals.labels.save') }}</x-ds::button>
                            </div>
                        </x-slot:footer>
                    </x-ds::modal>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.modals.sections.dismiss')" :description="__('ds.pages.modals.sections.dismiss_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::modal :dismissible="false" :closeOnBackdrop="false" :closeOnEsc="false">
                        <x-slot:trigger>
                            <x-ds::button variant="danger" icon="solar:lock-outline">{{ __('ds.pages.modals.labels.open_locked') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.modals.labels.locked_title') }}</x-slot:title>
                        <x-slot:description>{{ __('ds.pages.modals.labels.locked_description') }}</x-slot:description>

                        <x-ds::alert variant="warning" style="soft" :title="__('ds.pages.modals.labels.locked_alert_title')" icon="solar:shield-warning-linear" :dismissible="false">
                            {{ __('ds.pages.modals.labels.locked_alert_body') }}
                        </x-ds::alert>

                        <x-slot:footer>
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <x-ds::button variant="outline">{{ __('ds.pages.modals.labels.ok') }}</x-ds::button>
                            </div>
                        </x-slot:footer>
                    </x-ds::modal>

                    <x-ds::modal size="sm">
                        <x-slot:trigger>
                            <x-ds::button icon="solar:trash-bin-trash-linear">{{ __('ds.pages.modals.labels.open_confirm') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.modals.labels.confirm_title') }}</x-slot:title>

                        <div class="text-sm text-(--ds-muted-foreground)">
                            {{ __('ds.pages.modals.labels.confirm_body') }}
                        </div>

                        <x-slot:footer>
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <x-ds::button variant="outline">{{ __('ds.pages.modals.labels.cancel') }}</x-ds::button>
                                <x-ds::button variant="danger">{{ __('ds.pages.modals.labels.delete') }}</x-ds::button>
                            </div>
                        </x-slot:footer>
                    </x-ds::modal>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full lg:col-span-2" :title="__('ds.pages.modals.sections.composition')" :description="__('ds.pages.modals.sections.composition_description')">
                <div class="mt-4 space-y-4">
                    <x-ds::alert variant="info" style="soft" :title="__('ds.pages.modals.labels.alert_title')" icon="solar:info-circle-linear" :dismissible="false">
                        <div class="flex flex-wrap items-center gap-2">
                            <span>{{ __('ds.pages.modals.labels.alert_body') }}</span>
                            <x-ds::tooltip text="{{ __('ds.pages.modals.labels.tip_help') }}" placement="top" variant="dark">
                                <button type="button" class="inline-flex items-center text-(--ds-primary) hover:underline">
                                    {{ __('ds.pages.modals.labels.help') }}
                                </button>
                            </x-ds::tooltip>
                        </div>
                    </x-ds::alert>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-ds::modal>
                            <x-slot:trigger>
                                <x-ds::button variant="secondary">{{ __('ds.pages.modals.labels.open_nested') }}</x-ds::button>
                            </x-slot:trigger>

                            <x-slot:title>{{ __('ds.pages.modals.labels.nested_title') }}</x-slot:title>

                            <div class="space-y-4">
                                <x-ds::accordion
                                    :items="[
                                        ['title' => __('ds.pages.modals.labels.acc_one'), 'content' => __('ds.pages.modals.labels.acc_one_body')],
                                        ['title' => __('ds.pages.modals.labels.acc_two'), 'content' => __('ds.pages.modals.labels.acc_two_body')],
                                    ]"
                                    :defaultOpen="0"
                                />

                                <div class="flex flex-wrap items-center gap-2">
                                    <x-ds::tag variant="primary" style="soft" :removable="true">{{ __('ds.pages.modals.labels.tag') }}</x-ds::tag>
                                    <x-ds::tag variant="warning" style="outline" :dot="true">{{ __('ds.pages.modals.labels.tag') }}</x-ds::tag>
                                </div>
                            </div>

                            <x-slot:footer>
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <x-ds::button variant="outline">{{ __('ds.pages.modals.labels.close') }}</x-ds::button>
                                    <x-ds::button :loading="true">{{ __('ds.pages.modals.labels.loading') }}</x-ds::button>
                                </div>
                            </x-slot:footer>
                        </x-ds::modal>

                        <x-ds::toast
                            variant="info"
                            style="soft"
                            :duration="0"
                            :dismissible="false"
                            :title="__('ds.pages.modals.labels.toast_title')"
                            icon="solar:info-circle-linear"
                        >
                            {{ __('ds.pages.modals.labels.toast_body') }}
                        </x-ds::toast>
                    </div>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.modals.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.modals.docs.usage_title')" :description="__('ds.pages.modals.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.modals.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::modal size="sm">
                                <x-slot:trigger>
                                    <x-ds::button variant="outline">{{ __('ds.pages.modals.labels.open_basic') }}</x-ds::button>
                                </x-slot:trigger>

                                <x-slot:title>{{ __('ds.pages.modals.labels.basic_title') }}</x-slot:title>
                                <div class="text-sm text-(--ds-muted-foreground)">{{ __('ds.pages.modals.labels.basic_body') }}</div>

                                <x-slot:footer>
                                    <div class="flex items-center justify-end gap-2">
                                        <x-ds::button variant="outline">{{ __('ds.pages.modals.labels.cancel') }}</x-ds::button>
                                        <x-ds::button>{{ __('ds.pages.modals.labels.confirm') }}</x-ds::button>
                                    </div>
                                </x-slot:footer>
                            </x-ds::modal>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.modals.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::modal size="md">
    <x-slot:trigger>
        <x-ds::button>Open modal</x-ds::button>
    </x-slot:trigger>

    <x-slot:title>Modal title</x-slot:title>

    Modal content

    <x-slot:footer>
        <x-ds::button variant="outline">Cancel</x-ds::button>
        <x-ds::button>Confirm</x-ds::button>
    </x-slot:footer>
</x-ds::modal>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.modals.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">open</span> — {{ __('ds.pages.modals.docs.props.open') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">size</span> — {{ __('ds.pages.modals.docs.props.size') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">dismissible</span> — {{ __('ds.pages.modals.docs.props.dismissible') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">closeOnEsc</span> — {{ __('ds.pages.modals.docs.props.close_on_esc') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">closeOnBackdrop</span> — {{ __('ds.pages.modals.docs.props.close_on_backdrop') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.modals.docs.accessibility_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div>{{ __('ds.pages.modals.docs.accessibility.body') }}</div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
