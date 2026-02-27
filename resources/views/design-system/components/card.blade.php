@props([
    'title' => null,
    'description' => null,
    'align' => 'left',
    'variant' => 'default',
    'padded' => true,
    'shadow' => true,
    'bordered' => true,
    'hoverable' => false,
])

@php
    $alignClass = match ($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };

    $variantClass = match ($variant) {
        'ghost' => 'bg-transparent',
        'elevated' => 'bg-(--surface-elevated)',
        default => 'bg-(--surface-card)',
    };

    $shadowClass = match (true) {
        !$shadow => '',
        $variant === 'elevated' => 'shadow-(--shadow-md)',
        default => 'shadow-(--shadow-sm)',
    };

    $hoverClass = $hoverable
        ? 'hover:shadow-(--shadow-md) hover:border-(--border-default) cursor-pointer'
        : '';

    $containerClass = implode(' ', array_filter([
        'overflow-hidden rounded-lg transition-all duration-200',
        $bordered ? 'border border-(--border-subtle)' : '',
        $variantClass,
        $shadowClass,
        $hoverClass,
    ]));

    $bodyClass = implode(' ', array_filter([
        $padded ? 'p-6' : '',
        $alignClass,
    ]));
@endphp

<div {{ $attributes->class($containerClass) }}>
    @isset($media)
        <div class="overflow-hidden">
            {{ $media }}
        </div>
    @endisset

    @isset($header)
        <div class="border-b border-(--border-subtle) bg-(--surface-card) px-6 py-4">
            {{ $header }}
        </div>
    @endisset

    <div class="{{ $bodyClass }}">
        @if ($title)
            <div class="text-base font-semibold text-(--text-primary)">
                {{ $title }}
            </div>
        @endif

        @if ($description)
            <div class="mt-2 text-sm text-(--text-secondary)">
                {{ $description }}
            </div>
        @endif

        @if ($title || $description)
            <div class="mt-4">
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif
    </div>

    @isset($footer)
        <div class="border-t border-(--border-subtle) bg-(--surface-card) px-6 py-4">
            {{ $footer }}
        </div>
    @endisset
</div>
