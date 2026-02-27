@extends('layouts.layout-ds')

@section('content')
    <div>
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-(--text-primary)">
                    {{ __('ds.pages.offcanvas.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--text-secondary)">
                    {{ __('ds.pages.offcanvas.subtitle') }}
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Position variants -->
            <x-ds::card class="h-full" :title="__('ds.pages.offcanvas.sections.positions')"
                :description="__('ds.pages.offcanvas.sections.positions_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::offcanvas position="right">
                        <x-slot:trigger>
                            <x-ds::button
                                icon="solar:arrow-left-linear">{{ __('ds.pages.offcanvas.labels.open_right') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.right_title') }}</x-slot:title>
                        <x-slot:description>{{ __('ds.pages.offcanvas.labels.right_description') }}</x-slot:description>

                        <div class="space-y-4 text-sm text-(--text-secondary)">
                            <p>{{ __('ds.pages.offcanvas.labels.content_body') }}</p>
                            <x-ds::alert variant="info" style="soft" icon="solar:info-circle-linear" :dismissible="false">
                                {{ __('ds.pages.offcanvas.labels.alert_info') }}
                            </x-ds::alert>
                        </div>

                        <x-slot:footer>
                            <div class="flex items-center justify-end gap-2">
                                <x-ds::button variant="outline"
                                    x-on:click="open = false">{{ __('ds.pages.offcanvas.labels.cancel') }}</x-ds::button>
                                <x-ds::button>{{ __('ds.pages.offcanvas.labels.save') }}</x-ds::button>
                            </div>
                        </x-slot:footer>
                    </x-ds::offcanvas>

                    <x-ds::offcanvas position="left">
                        <x-slot:trigger>
                            <x-ds::button variant="outline"
                                icon="solar:arrow-right-linear">{{ __('ds.pages.offcanvas.labels.open_left') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.left_title') }}</x-slot:title>

                        <div class="space-y-4 text-sm text-(--text-secondary)">
                            <p>{{ __('ds.pages.offcanvas.labels.content_body') }}</p>
                            <div class="flex flex-wrap gap-2">
                                <x-ds::tag variant="purple">{{ __('ds.pages.offcanvas.labels.tag') }}</x-ds::tag>
                                <x-ds::tag variant="blue">{{ __('ds.pages.offcanvas.labels.tag') }}</x-ds::tag>
                                <x-ds::tag variant="green">{{ __('ds.pages.offcanvas.labels.tag') }}</x-ds::tag>
                            </div>
                        </div>

                        <x-slot:footer>
                            <x-ds::button variant="outline" :fullWidth="true"
                                x-on:click="open = false">{{ __('ds.pages.offcanvas.labels.close') }}</x-ds::button>
                        </x-slot:footer>
                    </x-ds::offcanvas>

                    <x-ds::offcanvas position="top" size="sm">
                        <x-slot:trigger>
                            <x-ds::button variant="secondary"
                                icon="solar:arrow-down-linear">{{ __('ds.pages.offcanvas.labels.open_top') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.top_title') }}</x-slot:title>

                        <div class="text-sm text-(--text-secondary)">
                            {{ __('ds.pages.offcanvas.labels.content_body') }}
                        </div>
                    </x-ds::offcanvas>

                    <x-ds::offcanvas position="bottom" size="sm">
                        <x-slot:trigger>
                            <x-ds::button variant="ghost"
                                icon="solar:arrow-up-linear">{{ __('ds.pages.offcanvas.labels.open_bottom') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.bottom_title') }}</x-slot:title>

                        <div class="text-sm text-(--text-secondary)">
                            {{ __('ds.pages.offcanvas.labels.content_body') }}
                        </div>
                    </x-ds::offcanvas>
                </div>
            </x-ds::card>

            <!-- Sizes -->
            <x-ds::card class="h-full" :title="__('ds.pages.offcanvas.sections.sizes')"
                :description="__('ds.pages.offcanvas.sections.sizes_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::offcanvas size="sm">
                        <x-slot:trigger>
                            <x-ds::button variant="outline"
                                size="sm">{{ __('ds.pages.offcanvas.labels.size_sm') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.small_title') }}</x-slot:title>
                        <x-slot:description>{{ __('ds.pages.offcanvas.labels.size_sm_desc') }}</x-slot:description>

                        <div class="text-sm text-(--text-secondary)">
                            {{ __('ds.pages.offcanvas.labels.content_body') }}
                        </div>
                    </x-ds::offcanvas>

                    <x-ds::offcanvas size="md">
                        <x-slot:trigger>
                            <x-ds::button variant="outline">{{ __('ds.pages.offcanvas.labels.size_md') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.medium_title') }}</x-slot:title>
                        <x-slot:description>{{ __('ds.pages.offcanvas.labels.size_md_desc') }}</x-slot:description>

                        <div class="text-sm text-(--text-secondary)">
                            {{ __('ds.pages.offcanvas.labels.content_body') }}
                        </div>
                    </x-ds::offcanvas>

                    <x-ds::offcanvas size="lg">
                        <x-slot:trigger>
                            <x-ds::button variant="outline"
                                size="lg">{{ __('ds.pages.offcanvas.labels.size_lg') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.large_title') }}</x-slot:title>
                        <x-slot:description>{{ __('ds.pages.offcanvas.labels.size_lg_desc') }}</x-slot:description>

                        <div class="text-sm text-(--text-secondary)">
                            {{ __('ds.pages.offcanvas.labels.content_body') }}
                        </div>
                    </x-ds::offcanvas>

                    <x-ds::offcanvas size="xl">
                        <x-slot:trigger>
                            <x-ds::button
                                icon="solar:maximize-linear">{{ __('ds.pages.offcanvas.labels.size_xl') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.xlarge_title') }}</x-slot:title>
                        <x-slot:description>{{ __('ds.pages.offcanvas.labels.size_xl_desc') }}</x-slot:description>

                        <div class="space-y-4 text-sm text-(--text-secondary)">
                            <p>{{ __('ds.pages.offcanvas.labels.content_body') }}</p>
                            <x-ds::tabs variant="pill" :tabs="[
            ['label' => __('ds.pages.offcanvas.labels.tab_one'), 'content' => __('ds.pages.offcanvas.labels.tab_one_body')],
            ['label' => __('ds.pages.offcanvas.labels.tab_two'), 'content' => __('ds.pages.offcanvas.labels.tab_two_body')],
        ]" />
                        </div>
                    </x-ds::offcanvas>
                </div>
            </x-ds::card>

            <!-- Dismiss behavior -->
            <x-ds::card class="h-full" :title="__('ds.pages.offcanvas.sections.dismiss')"
                :description="__('ds.pages.offcanvas.sections.dismiss_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::offcanvas :dismissible="false" :closeOnBackdrop="false" :closeOnEsc="false">
                        <x-slot:trigger>
                            <x-ds::button variant="danger"
                                icon="solar:lock-outline">{{ __('ds.pages.offcanvas.labels.open_locked') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.locked_title') }}</x-slot:title>
                        <x-slot:description>{{ __('ds.pages.offcanvas.labels.locked_description') }}</x-slot:description>

                        <div class="space-y-4">
                            <x-ds::alert variant="warning" style="soft"
                                :title="__('ds.pages.offcanvas.labels.locked_alert_title')"
                                icon="solar:shield-warning-linear" :dismissible="false">
                                {{ __('ds.pages.offcanvas.labels.locked_alert_body') }}
                            </x-ds::alert>
                        </div>

                        <x-slot:footer>
                            <x-ds::button :fullWidth="true"
                                x-on:click="open = false">{{ __('ds.pages.offcanvas.labels.confirm_action') }}</x-ds::button>
                        </x-slot:footer>
                    </x-ds::offcanvas>

                    <x-ds::offcanvas :backdrop="false">
                        <x-slot:trigger>
                            <x-ds::button variant="outline"
                                icon="solar:eye-outline">{{ __('ds.pages.offcanvas.labels.open_no_backdrop') }}</x-ds::button>
                        </x-slot:trigger>

                        <x-slot:title>{{ __('ds.pages.offcanvas.labels.no_backdrop_title') }}</x-slot:title>
                        <x-slot:description>{{ __('ds.pages.offcanvas.labels.no_backdrop_description') }}</x-slot:description>

                        <div class="text-sm text-(--text-secondary)">
                            {{ __('ds.pages.offcanvas.labels.no_backdrop_body') }}
                        </div>

                        <x-slot:footer>
                            <x-ds::button variant="outline" :fullWidth="true"
                                x-on:click="open = false">{{ __('ds.pages.offcanvas.labels.close') }}</x-ds::button>
                        </x-slot:footer>
                    </x-ds::offcanvas>
                </div>
            </x-ds::card>

            <!-- Composition -->
            <x-ds::card class="h-full" :title="__('ds.pages.offcanvas.sections.composition')"
                :description="__('ds.pages.offcanvas.sections.composition_description')">
                <div class="mt-4 space-y-4">
                    <x-ds::alert variant="info" style="soft" :title="__('ds.pages.offcanvas.labels.comp_alert_title')"
                        icon="solar:info-circle-linear" :dismissible="false">
                        <div class="flex flex-wrap items-center gap-2">
                            <span>{{ __('ds.pages.offcanvas.labels.comp_alert_body') }}</span>
                            <x-ds::tooltip text="{{ __('ds.pages.offcanvas.labels.tip_help') }}" placement="top"
                                variant="dark">
                                <button type="button"
                                    class="inline-flex items-center text-(--color-primary) hover:underline">
                                    {{ __('ds.pages.offcanvas.labels.help') }}
                                </button>
                            </x-ds::tooltip>
                        </div>
                    </x-ds::alert>

                    <div class="flex flex-wrap items-center gap-3">
                        <x-ds::offcanvas size="lg">
                            <x-slot:trigger>
                                <x-ds::button
                                    icon="solar:settings-outline">{{ __('ds.pages.offcanvas.labels.open_settings') }}</x-ds::button>
                            </x-slot:trigger>

                            <x-slot:title>{{ __('ds.pages.offcanvas.labels.settings_title') }}</x-slot:title>
                            <x-slot:description>{{ __('ds.pages.offcanvas.labels.settings_description') }}</x-slot:description>

                            <div class="space-y-6">
                                <x-ds::accordion :items="[
            ['title' => __('ds.pages.offcanvas.labels.acc_one'), 'content' => __('ds.pages.offcanvas.labels.acc_one_body'), 'icon' => 'solar:user-outline'],
            ['title' => __('ds.pages.offcanvas.labels.acc_two'), 'content' => __('ds.pages.offcanvas.labels.acc_two_body'), 'icon' => 'solar:bell-outline'],
            ['title' => __('ds.pages.offcanvas.labels.acc_three'), 'content' => __('ds.pages.offcanvas.labels.acc_three_body'), 'icon' => 'solar:lock-outline'],
        ]"
                                    :defaultOpen="0" />

                                <div class="flex flex-wrap items-center gap-2">
                                    <x-ds::tag variant="purple" style="soft"
                                        :removable="true">{{ __('ds.pages.offcanvas.labels.tag_feature') }}</x-ds::tag>
                                    <x-ds::tag variant="green" style="soft"
                                        :removable="true">{{ __('ds.pages.offcanvas.labels.tag_active') }}</x-ds::tag>
                                    <x-ds::badge variant="purple"
                                        size="sm">{{ __('ds.pages.offcanvas.labels.badge_new') }}</x-ds::badge>
                                </div>
                            </div>

                            <x-slot:footer>
                                <div class="flex items-center justify-between gap-3">
                                    <x-ds::button variant="ghost"
                                        icon="solar:restart-outline">{{ __('ds.pages.offcanvas.labels.reset') }}</x-ds::button>
                                    <div class="flex items-center gap-2">
                                        <x-ds::button variant="outline"
                                            x-on:click="open = false">{{ __('ds.pages.offcanvas.labels.cancel') }}</x-ds::button>
                                        <x-ds::button
                                            icon="solar:check-circle-linear">{{ __('ds.pages.offcanvas.labels.apply') }}</x-ds::button>
                                    </div>
                                </div>
                            </x-slot:footer>
                        </x-ds::offcanvas>

                        <x-ds::toast variant="info" style="soft" :duration="0" :dismissible="false"
                            :title="__('ds.pages.offcanvas.labels.toast_title')" icon="solar:info-circle-linear">
                            {{ __('ds.pages.offcanvas.labels.toast_body') }}
                        </x-ds::toast>
                    </div>
                </div>
            </x-ds::card>
        </div>

        <!-- Documentation -->
        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--text-secondary)">
                {{ __('ds.pages.offcanvas.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.offcanvas.docs.usage_title')"
                :description="__('ds.pages.offcanvas.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold text-(--text-primary)">
                            {{ __('ds.pages.offcanvas.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::offcanvas size="sm">
                                <x-slot:trigger>
                                    <x-ds::button
                                        variant="outline">{{ __('ds.pages.offcanvas.labels.open_right') }}</x-ds::button>
                                </x-slot:trigger>

                                <x-slot:title>{{ __('ds.pages.offcanvas.labels.right_title') }}</x-slot:title>
                                <div class="text-sm text-(--text-secondary)">
                                    {{ __('ds.pages.offcanvas.labels.content_body') }}</div>

                                <x-slot:footer>
                                    <div class="flex items-center justify-end gap-2">
                                        <x-ds::button variant="outline"
                                            x-on:click="open = false">{{ __('ds.pages.offcanvas.labels.cancel') }}</x-ds::button>
                                        <x-ds::button>{{ __('ds.pages.offcanvas.labels.confirm') }}</x-ds::button>
                                    </div>
                                </x-slot:footer>
                            </x-ds::offcanvas>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-(--text-primary)">
                            {{ __('ds.pages.offcanvas.docs.example_code_title') }}</div>
                        <div
                            class="mt-3 overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-hover)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
                                <x-ds::offcanvas position="right" size="md">
                                    <x-slot:trigger>
                                        <x-ds::button>Open offcanvas</x-ds::button>
                                    </x-slot:trigger>

                                    <x-slot:title>Panel title</x-slot:title>
                                    <x-slot:description>Optional description</x-slot:description>

                                    Panel content goes here.

                                    <x-slot:footer>
                                        <x-ds::button variant="outline">Cancel</x-ds::button>
                                        <x-ds::button>Confirm</x-ds::button>
                                    </x-slot:footer>
                                </x-ds::offcanvas>
                            @endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold text-(--text-primary)">
                            {{ __('ds.pages.offcanvas.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--text-secondary)">
                            <div><span class="font-semibold text-(--text-primary)">open</span> —
                                {{ __('ds.pages.offcanvas.docs.props.open') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">position</span> —
                                {{ __('ds.pages.offcanvas.docs.props.position') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">size</span> —
                                {{ __('ds.pages.offcanvas.docs.props.size') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">dismissible</span> —
                                {{ __('ds.pages.offcanvas.docs.props.dismissible') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">closeOnEsc</span> —
                                {{ __('ds.pages.offcanvas.docs.props.close_on_esc') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">closeOnBackdrop</span> —
                                {{ __('ds.pages.offcanvas.docs.props.close_on_backdrop') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">backdrop</span> —
                                {{ __('ds.pages.offcanvas.docs.props.backdrop') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-(--text-primary)">
                            {{ __('ds.pages.offcanvas.docs.accessibility_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--text-secondary)">
                            <div>{{ __('ds.pages.offcanvas.docs.accessibility.body') }}</div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection