@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'checked' => false,
    'disabled' => false,
    'error' => null,
    'helper' => null,
])

@php
    $id = $id ?? $name ?? 'checkbox-' . uniqid();
    
    $inputClass = 'peer h-4 w-4 shrink-0 cursor-pointer appearance-none rounded border border-(--border-default) bg-(--surface-card) transition-all checked:border-(--color-primary) checked:bg-(--color-primary) hover:border-(--color-primary) focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20 disabled:cursor-not-allowed disabled:bg-(--surface-hover) disabled:opacity-50';
    
    if ($error) {
        $inputClass .= ' border-(--status-error)';
    }
@endphp

<div class="flex items-start">
    <div class="flex h-5 items-center">
        <input
            type="checkbox"
            id="{{ $id }}"
            name="{{ $name }}"
            @if($checked) checked @endif
            @if($disabled) disabled @endif
            {{ $attributes->class($inputClass) }}
        />
        <iconify-icon icon="solar:check-read-linear" class="pointer-events-none absolute h-4 w-4 text-(--text-on-primary) opacity-0 transition-opacity peer-checked:opacity-100"></iconify-icon>
    </div>
    
    @if ($label)
        <div class="ml-2 text-sm">
            <label for="{{ $id }}" class="font-medium text-(--text-primary) {{ $disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
                {{ $label }}
            </label>
            @if ($error)
                <p class="mt-1 text-xs text-(--status-error)">{{ $error }}</p>
            @elseif ($helper)
                <p class="mt-1 text-xs text-(--text-secondary)">{{ $helper }}</p>
            @endif
        </div>
    @endif
</div>
