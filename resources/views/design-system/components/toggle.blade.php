@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'checked' => false,
    'disabled' => false,
    'size' => 'md', // sm, md
])

@php
    $id = $id ?? $name ?? 'toggle-' . uniqid();
    
    $wrapperClass = match ($size) {
        'sm' => 'h-5 w-9',
        default => 'h-6 w-11',
    };
    
    $circleClass = match ($size) {
        'sm' => 'h-3 w-3 translate-x-1 peer-checked:translate-x-5',
        default => 'h-4 w-4 translate-x-1 peer-checked:translate-x-6',
    };
@endphp

<div class="flex items-center gap-3">
    <div class="relative inline-flex shrink-0 items-center">
        <input
            type="checkbox"
            id="{{ $id }}"
            name="{{ $name }}"
            class="peer sr-only"
            @if($checked) checked @endif
            @if($disabled) disabled @endif
            {{ $attributes->except('class') }}
        >
        <div class="{{ $wrapperClass }} cursor-pointer rounded-full bg-(--surface-hover) border border-(--border-subtle) transition-colors peer-focus:ring-2 peer-focus:ring-(--color-primary)/20 peer-checked:bg-(--color-primary) peer-disabled:cursor-not-allowed peer-disabled:opacity-50"></div>
        <div class="pointer-events-none absolute rounded-full bg-white transition-transform {{ $circleClass }}"></div>
    </div>
    
    @if ($label)
        <label for="{{ $id }}" class="text-sm font-medium text-(--text-primary) cursor-pointer select-none {{ $disabled ? 'cursor-not-allowed opacity-50' : '' }}">
            {{ $label }}
        </label>
    @endif
</div>
