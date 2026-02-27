@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'disabled' => false,
    'fullWidth' => false,
])

@php
    $isDisabled = $disabled || $loading;

    $base = 'inline-flex items-center justify-center font-medium transition-all duration-200 cursor-pointer focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20';

    $sizeClass = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs h-8 rounded-md',
        'lg' => 'px-5 py-3 text-base h-11 rounded-lg',
        'icon' => 'h-8 w-8 p-0 rounded-md',
        default => 'px-4 py-2 text-sm h-9 rounded-md',
    };

    $variantClass = match ($variant) {
        'secondary' => 'bg-(--surface-card) text-(--text-primary) border border-(--border-default) hover:bg-(--surface-hover) hover:border-(--border-hover) shadow-(--shadow-xs)',
        'outline' => 'border border-(--border-default) bg-transparent text-(--text-primary) hover:bg-(--surface-hover) hover:border-(--border-hover)',
        'ghost' => 'bg-transparent text-(--text-secondary) hover:bg-(--surface-hover) hover:text-(--text-primary)',
        'link' => 'bg-transparent text-(--text-link) hover:underline p-0 h-auto',
        'success' => 'bg-(--status-success) text-white hover:opacity-90 shadow-(--shadow-xs) hover:shadow-(--shadow-sm)',
        'warning' => 'bg-(--status-warning) text-white hover:opacity-90 shadow-(--shadow-xs) hover:shadow-(--shadow-sm)',
        'danger' => 'bg-(--status-error) text-white hover:opacity-90 shadow-(--shadow-xs) hover:shadow-(--shadow-sm)',
        'info' => 'bg-(--status-info) text-white hover:opacity-90 shadow-(--shadow-xs) hover:shadow-(--shadow-sm)',
        default => 'bg-(--color-primary) text-(--text-on-primary) hover:bg-(--color-primary-hover) shadow-(--shadow-xs) hover:shadow-(--shadow-sm)',
    };

    $stateClass = $isDisabled ? 'opacity-40 cursor-not-allowed pointer-events-none' : '';
    $widthClass = $fullWidth ? 'w-full' : '';

    $classes = implode(' ', array_filter([$base, $sizeClass, $variantClass, $stateClass, $widthClass]));

    $tag = $href ? 'a' : 'button';

    $iconMarkup = null;
    if (! $loading && $icon) {
        $iconSize = match ($size) {
            'sm' => 'text-base',
            'lg' => 'text-xl',
            default => 'text-lg',
        };
        $iconMarkup = '<iconify-icon icon="' . e($icon) . '" class="' . $iconSize . '"></iconify-icon>';
    }

    $gapClass = ($iconMarkup || $loading) ? 'gap-2' : '';
@endphp

@if ($tag === 'a')
    <a
        href="{{ $isDisabled ? 'javascript:void(0)' : $href }}"
        aria-disabled="{{ $isDisabled ? 'true' : 'false' }}"
        {{ $attributes->merge(['class' => $classes . ' ' . $gapClass]) }}
    >
        @if ($loading)
            <x-ds::spinner size="sm" variant="secondary" />
        @elseif ($iconMarkup && $iconPosition === 'left')
            {!! $iconMarkup !!}
        @endif

        @if (trim($slot) !== '')
            <span>{{ $slot }}</span>
        @endif

        @if (! $loading && $iconMarkup && $iconPosition === 'right')
            {!! $iconMarkup !!}
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $isDisabled ? 'disabled' : '' }}
        aria-busy="{{ $loading ? 'true' : 'false' }}"
        {{ $attributes->merge(['class' => $classes . ' ' . $gapClass]) }}
    >
        @if ($loading)
            <x-ds::spinner size="sm" variant="secondary" />
        @elseif ($iconMarkup && $iconPosition === 'left')
            {!! $iconMarkup !!}
        @endif

        @if (trim($slot) !== '')
            <span>{{ $slot }}</span>
        @endif

        @if (! $loading && $iconMarkup && $iconPosition === 'right')
            {!! $iconMarkup !!}
        @endif
    </button>
@endif
