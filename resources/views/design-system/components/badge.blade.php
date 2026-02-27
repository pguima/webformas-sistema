@props([
    'variant' => 'primary',
    'style' => 'solid',
    'size' => 'md',
    'pill' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'dot' => false,
])
@php
    $base = 'inline-flex items-center font-medium transition-all duration-150';

    $sizeClass = match ($size) {
        'sm' => 'text-xs px-2 py-0.5',
        'lg' => 'text-sm px-4 py-1.5',
        default => 'text-xs px-3 py-1',
    };

    $radiusClass = $pill ? 'rounded-full' : 'rounded-md';

    // Tag color variants from design-notion.md
    $variantTokens = match ($variant) {
        'purple' => ['bg' => 'var(--tag-purple)', 'fg' => 'var(--tag-purple-text)'],
        'green' => ['bg' => 'var(--tag-green)', 'fg' => 'var(--tag-green-text)'],
        'blue' => ['bg' => 'var(--tag-blue)', 'fg' => 'var(--tag-blue-text)'],
        'pink' => ['bg' => 'var(--tag-pink)', 'fg' => 'var(--tag-pink-text)'],
        'orange' => ['bg' => 'var(--tag-orange)', 'fg' => 'var(--tag-orange-text)'],
        'red' => ['bg' => 'var(--tag-red)', 'fg' => 'var(--tag-red-text)'],
        'success' => ['bg' => 'var(--status-success-light)', 'fg' => 'var(--status-success)'],
        'info' => ['bg' => 'var(--status-info-light)', 'fg' => 'var(--status-info)'],
        'warning' => ['bg' => 'var(--status-warning-light)', 'fg' => 'var(--status-warning)'],
        'danger' => ['bg' => 'var(--status-error-light)', 'fg' => 'var(--status-error)'],
        'secondary' => ['bg' => 'var(--surface-hover)', 'fg' => 'var(--text-secondary)'],
        'dark' => ['bg' => 'var(--text-primary)', 'fg' => 'var(--surface-card)'],
        'light' => ['bg' => 'var(--surface-hover)', 'fg' => 'var(--text-primary)'],
        default => ['bg' => 'var(--color-primary)', 'fg' => 'var(--text-on-primary)'],
    };

    // Style variations
    $colorStyle = '';
    $textStyle = '';
    $borderStyle = '';

    if ($style === 'outline') {
        $colorStyle = 'background-color: transparent;';
        $textStyle = 'color: ' . $variantTokens['fg'] . ';';
        $borderStyle = 'border: 1px solid ' . $variantTokens['fg'] . ';';
    } elseif ($style === 'soft') {
        $colorStyle = 'background-color: ' . $variantTokens['bg'] . ';';
        $textStyle = 'color: ' . $variantTokens['fg'] . ';';
        $borderStyle = '';
    } else {
        // solid style for primary variant
        if ($variant === 'primary') {
            $colorStyle = 'background-color: var(--color-primary);';
            $textStyle = 'color: var(--text-on-primary);';
        } else {
            $colorStyle = 'background-color: ' . $variantTokens['bg'] . ';';
            $textStyle = 'color: ' . $variantTokens['fg'] . ';';
        }
    }

    $dotMarkup = null;
    if ($dot) {
        $dotMarkup = '<span class="h-1.5 w-1.5 rounded-full" style="background-color: ' . $variantTokens['fg'] . ';"></span>';
    }

    $iconMarkup = null;
    if ($icon) {
        $iconMarkup = '<iconify-icon icon="' . e($icon) . '" class="text-sm"></iconify-icon>';
    }

    $gapClass = ($dot || $iconMarkup) ? 'gap-1.5' : '';
    $classes = implode(' ', array_filter([$base, $gapClass, $sizeClass, $radiusClass]));

    $styleAttr = $colorStyle . $textStyle . $borderStyle;
@endphp

<span {{ $attributes->merge(['class' => $classes, 'style' => $styleAttr]) }}>
    @if ($dotMarkup)
        {!! $dotMarkup !!}
    @endif

    @if ($iconMarkup && $iconPosition === 'left')
        {!! $iconMarkup !!}
    @endif

    @if (trim($slot) !== '')
        <span>{{ $slot }}</span>
    @endif

    @if ($iconMarkup && $iconPosition === 'right')
        {!! $iconMarkup !!}
    @endif
</span>
