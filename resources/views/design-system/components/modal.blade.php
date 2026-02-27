 @props([
    'title' => null,
    'description' => null,
    'open' => false,
    'size' => 'md',
    'variant' => 'default',
    'dismissible' => true,
    'closeOnEsc' => true,
    'closeOnBackdrop' => true,
])

@php
    $modalId = 'ds-modal-' . uniqid();

    $panelSizeClass = match ($size) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        'full' => 'max-w-6xl',
        default => 'max-w-xl',
    };

    $titleId = $modalId . '-title';
@endphp

<div
    x-data="{
        open: @js((bool) $open),
        openModal() {
            this.open = true;
            document.body.classList.add('overflow-hidden');
            this.$nextTick(() => { this.$refs.panel?.focus(); });
        },
        closeModal() {
            this.open = false;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    x-on:keydown.escape.window="@js($closeOnEsc) ? closeModal() : null"
    {{ $attributes->except(['class']) }}
>
    @isset($trigger)
        <span class="inline-flex" x-on:click="openModal()">
            {{ $trigger }}
        </span>
    @endisset

    <div
        x-cloak
        x-show="open"
        class="fixed inset-0 z-50"
        aria-labelledby="{{ $titleId }}"
        role="dialog"
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0 z-0 bg-black/50 backdrop-blur-sm"
            x-on:click="@js($closeOnBackdrop) ? closeModal() : null"
        ></div>

        <!-- Modal container -->
        <div class="absolute inset-0 z-10 overflow-y-auto p-4">
            <div class="flex min-h-full items-center justify-center">
                <!-- Modal panel -->
                <div
                    x-ref="panel"
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    tabindex="-1"
                    x-on:click.stop
                    class="relative w-full {{ $panelSizeClass }} overflow-hidden rounded-xl border border-(--border-default) bg-(--surface-card) shadow-(--shadow-xl)"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between gap-3 px-6 py-4 border-b border-(--border-subtle)">
                        <div class="min-w-0">
                            @isset($title)
                                <div id="{{ $titleId }}" class="text-base font-semibold text-(--text-primary)">
                                    {{ $title }}
                                </div>
                            @endisset

                            @isset($description)
                                <div class="mt-1 text-sm text-(--text-secondary)">
                                    {{ $description }}
                                </div>
                            @endisset
                        </div>

                        @if ($dismissible)
                            <button
                                type="button"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-md text-(--text-secondary) transition-colors duration-150 hover:bg-(--surface-hover) hover:text-(--text-primary)"
                                x-on:click="closeModal()"
                                aria-label="{{ __('ds.pages.modals.labels.close') }}"
                            >
                                <iconify-icon icon="iconamoon:sign-times-light" class="text-xl"></iconify-icon>
                            </button>
                        @endif
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5 text-(--text-primary)">
                        {{ $slot }}
                    </div>

                    <!-- Footer -->
                    @isset($footer)
                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-(--border-subtle) bg-(--surface-page)">
                            {{ $footer }}
                        </div>
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>
