@props([
    'size' => 'md',
    'variant' => 'primary',
    'thickness' => 2,
    'label' => null,
])
@php
    $sizePx = match ($size) {
        'xs' => 12,
        'sm' => 16,
        'lg' => 28,
        'xl' => 36,
        default => 20,
    };

    $color = match ($variant) {
        'secondary' => 'var(--text-secondary)',
        'success' => 'var(--status-success)',
        'warning' => 'var(--status-warning)',
        'danger' => 'var(--status-error)',
        'info' => 'var(--status-info)',
        default => 'var(--color-primary)',
    };

    $track = 'color-mix(in oklab, ' . $color . ' 20%, transparent)';

    $thicknessPx = is_numeric($thickness) ? max(1, (int) $thickness) : 2;

    $styleAttr = implode(' ', array_filter([
        'width: ' . $sizePx . 'px;',
        'height: ' . $sizePx . 'px;',
        'border-width: ' . $thicknessPx . 'px;',
        'border-color: ' . $track . ';',
        'border-top-color: ' . $color . ';',
    ]));

    $classes = 'inline-block rounded-full border-solid animate-spin';
@endphp
<span class="inline-flex items-center gap-2" role="status" aria-live="polite">
    <span {{ $attributes->merge(['class' => $classes, 'style' => $styleAttr]) }}></span>

    @if ($label)
        <span class="text-sm text-(--text-secondary)">{{ $label }}</span>
    @endif

    <span class="sr-only">{{ $label ?: 'Loading' }}</span>
</span>
