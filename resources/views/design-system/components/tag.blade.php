@props([
    'variant' => 'secondary',
    'style' => 'soft',
    'size' => 'md',
    'pill' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'dot' => false,
    'removable' => false,
    'disabled' => false,
])

@php
    $base = 'inline-flex items-center font-medium transition-all duration-150';

    $sizeClass = match ($size) {
        'sm' => 'px-2 py-0.5 text-xs gap-1',
        'lg' => 'px-3.5 py-1.5 text-sm gap-2',
        default => 'px-2.5 py-1 text-xs gap-1.5',
    };

    $radiusClass = $pill ? 'rounded-full' : 'rounded-md';

    // Tag color variants from design-notion.md
    $tone = match ($variant) {
        'primary' => ['bg' => 'var(--color-primary)', 'fg' => 'var(--text-on-primary)', 'light' => 'var(--color-primary-light)', 'text' => 'var(--color-primary)'],
        'purple' => ['bg' => 'var(--tag-purple)', 'fg' => 'var(--tag-purple-text)', 'light' => 'var(--tag-purple)', 'text' => 'var(--tag-purple-text)'],
        'green' => ['bg' => 'var(--tag-green)', 'fg' => 'var(--tag-green-text)', 'light' => 'var(--tag-green)', 'text' => 'var(--tag-green-text)'],
        'blue' => ['bg' => 'var(--tag-blue)', 'fg' => 'var(--tag-blue-text)', 'light' => 'var(--tag-blue)', 'text' => 'var(--tag-blue-text)'],
        'pink' => ['bg' => 'var(--tag-pink)', 'fg' => 'var(--tag-pink-text)', 'light' => 'var(--tag-pink)', 'text' => 'var(--tag-pink-text)'],
        'orange' => ['bg' => 'var(--tag-orange)', 'fg' => 'var(--tag-orange-text)', 'light' => 'var(--tag-orange)', 'text' => 'var(--tag-orange-text)'],
        'red' => ['bg' => 'var(--tag-red)', 'fg' => 'var(--tag-red-text)', 'light' => 'var(--tag-red)', 'text' => 'var(--tag-red-text)'],
        'success' => ['bg' => 'var(--status-success)', 'fg' => 'white', 'light' => 'var(--status-success-light)', 'text' => 'var(--status-success)'],
        'info' => ['bg' => 'var(--status-info)', 'fg' => 'white', 'light' => 'var(--status-info-light)', 'text' => 'var(--status-info)'],
        'warning' => ['bg' => 'var(--status-warning)', 'fg' => 'white', 'light' => 'var(--status-warning-light)', 'text' => 'var(--status-warning)'],
        'danger' => ['bg' => 'var(--status-error)', 'fg' => 'white', 'light' => 'var(--status-error-light)', 'text' => 'var(--status-error)'],
        default => ['bg' => 'var(--surface-hover)', 'fg' => 'var(--text-primary)', 'light' => 'var(--surface-hover)', 'text' => 'var(--text-secondary)'],
    };

    // Style classes
    $styleAttr = match ($style) {
        'solid' => 'background-color: ' . $tone['bg'] . '; color: ' . $tone['fg'] . ';',
        'outline' => 'background-color: transparent; color: ' . $tone['text'] . '; border: 1px solid ' . $tone['text'] . ';',
        default => 'background-color: ' . $tone['light'] . '; color: ' . $tone['text'] . ';', // soft
    };

    $stateClass = $disabled ? 'opacity-40 cursor-not-allowed select-none' : '';

    $iconMarkup = null;
    if ($icon) {
        $iconMarkup = '<iconify-icon icon="' . e($icon) . '" class="text-sm"></iconify-icon>';
    }
@endphp

<span
    x-data="{ removed: false }"
    x-show="!removed"
    x-cloak
    class="{{ implode(' ', array_filter([$base, $sizeClass, $radiusClass, $stateClass])) }}"
    style="{{ $styleAttr }}"
    {{ $attributes }}
>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $tone['text'] }};"></span>
    @endif

    @if ($iconMarkup && $iconPosition === 'left')
        {!! $iconMarkup !!}
    @endif

    <span class="leading-none">{{ $slot }}</span>

    @if ($iconMarkup && $iconPosition === 'right')
        {!! $iconMarkup !!}
    @endif

    @if ($removable)
        <button
            type="button"
            class="ml-0.5 inline-flex h-4 w-4 items-center justify-center rounded transition-opacity duration-150 hover:opacity-70"
            x-on:click="removed = true"
            aria-label="{{ __('ds.pages.tags.labels.remove') }}"
            @if ($disabled) disabled @endif
        >
            <iconify-icon icon="iconamoon:sign-times-light" class="text-sm"></iconify-icon>
        </button>
    @endif
</span>
