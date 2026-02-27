@props([
    'text' => null,
    'placement' => 'top',
    'trigger' => 'hover',
    'variant' => 'dark',
    'size' => 'md',
])

@php
    $tooltipId = 'ds-tooltip-' . uniqid();

    $sizeClass = match ($size) {
        'sm' => 'px-2 py-1 text-xs',
        'lg' => 'px-4 py-2 text-sm',
        default => 'px-3 py-1.5 text-xs',
    };

    $variantStyle = match ($variant) {
        'light' => 'background-color: var(--surface-card); color: var(--text-primary); border: 1px solid var(--border-default);',
        'primary' => 'background-color: var(--color-primary); color: var(--text-on-primary);',
        'success' => 'background-color: var(--status-success); color: white;',
        'info' => 'background-color: var(--status-info); color: white;',
        'warning' => 'background-color: var(--status-warning); color: white;',
        'danger' => 'background-color: var(--status-error); color: white;',
        default => 'background-color: var(--text-primary); color: var(--surface-card);',
    };

    $positionClass = match ($placement) {
        'right' => 'left-full ml-2 top-1/2 -translate-y-1/2',
        'left' => 'right-full mr-2 top-1/2 -translate-y-1/2',
        'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
        default => 'bottom-full mb-2 left-1/2 -translate-x-1/2',
    };

    $arrowClass = match ($placement) {
        'right' => 'left-0 top-1/2 -translate-x-1/2 -translate-y-1/2',
        'left' => 'right-0 top-1/2 translate-x-1/2 -translate-y-1/2',
        'bottom' => 'top-0 left-1/2 -translate-x-1/2 -translate-y-1/2',
        default => 'bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2',
    };

    $tooltipContent = trim($text ?? '');
    if ($tooltipContent === '' && isset($content)) {
        $tooltipContent = trim((string) $content);
    }

    $hasTooltipContent = $tooltipContent !== '';

    $hoverOpen = str_contains($trigger, 'hover');
    $clickOpen = str_contains($trigger, 'click');
    $focusOpen = str_contains($trigger, 'focus');
@endphp

<span
    class="relative inline-flex"
    x-data="{ open: false }"
    @if ($hoverOpen)
        x-on:mouseenter="open = true"
        x-on:mouseleave="open = false"
    @endif
    @if ($clickOpen)
        x-on:click="open = !open"
        x-on:click.outside="open = false"
    @endif
    @if ($focusOpen)
        x-on:focusin="open = true"
        x-on:focusout="open = false"
    @endif
>
    <span class="inline-flex" aria-describedby="{{ $tooltipId }}">
        {{ $slot }}
    </span>

    @if ($hasTooltipContent)
        <span
            id="{{ $tooltipId }}"
            role="tooltip"
            x-cloak
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="pointer-events-none absolute z-50 {{ $positionClass }}"
        >
            <span
                class="relative inline-flex max-w-xs items-center rounded-md font-medium shadow-(--shadow-md) {{ $sizeClass }}"
                style="{{ $variantStyle }}"
            >
                <span class="absolute h-2 w-2 rotate-45 {{ $arrowClass }}" style="{{ $variantStyle }}"></span>
                {{ $tooltipContent }}
            </span>
        </span>
    @endif
</span>
