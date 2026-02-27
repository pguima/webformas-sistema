@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight">
                    {{ __('ds.pages.card.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.card.subtitle') }}
                </div>
            </div>
        </div>

        <div class="mt-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <x-ds::card
                    :title="__('ds.pages.card.demo.card_title')"
                    :description="__('ds.pages.card.demo.card_text')"
                    class="h-full"
                >
                    <x-slot:media>
                        <div class="h-32 bg-linear-to-r from-(--ds-primary)/15 to-(--ds-surface-3)"></div>
                    </x-slot:media>

                    <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right">
                        {{ __('ds.pages.card.actions.read_more') }}
                    </x-ds::link>
                </x-ds::card>

                <x-ds::card
                    :title="__('ds.pages.card.demo.card_title')"
                    :description="__('ds.pages.card.demo.card_text')"
                    align="center"
                    class="h-full"
                >
                    <x-slot:media>
                        <div class="h-32 bg-linear-to-r from-(--ds-success)/15 to-(--ds-surface-3)"></div>
                    </x-slot:media>

                    <button type="button" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-(--ds-primary) px-3 py-2 text-sm font-semibold text-white">
                        {{ __('ds.pages.card.actions.read_more') }}
                        <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                    </button>
                </x-ds::card>

                <x-ds::card
                    :title="__('ds.pages.card.demo.card_title')"
                    :description="__('ds.pages.card.demo.card_text')"
                    align="right"
                    class="h-full"
                >
                    <x-slot:media>
                        <div class="h-32 bg-linear-to-r from-(--ds-warning)/15 to-(--ds-surface-3)"></div>
                    </x-slot:media>

                    <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-(--ds-primary)/10 px-3 py-2 text-sm font-semibold text-(--ds-primary)">
                            {{ __('ds.pages.card.actions.read_more') }}
                            <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                        </button>
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-(--ds-warning)/15 px-3 py-2 text-sm font-semibold text-(--ds-warning)">
                            {{ __('ds.pages.card.actions.bookmark') }}
                            <iconify-icon icon="bx:bookmark-minus" class="text-xl"></iconify-icon>
                        </button>
                    </div>
                </x-ds::card>

                <x-ds::card
                    :title="__('ds.pages.card.demo.card_title')"
                    :description="__('ds.pages.card.demo.card_text')"
                    align="center"
                    class="h-full"
                >
                    <x-slot:media>
                        <div class="h-32 bg-linear-to-r from-(--ds-info)/15 to-(--ds-surface-3)"></div>
                    </x-slot:media>

                    <button type="button" class="mt-4 inline-flex items-center justify-center rounded-lg bg-(--ds-primary) p-2 text-white">
                        <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                    </button>
                </x-ds::card>
            </div>
        </div>

        <div class="mt-10">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.card.sections.text_icon') }}
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <x-ds::card
                    :title="__('ds.pages.card.text_icon.brand_identity')"
                    :description="__('ds.pages.card.text_icon.description')"
                    class="h-full"
                >
                    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-[#a855f7]/15 text-[#a855f7]">
                        <iconify-icon icon="solar:medal-ribbons-star-bold" class="text-2xl"></iconify-icon>
                    </div>
                    <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right" variant="ghost" class="text-[#a855f7] hover:text-[#a855f7]/80">
                        {{ __('ds.pages.card.actions.read_more') }}
                    </x-ds::link>
                </x-ds::card>

                <x-ds::card
                    :title="__('ds.pages.card.text_icon.uiux')"
                    :description="__('ds.pages.card.text_icon.description')"
                    align="center"
                    class="h-full"
                >
                    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-(--ds-primary)/15 text-(--ds-primary)">
                        <iconify-icon icon="ri:computer-fill" class="text-2xl"></iconify-icon>
                    </div>
                    <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right">
                        {{ __('ds.pages.card.actions.read_more') }}
                    </x-ds::link>
                </x-ds::card>

                <x-ds::card
                    :title="__('ds.pages.card.text_icon.strategy')"
                    :description="__('ds.pages.card.text_icon.description')"
                    align="right"
                    class="h-full"
                >
                    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-(--ds-success)/15 text-(--ds-success)">
                        <iconify-icon icon="fluent:toolbox-20-filled" class="text-2xl"></iconify-icon>
                    </div>
                    <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right" variant="ghost" class="text-(--ds-success) hover:text-(--ds-success)/80">
                        {{ __('ds.pages.card.actions.read_more') }}
                    </x-ds::link>
                </x-ds::card>

                <x-ds::card
                    :title="__('ds.pages.card.text_icon.development')"
                    :description="__('ds.pages.card.text_icon.description')"
                    align="center"
                    class="h-full"
                >
                    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-(--ds-danger)/15 text-(--ds-danger)">
                        <iconify-icon icon="ph:code-fill" class="text-2xl"></iconify-icon>
                    </div>
                    <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right" variant="ghost" class="text-(--ds-danger) hover:text-(--ds-danger)/80">
                        {{ __('ds.pages.card.actions.read_more') }}
                    </x-ds::link>
                </x-ds::card>
            </div>
        </div>

        <div class="mt-10">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.card.sections.overlay') }}
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-6 xl:grid-cols-3">
                <x-ds::card
                    class="relative h-full"
                    :title="null"
                    :description="null"
                    :padded="false"
                    :bordered="false"
                >
                    <x-slot:media>
                        <div class="h-56 bg-linear-to-br from-(--ds-primary)/30 to-(--ds-primary)/5"></div>
                        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/25 to-transparent"></div>
                    </x-slot:media>

                    <div class="absolute bottom-0 left-0 p-6">
                        <div class="text-lg font-semibold text-white">{{ __('ds.pages.card.demo.card_title') }}</div>
                        <div class="mt-2 text-sm text-white/90">{{ __('ds.pages.card.overlay.text') }}</div>
                        <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right">
                            {{ __('ds.pages.card.actions.read_more') }}
                        </x-ds::link>
                    </div>
                </x-ds::card>

                <x-ds::card
                    class="relative h-full"
                    :title="null"
                    :description="null"
                    :padded="false"
                    :bordered="false"
                >
                    <x-slot:media>
                        <div class="h-56 bg-linear-to-br from-(--ds-success)/30 to-(--ds-success)/5"></div>
                        <div class="absolute inset-0 bg-linear-to-b from-black/70 via-black/25 to-transparent"></div>
                    </x-slot:media>

                    <div class="absolute top-0 left-0 w-full p-6 text-center">
                        <div class="text-lg font-semibold text-white">{{ __('ds.pages.card.demo.card_title') }}</div>
                        <div class="mt-2 text-sm text-white/90">{{ __('ds.pages.card.overlay.text') }}</div>
                        <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right">
                            {{ __('ds.pages.card.actions.read_more') }}
                        </x-ds::link>
                    </div>
                </x-ds::card>

                <x-ds::card
                    class="relative h-full"
                    :title="null"
                    :description="null"
                    :padded="false"
                    :bordered="false"
                >
                    <x-slot:media>
                        <div class="h-56 bg-linear-to-br from-(--ds-warning)/30 to-(--ds-warning)/5"></div>
                        <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/25 to-transparent"></div>
                    </x-slot:media>

                    <div class="absolute bottom-0 left-0 w-full p-6 text-right">
                        <div class="text-lg font-semibold text-white">{{ __('ds.pages.card.demo.card_title') }}</div>
                        <div class="mt-2 text-sm text-white/90">{{ __('ds.pages.card.overlay.text') }}</div>
                        <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right">
                            {{ __('ds.pages.card.actions.read_more') }}
                        </x-ds::link>
                    </div>
                </x-ds::card>
            </div>
        </div>

        <div class="mt-10">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.card.sections.header_footer') }}
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <x-ds::card
                    :title="__('ds.pages.card.header_footer.body_title')"
                    :description="__('ds.pages.card.header_footer.body_text')"
                    :padded="false"
                    class="h-full"
                >
                    <x-slot:header>
                        <div class="flex items-center justify-between gap-2 px-6 py-4">
                            <div class="text-sm font-semibold">{{ __('ds.pages.card.header_footer.greeting') }}</div>
                            <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-(--ds-surface-2)">
                                <iconify-icon icon="mdi:times" class="text-xl"></iconify-icon>
                            </button>
                        </div>
                    </x-slot:header>

                    <div class="p-6">
                        <div class="text-base font-semibold">{{ __('ds.pages.card.header_footer.body_title') }}</div>
                        <div class="mt-2 text-sm text-(--ds-muted-foreground)">{{ __('ds.pages.card.header_footer.body_text') }}</div>
                    </div>

                    <x-slot:footer>
                        <div class="px-6 py-4 text-center">
                            <x-ds::link href="javascript:void(0)">{{ __('ds.pages.card.actions.view_all') }}</x-ds::link>
                        </div>
                    </x-slot:footer>
                </x-ds::card>

                <x-ds::card class="h-full" :title="null" :description="null">
                    <div class="flex items-center gap-3">
                        <iconify-icon icon="typcn:user-add" class="text-2xl"></iconify-icon>
                        <div class="text-sm font-semibold">{{ __('ds.pages.card.header_footer.body_title') }}</div>
                    </div>
                    <div class="mt-3 text-sm text-(--ds-muted-foreground)">{{ __('ds.pages.card.header_footer.body_text') }}</div>
                    <div class="mt-3 text-sm text-(--ds-muted-foreground)">{{ __('ds.pages.card.header_footer.body_text_2') }}</div>
                    <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right">
                        {{ __('ds.pages.card.actions.read_more') }}
                    </x-ds::link>
                </x-ds::card>

                <x-ds::card class="h-full" :title="null" :description="null" :padded="false">
                    <x-slot:header>
                        <div class="flex items-center justify-between gap-2 px-6 py-4">
                            <div>
                                <div class="text-sm font-semibold">{{ __('ds.pages.card.header_footer.support_title') }}</div>
                                <div class="text-xs text-(--ds-muted-foreground)">{{ __('ds.pages.card.header_footer.support_subtitle') }}</div>
                            </div>
                            <x-ds::link href="javascript:void(0)">{{ __('ds.pages.card.actions.view_all') }}</x-ds::link>
                        </div>
                    </x-slot:header>

                    <div class="px-6 py-4">
                        <div class="text-sm text-(--ds-muted-foreground)">{{ __('ds.pages.card.header_footer.body_text') }}</div>
                        <div class="mt-3 text-sm text-(--ds-muted-foreground)">{{ __('ds.pages.card.header_footer.body_text_2') }}</div>
                    </div>
                </x-ds::card>
            </div>
        </div>

        <div class="mt-10">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.card.sections.horizontal') }}
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <x-ds::card
                    :title="__('ds.pages.card.demo.card_title')"
                    :description="__('ds.pages.card.demo.card_text')"
                    class="h-full sm:flex"
                >
                    <x-slot:media>
                        <div class="h-40 w-full bg-linear-to-br from-(--ds-primary)/25 to-(--ds-primary)/5 sm:h-auto sm:w-44"></div>
                    </x-slot:media>

                    <x-ds::link href="javascript:void(0)" icon="iconamoon:arrow-right-2" iconPosition="right">
                        {{ __('ds.pages.card.actions.read_more') }}
                    </x-ds::link>
                </x-ds::card>

                <x-ds::card
                    :title="__('ds.pages.card.demo.card_title')"
                    :description="__('ds.pages.card.demo.card_text')"
                    class="h-full sm:flex"
                >
                    <x-slot:media>
                        <div class="order-1 h-40 w-full bg-linear-to-br from-(--ds-success)/25 to-(--ds-success)/5 sm:order-0 sm:h-auto sm:w-44"></div>
                    </x-slot:media>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-(--ds-primary) px-3 py-2 text-sm font-semibold text-white">
                            {{ __('ds.pages.card.actions.read_more') }}
                            <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                        </button>
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-(--ds-surface-2) px-3 py-2 text-sm font-semibold">
                            {{ __('ds.pages.card.actions.secondary') }}
                        </button>
                    </div>
                </x-ds::card>
            </div>
        </div>

        <div class="mt-12">
            <div class="mb-6 text-sm font-semibold text-(--ds-muted-foreground)">
                {{ __('ds.pages.card.docs.title') }}
            </div>

            <x-ds::card class="h-full" :title="__('ds.pages.card.docs.usage_title')" :description="__('ds.pages.card.docs.usage_subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.card.docs.example_title') }}</div>
                        <div class="mt-3">
                            <x-ds::card
                                :title="__('ds.pages.card.docs.example.card_title')"
                                :description="__('ds.pages.card.docs.example.card_description')"
                            >
                                <x-ds::card-actions class="mt-4">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-(--ds-primary) px-3 py-2 text-sm font-semibold text-white">
                                        {{ __('ds.pages.card.docs.example.primary_action') }}
                                        <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
                                    </button>
                                    <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-(--ds-surface-2) px-3 py-2 text-sm font-semibold">
                                        {{ __('ds.pages.card.docs.example.secondary_action') }}
                                    </button>
                                </x-ds::card-actions>
                            </x-ds::card>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.card.docs.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::card
    :title="__('ds.pages.card.docs.example.card_title')"
    :description="__('ds.pages.card.docs.example.card_description')"
>
    <x-ds::card-actions class="mt-4">
        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-(--ds-primary) px-3 py-2 text-sm font-semibold text-white">
            {{ __('ds.pages.card.docs.example.primary_action') }}
            <iconify-icon icon="iconamoon:arrow-right-2" class="text-xl"></iconify-icon>
        </button>
        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-(--ds-surface-2) px-3 py-2 text-sm font-semibold">
            {{ __('ds.pages.card.docs.example.secondary_action') }}
        </button>
    </x-ds::card-actions>
</x-ds::card>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.card.docs.props_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">title</span> — {{ __('ds.pages.card.docs.props.title') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">description</span> — {{ __('ds.pages.card.docs.props.description') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">align</span> — {{ __('ds.pages.card.docs.props.align') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">variant</span> — {{ __('ds.pages.card.docs.props.variant') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">padded</span> — {{ __('ds.pages.card.docs.props.padded') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">shadow</span> — {{ __('ds.pages.card.docs.props.shadow') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">bordered</span> — {{ __('ds.pages.card.docs.props.bordered') }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold">{{ __('ds.pages.card.docs.slots_title') }}</div>
                        <div class="mt-3 space-y-2 text-sm text-(--ds-muted-foreground)">
                            <div><span class="font-semibold text-(--ds-foreground)">media</span> — {{ __('ds.pages.card.docs.slots.media') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">header</span> — {{ __('ds.pages.card.docs.slots.header') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">footer</span> — {{ __('ds.pages.card.docs.slots.footer') }}</div>
                            <div><span class="font-semibold text-(--ds-foreground)">slot</span> — {{ __('ds.pages.card.docs.slots.default') }}</div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
