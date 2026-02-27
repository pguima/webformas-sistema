@extends('layouts.layout-ds')

@section('content')
    <div>
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-(--text-primary)">
                    {{ __('ds.pages.links.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--text-secondary)">
                    {{ __('ds.pages.links.subtitle') }}
                </div>
            </div>
            <div class="flex gap-2">
                <x-ds::button variant="ghost" icon="solar:arrow-left-linear" onclick="history.back()">Back</x-ds::button>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Variants -->
            <x-ds::card :title="__('ds.pages.links.sections.variants')"
                :description="__('ds.pages.links.sections.variants_desc')">
                <div class="mt-4 flex flex-col items-start gap-4">
                    <x-ds::link href="#">Primary Link</x-ds::link>
                    <x-ds::link href="#" variant="secondary">Secondary Link</x-ds::link>
                    <x-ds::link href="#" variant="muted">Muted Link</x-ds::link>
                    <x-ds::link href="#" variant="danger">Danger Link</x-ds::link>
                    <x-ds::link href="#" variant="ghost">Ghost (Text) Link</x-ds::link>
                </div>
            </x-ds::card>

            <!-- Sizes -->
            <x-ds::card :title="__('ds.pages.links.sections.sizes')"
                :description="__('ds.pages.links.sections.sizes_desc')">
                <div class="mt-4 flex flex-col items-start gap-4">
                    <x-ds::link href="#" size="sm">Small Link (12px)</x-ds::link>
                    <x-ds::link href="#" size="md">Default Link (14px)</x-ds::link>
                    <x-ds::link href="#" size="lg">Large Link (16px)</x-ds::link>
                </div>
            </x-ds::card>

            <!-- Icons & External -->
            <x-ds::card :title="__('ds.pages.links.sections.icons')"
                :description="__('ds.pages.links.sections.icons_desc')">
                <div class="mt-4 flex flex-col items-start gap-4">
                    <x-ds::link href="#" icon="solar:home-smile-linear">Home</x-ds::link>
                    <x-ds::link href="#" icon="solar:arrow-right-linear" iconPosition="right">Next Step</x-ds::link>
                    <x-ds::link href="https://example.com" external variant="secondary">External Link</x-ds::link>
                    <x-ds::link href="#" disabled>Disabled Link</x-ds::link>
                </div>
            </x-ds::card>

            <!-- Inline Example -->
            <x-ds::card :title="__('ds.pages.links.sections.inline')"
                :description="__('ds.pages.links.sections.inline_desc')">
                <div class="mt-4 text-sm text-(--text-secondary) leading-relaxed">
                    <p>
                        Links are essential for navigation. You can place them
                        <x-ds::link href="#" underline="always">inline within text</x-ds::link>
                        to direct users to other pages. They should be distinct from regular text
                        but not distracting.
                    </p>
                    <p class="mt-2">
                        For critical actions, consider using a
                        <x-ds::link href="#" variant="danger">danger link</x-ds::link>
                        to warn the user.
                    </p>
                </div>
            </x-ds::card>

            <!-- Documentation -->
            <div class="lg:col-span-2">
                <div class="mb-6 text-sm font-semibold text-(--text-secondary)">
                    {{ __('ds.pages.links.docs.title') }}
                </div>

                <x-ds::card class="h-full" :title="__('ds.pages.links.docs.usage_title')"
                    :description="__('ds.pages.links.docs.usage_subtitle')">
                    <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <div class="text-sm font-semibold text-(--text-primary)">
                                {{ __('ds.pages.links.docs.example_code_title') }}</div>
                            <div
                                class="mt-3 overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-hover)">
                                <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
                                    <!-- Basic -->
                                    <x-ds::link href="/dashboard">Go to Dashboard</x-ds::link>

                                    <!-- External with Icon -->
                                    <x-ds::link href="https://google.com" external>
                                        Google
                                    </x-ds::link>

                                    <!-- With Icon -->
                                    <x-ds::link icon="solar:settings-linear">
                                        Settings
                                    </x-ds::link>
                                @endverbatim</code></pre>
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-(--text-primary)">
                                {{ __('ds.pages.links.docs.props_title') }}</div>
                            <div class="mt-3 grid grid-cols-1 gap-4 text-sm text-(--text-secondary)">
                                <div><span class="font-semibold text-(--text-primary)">variant</span> — primary,
                                    secondary, muted, danger.</div>
                                <div><span class="font-semibold text-(--text-primary)">size</span> — sm, md, lg.</div>
                                <div><span class="font-semibold text-(--text-primary)">external</span> — Adds
                                    target="_blank" and arrow icon.</div>
                                <div><span class="font-semibold text-(--text-primary)">underline</span> — hover
                                    (default), always, none.</div>
                                <div><span class="font-semibold text-(--text-primary)">icon</span> — Iconify icon name.
                                </div>
                            </div>
                        </div>
                    </div>
                </x-ds::card>
            </div>
        </div>
    </div>
@endsection