@props([
     'title' => null,
     'description' => null,
    'open' => false,
    'position' => 'right',
    'size' => 'md',
    'dismissible' => true,
    'closeOnBackdrop' => true,
    'closeOnEsc' => true,
    'backdrop' => true,
])

@php
    $offcanvasId = 'ds-offcanvas-' . uniqid();

    $sizeClass = match ($size) {
        'sm' => match ($position) {
            'left', 'right' => 'w-72',
            default => 'h-48',
        },
        'lg' => match ($position) {
            'left', 'right' => 'w-[480px]',
            default => 'h-96',
        },
        'xl' => match ($position) {
            'left', 'right' => 'w-[640px]',
            default => 'h-[480px]',
        },
        'full' => match ($position) {
            'left', 'right' => 'w-full max-w-full',
            default => 'h-full max-h-full',
        },
        default => match ($position) {
            'left', 'right' => 'w-80',
            default => 'h-64',
        },
    };

    $positionClass = match ($position) {
        'left' => 'inset-y-0 left-0',
        'top' => 'inset-x-0 top-0',
        'bottom' => 'inset-x-0 bottom-0',
        default => 'inset-y-0 right-0',
    };

    $translateHiddenClass = match ($position) {
        'left' => '-translate-x-full',
        'top' => '-translate-y-full',
        'bottom' => 'translate-y-full',
        default => 'translate-x-full',
    };

    $translateVisibleClass = match ($position) {
        'left' => 'translate-x-0',
        'top' => 'translate-y-0',
        'bottom' => 'translate-y-0',
        default => 'translate-x-0',
    };

    $isHorizontal = in_array($position, ['left', 'right']);

    $panelBase = implode(' ', [
        'fixed z-[60] flex flex-col bg-(--surface-card) shadow-(--shadow-xl)',
        $positionClass,
        $sizeClass,
        $isHorizontal ? 'h-full' : 'w-full',
    ]);

    $shouldProvideXData = ! $attributes->has('x-data');
    $xData = $shouldProvideXData ? "{ open: " . ($open ? 'true' : 'false') . " }" : null;
@endphp

<div
    @if ($xData) x-data="{{ $xData }}" @endif
    @if ($closeOnEsc) x-on:keydown.escape.window="open = false" @endif
    {{ $attributes->except(['class']) }}
>
    {{-- Trigger slot --}}
    @isset($trigger)
        <span x-on:click="open = true">
            {{ $trigger }}
        </span>
    @endisset

    {{-- Backdrop --}}
    @if ($backdrop)
        <div
            x-cloak
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm"
            @if ($closeOnBackdrop) x-on:click="open = false" @endif
            aria-hidden="true"
        ></div>
    @endif

    {{-- Panel --}}
    <div
        id="{{ $offcanvasId }}"
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="{{ $translateHiddenClass }} opacity-0"
        x-transition:enter-end="{{ $translateVisibleClass }} opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="{{ $translateVisibleClass }} opacity-100"
        x-transition:leave-end="{{ $translateHiddenClass }} opacity-0"
        class="{{ $panelBase }}"
        role="dialog"
        aria-modal="true"
        @isset($title) aria-labelledby="{{ $offcanvasId }}-title" @endisset
    >
        {{-- Header --}}
        @if ($dismissible || isset($title))
            <div class="flex items-center justify-between gap-4 border-b border-(--border-subtle) px-5 py-4">
                <div class="min-w-0 flex-1">
                    @isset($title)
                        <h3 id="{{ $offcanvasId }}-title" class="truncate text-base font-semibold text-(--text-primary)">
                            {{ $title }}
                        </h3>
                    @endisset
                    @isset($description)
                        <p class="mt-1 text-sm text-(--text-secondary)">
                            {{ $description }}
                        </p>
                    @endisset
                </div>

                @if ($dismissible)
                    <button
                        type="button"
                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-(--text-secondary) transition-colors duration-150 hover:bg-(--surface-hover) hover:text-(--text-primary) focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20"
                        x-on:click="open = false"
                        aria-label="{{ __('ds.actions.close') }}"
                    >
                        <iconify-icon icon="iconamoon:sign-times-light" class="text-xl"></iconify-icon>
                    </button>
                @endif
            </div>
        @endif

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-5">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @isset($footer)
            <div class="border-t border-(--border-subtle) px-5 py-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
