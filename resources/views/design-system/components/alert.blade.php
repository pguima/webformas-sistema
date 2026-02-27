@props([
    'variant' => 'info',
    'style' => 'soft',
    'title' => null,
    'icon' => null,
    'dismissible' => true,
    'bordered' => true,
    'leftBorder' => false,
])

@php
    $variantTokens = match ($variant) {
        'primary' => ['c' => 'var(--color-primary)', 'light' => 'var(--color-primary-light)', 'fg' => 'var(--text-on-primary)'],
        'success' => ['c' => 'var(--status-success)', 'light' => 'var(--status-success-light)', 'fg' => 'white'],
        'warning' => ['c' => 'var(--status-warning)', 'light' => 'var(--status-warning-light)', 'fg' => 'white'],
        'danger' => ['c' => 'var(--status-error)', 'light' => 'var(--status-error-light)', 'fg' => 'white'],
        default => ['c' => 'var(--status-info)', 'light' => 'var(--status-info-light)', 'fg' => 'white'],
    };

    $base = 'relative rounded-lg transition-all duration-200';

    $borderClass = $bordered ? 'border' : '';
    $leftBorderClass = $leftBorder ? 'border-l-[3px]' : '';

    $paddingClass = $title ? 'px-5 py-4' : 'px-5 py-3';

    $styleAttr = '';
    $textStyle = '';
    $borderStyle = '';

    if ($style === 'solid') {
        $styleAttr = 'background-color: ' . $variantTokens['c'] . ';';
        $textStyle = 'color: ' . $variantTokens['fg'] . ';';
        $borderStyle = 'border-color: ' . $variantTokens['c'] . ';';
    } elseif ($style === 'outline') {
        $styleAttr = 'background-color: transparent;';
        $textStyle = 'color: ' . $variantTokens['c'] . ';';
        $borderStyle = 'border-color: ' . $variantTokens['c'] . ';';
    } else {
        // soft style - use light background
        $styleAttr = 'background-color: ' . $variantTokens['light'] . ';';
        $textStyle = 'color: ' . $variantTokens['c'] . ';';
        $borderStyle = 'border-color: ' . $variantTokens['light'] . ';';
    }

    $iconMarkup = null;
    if ($icon) {
        $iconMarkup = '<iconify-icon icon="' . e($icon) . '" class="text-xl shrink-0"></iconify-icon>';
    }

    $classes = implode(' ', array_filter([
        $base,
        $borderClass,
        $leftBorderClass,
        $paddingClass,
    ]));

    $mergedStyle = $styleAttr . $textStyle . $borderStyle;
@endphp

<div
    x-data="{ open: true }"
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-1"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-1"
    x-cloak
    {{ $attributes->merge(['class' => $classes, 'style' => $mergedStyle]) }}
    role="alert"
>
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            @if ($iconMarkup)
                {!! $iconMarkup !!}
            @endif

            <div>
                @if ($title)
                    <div class="text-sm font-semibold">{{ $title }}</div>
                @endif

                <div class="{{ $title ? 'mt-1' : '' }} text-sm">
                    {{ $slot }}
                </div>

                @isset($actions)
                    <div class="mt-3">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </div>

        @if ($dismissible)
            <button
                type="button"
                class="-mt-1 inline-flex h-7 w-7 items-center justify-center rounded-md opacity-70 transition-opacity duration-150 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20"
                @click="open = false"
                aria-label="Close"
            >
                <iconify-icon icon="iconamoon:sign-times-light" class="text-xl"></iconify-icon>
            </button>
        @endif
    </div>
</div>
