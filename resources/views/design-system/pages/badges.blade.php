@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.badges.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.badges.subtitle') }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card class="h-full" :title="__('ds.pages.badges.sections.default')" :description="__('ds.pages.badges.sections.default_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::badge>{{ __('ds.pages.badges.labels.primary') }}</x-ds::badge>
                    <x-ds::badge variant="secondary">{{ __('ds.pages.badges.labels.secondary') }}</x-ds::badge>
                    <x-ds::badge variant="success">{{ __('ds.pages.badges.labels.success') }}</x-ds::badge>
                    <x-ds::badge variant="info">{{ __('ds.pages.badges.labels.info') }}</x-ds::badge>
                    <x-ds::badge variant="warning">{{ __('ds.pages.badges.labels.warning') }}</x-ds::badge>
                    <x-ds::badge variant="danger">{{ __('ds.pages.badges.labels.danger') }}</x-ds::badge>
                    <x-ds::badge variant="dark">{{ __('ds.pages.badges.labels.dark') }}</x-ds::badge>
                    <x-ds::badge variant="link">{{ __('ds.pages.badges.labels.link') }}</x-ds::badge>
                    <x-ds::badge variant="light">{{ __('ds.pages.badges.labels.light') }}</x-ds::badge>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.badges.sections.outline')" :description="__('ds.pages.badges.sections.outline_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::badge style="outline">{{ __('ds.pages.badges.labels.primary') }}</x-ds::badge>
                    <x-ds::badge style="outline" variant="secondary">{{ __('ds.pages.badges.labels.secondary') }}</x-ds::badge>
                    <x-ds::badge style="outline" variant="success">{{ __('ds.pages.badges.labels.success') }}</x-ds::badge>
                    <x-ds::badge style="outline" variant="info">{{ __('ds.pages.badges.labels.info') }}</x-ds::badge>
                    <x-ds::badge style="outline" variant="warning">{{ __('ds.pages.badges.labels.warning') }}</x-ds::badge>
                    <x-ds::badge style="outline" variant="danger">{{ __('ds.pages.badges.labels.danger') }}</x-ds::badge>
                    <x-ds::badge style="outline" variant="dark">{{ __('ds.pages.badges.labels.dark') }}</x-ds::badge>
                    <x-ds::badge style="outline" variant="light">{{ __('ds.pages.badges.labels.light') }}</x-ds::badge>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.badges.sections.soft')" :description="__('ds.pages.badges.sections.soft_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::badge style="soft">{{ __('ds.pages.badges.labels.primary') }}</x-ds::badge>
                    <x-ds::badge style="soft" variant="secondary">{{ __('ds.pages.badges.labels.secondary') }}</x-ds::badge>
                    <x-ds::badge style="soft" variant="success">{{ __('ds.pages.badges.labels.success') }}</x-ds::badge>
                    <x-ds::badge style="soft" variant="info">{{ __('ds.pages.badges.labels.info') }}</x-ds::badge>
                    <x-ds::badge style="soft" variant="warning">{{ __('ds.pages.badges.labels.warning') }}</x-ds::badge>
                    <x-ds::badge style="soft" variant="danger">{{ __('ds.pages.badges.labels.danger') }}</x-ds::badge>
                    <x-ds::badge style="soft" variant="dark">{{ __('ds.pages.badges.labels.dark') }}</x-ds::badge>
                    <x-ds::badge style="soft" variant="light">{{ __('ds.pages.badges.labels.light') }}</x-ds::badge>
                    <x-ds::badge style="soft" variant="link">{{ __('ds.pages.badges.labels.link') }}</x-ds::badge>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.badges.sections.pill')" :description="__('ds.pages.badges.sections.pill_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::badge :pill="true">{{ __('ds.pages.badges.labels.primary') }}</x-ds::badge>
                    <x-ds::badge :pill="true" variant="secondary">{{ __('ds.pages.badges.labels.secondary') }}</x-ds::badge>
                    <x-ds::badge :pill="true" variant="success">{{ __('ds.pages.badges.labels.success') }}</x-ds::badge>
                    <x-ds::badge :pill="true" variant="warning">{{ __('ds.pages.badges.labels.warning') }}</x-ds::badge>
                    <x-ds::badge :pill="true" variant="danger">{{ __('ds.pages.badges.labels.danger') }}</x-ds::badge>
                    <x-ds::badge :pill="true" variant="light">{{ __('ds.pages.badges.labels.light') }}</x-ds::badge>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.badges.sections.with_icon')" :description="__('ds.pages.badges.sections.with_icon_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::badge icon="solar:bolt-linear">{{ __('ds.pages.badges.labels.primary') }}</x-ds::badge>
                    <x-ds::badge variant="success" icon="solar:check-circle-linear">{{ __('ds.pages.badges.labels.success') }}</x-ds::badge>
                    <x-ds::badge variant="warning" icon="solar:bell-linear">{{ __('ds.pages.badges.labels.warning') }}</x-ds::badge>
                    <x-ds::badge variant="danger" icon="solar:danger-triangle-linear">{{ __('ds.pages.badges.labels.danger') }}</x-ds::badge>
                    <x-ds::badge variant="secondary" icon="solar:tag-linear" iconPosition="right">{{ __('ds.pages.badges.labels.secondary') }}</x-ds::badge>
                </div>
            </x-ds::card>

            <x-ds::card class="h-full" :title="__('ds.pages.badges.sections.dots')" :description="__('ds.pages.badges.sections.dots_description')">
                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <x-ds::badge variant="primary" :dot="true" style="soft">{{ __('ds.pages.badges.labels.primary') }}</x-ds::badge>
                    <x-ds::badge variant="secondary" :dot="true" style="soft">{{ __('ds.pages.badges.labels.secondary') }}</x-ds::badge>
                    <x-ds::badge variant="success" :dot="true" style="soft">{{ __('ds.pages.badges.labels.success') }}</x-ds::badge>
                    <x-ds::badge variant="info" :dot="true" style="soft">{{ __('ds.pages.badges.labels.info') }}</x-ds::badge>
                    <x-ds::badge variant="warning" :dot="true" style="soft">{{ __('ds.pages.badges.labels.warning') }}</x-ds::badge>
                    <x-ds::badge variant="danger" :dot="true" style="soft">{{ __('ds.pages.badges.labels.danger') }}</x-ds::badge>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.badges.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.badges.docs.usage_title')" :description="__('ds.pages.badges.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.badges.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::card :title="__('ds.pages.badges.docs.example.card_title')" :description="__('ds.pages.badges.docs.example.card_description')">
                                <x-ds::card-actions class="mt-4">
                                    <x-ds::badge style="soft" variant="info" icon="solar:info-circle-linear">{{ __('ds.pages.badges.docs.example.badge_1') }}</x-ds::badge>
                                    <x-ds::badge style="outline" variant="warning" :pill="true">{{ __('ds.pages.badges.docs.example.badge_2') }}</x-ds::badge>
                                    <x-ds::badge style="soft" variant="success" :dot="true">{{ __('ds.pages.badges.docs.example.badge_3') }}</x-ds::badge>
                                </x-ds::card-actions>

                                <div class="mt-6">
                                    <x-ds::button variant="outline">{{ __('ds.pages.badges.docs.example.action') }}</x-ds::button>
                                </div>
                            </x-ds::card>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.badges.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::badge style="soft" variant="info" icon="solar:info-circle-linear">
    Badge
