@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.button.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.button.subtitle') }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.button.sections.variants')" :description="__('ds.pages.button.sections.variants_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::button>{{ __('ds.pages.button.labels.primary') }}</x-ds::button>
                    <x-ds::button variant="secondary">{{ __('ds.pages.button.labels.secondary') }}</x-ds::button>
                    <x-ds::button variant="outline">{{ __('ds.pages.button.labels.outline') }}</x-ds::button>
                    <x-ds::button variant="ghost">{{ __('ds.pages.button.labels.ghost') }}</x-ds::button>
                    <x-ds::button variant="link" href="javascript:void(0)">{{ __('ds.pages.button.labels.link') }}</x-ds::button>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <x-ds::button variant="success">{{ __('ds.pages.button.labels.success') }}</x-ds::button>
                    <x-ds::button variant="warning">{{ __('ds.pages.button.labels.warning') }}</x-ds::button>
                    <x-ds::button variant="danger">{{ __('ds.pages.button.labels.danger') }}</x-ds::button>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.button.sections.sizes')" :description="__('ds.pages.button.sections.sizes_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::button size="sm">{{ __('ds.pages.button.labels.small') }}</x-ds::button>
                    <x-ds::button size="md">{{ __('ds.pages.button.labels.medium') }}</x-ds::button>
                    <x-ds::button size="lg">{{ __('ds.pages.button.labels.large') }}</x-ds::button>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <x-ds::button icon="solar:download-linear">{{ __('ds.pages.button.labels.with_icon') }}</x-ds::button>
                    <x-ds::button variant="secondary" size="icon" icon="mdi:heart-outline" />
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.button.sections.states')" :description="__('ds.pages.button.sections.states_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::button :disabled="true">{{ __('ds.pages.button.labels.disabled') }}</x-ds::button>

                    <x-ds::button :loading="true">{{ __('ds.pages.button.labels.loading') }}</x-ds::button>

                    <x-ds::button variant="outline">{{ __('ds.pages.button.labels.focus') }}</x-ds::button>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.button.sections.groups')" :description="__('ds.pages.button.sections.groups_description')">
                <div class="mt-4 inline-flex overflow-hidden rounded-lg border border-(--ds-border)">
                    <x-ds::button variant="ghost" class="rounded-none border-none px-4 py-2">{{ __('ds.pages.button.labels.left') }}</x-ds::button>
                    <x-ds::button variant="ghost" class="rounded-none border-none border-l border-(--ds-border) px-4 py-2">{{ __('ds.pages.button.labels.middle') }}</x-ds::button>
                    <x-ds::button variant="ghost" class="rounded-none border-none border-l border-(--ds-border) px-4 py-2">{{ __('ds.pages.button.labels.right') }}</x-ds::button>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <x-ds::button variant="outline" icon="tabler:chevron-left">{{ __('ds.pages.button.labels.previous') }}</x-ds::button>
                    <x-ds::button icon="tabler:chevron-right" iconPosition="right">{{ __('ds.pages.button.labels.next') }}</x-ds::button>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.button.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.button.docs.usage_title')" :description="__('ds.pages.button.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.button.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::card :title="__('ds.pages.button.docs.example.card_title')" :description="__('ds.pages.button.docs.example.card_description')">
                                <x-ds::card-actions class="mt-4">
                                    <x-ds::button icon="solar:bolt-linear">{{ __('ds.pages.button.docs.example.primary_action') }}</x-ds::button>
                                    <x-ds::button variant="outline">{{ __('ds.pages.button.docs.example.secondary_action') }}</x-ds::button>
                                </x-ds::card-actions>
                            </x-ds::card>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.button.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::button icon="solar:bolt-linear">
    Button
</x-ds::button>

<x-ds::button variant="outline">
    Button
</x-ds::button>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.button.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.button.docs.props.variant') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">size</span> — {{ __('ds.pages.button.docs.props.size') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">href</span> — {{ __('ds.pages.button.docs.props.href') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">type</span> — {{ __('ds.pages.button.docs.props.type') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">icon</span> — {{ __('ds.pages.button.docs.props.icon') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">iconPosition</span> — {{ __('ds.pages.button.docs.props.iconPosition') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">loading</span> — {{ __('ds.pages.button.docs.props.loading') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">disabled</span> — {{ __('ds.pages.button.docs.props.disabled') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">fullWidth</span> — {{ __('ds.pages.button.docs.props.fullWidth') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.button.docs.variants_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">primary</span>, <span class="font-semibold text-(--ds-foreground)">secondary</span>, <span class="font-semibold text-(--ds-foreground)">outline</span>, <span class="font-semibold text-(--ds-foreground)">ghost</span>, <span class="font-semibold text-(--ds-foreground)">link</span></div>
                            <div><span class="font-semibold text-(--ds-foreground)">success</span>, <span class="font-semibold text-(--ds-foreground)">warning</span>, <span class="font-semibold text-(--ds-foreground)">danger</span></div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