</x-ds::badge>

<x-ds::badge style="outline" variant="warning" :pill="true">
    Badge
</x-ds::badge>

<x-ds::badge style="soft" variant="success" :dot="true">
    Badge
</x-ds::badge>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.badges.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.badges.docs.props.variant') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">style</span> — {{ __('ds.pages.badges.docs.props.style') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">size</span> — {{ __('ds.pages.badges.docs.props.size') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">pill</span> — {{ __('ds.pages.badges.docs.props.pill') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">icon</span> — {{ __('ds.pages.badges.docs.props.icon') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">iconPosition</span> — {{ __('ds.pages.badges.docs.props.iconPosition') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">dot</span> — {{ __('ds.pages.badges.docs.props.dot') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.badges.docs.variants_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">primary</span>, <span class="font-semibold text-(--ds-foreground)">secondary</span>, <span class="font-semibold text-(--ds-foreground)">success</span>, <span class="font-semibold text-(--ds-foreground)">info</span></div>
                            <div><span class="font-semibold text-(--ds-foreground)">warning</span>, <span class="font-semibold text-(--ds-foreground)">danger</span>, <span class="font-semibold text-(--ds-foreground)">dark</span>, <span class="font-semibold text-(--ds-foreground)">light</span>, <span class="font-semibold text-(--ds-foreground)">link</span></div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
