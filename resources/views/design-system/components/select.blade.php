@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'options' => [], // ['value' => 'Label'] or [['value' => 'v', 'label' => 'l']]
    'placeholder' => null,
    'helper' => null,
    'error' => null,
    'disabled' => false,
    'required' => false,
    'size' => 'md',
    'icon' => null,
])

@php
    $id = $id ?? $name ?? 'select-' . uniqid();
    
    $containerClass = 'flex flex-col gap-1.5 w-full';
    $labelClass = 'text-sm font-medium text-(--text-primary)';
    
    $baseClass = 'w-full appearance-none rounded-lg border bg-(--surface-card) text-(--text-primary) transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20 disabled:cursor-not-allowed disabled:bg-(--surface-hover) disabled:text-(--text-muted)';
    
    $borderClass = $error 
        ? 'border-(--status-error) focus:border-(--status-error) focus:ring-(--status-error)/20' 
        : 'border-(--border-default) focus:border-(--color-primary) hover:border-(--border-hover)';

    $sizeClass = match ($size) {
        'sm' => 'h-8 py-1 pl-2.5 pr-8 text-xs',
        'lg' => 'h-12 py-3 pl-4 pr-10 text-base',
        default => 'h-10 py-2 pl-3 pr-10 text-sm',
    };
    
    $paddingClass = $icon ? ($size === 'sm' ? 'pl-8' : ($size === 'lg' ? 'pl-11' : 'pl-10')) : '';
    
    $selectClasses = implode(' ', [$baseClass, $borderClass, $sizeClass, $paddingClass]);
    
    $messageClass = 'text-xs';
    $errorClass = 'text-(--status-error)';
    $helperClass = 'text-(--text-secondary)';
@endphp

<div class="{{ $containerClass }}">
    @if ($label)
        <label for="{{ $id }}" class="{{ $labelClass }}">
            {{ $label }}
            @if ($required) <span class="text-(--status-error)">*</span> @endif
        </label>
    @endif

    <div class="relative">
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-(--text-secondary)">
                <iconify-icon icon="{{ $icon }}" class="{{ $size === 'sm' ? 'text-sm' : ($size === 'lg' ? 'text-xl' : 'text-lg') }}"></iconify-icon>
            </div>
        @endif

        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @if($disabled) disabled @endif
            @if($required) required @endif
            {{ $attributes->merge(['class' => $selectClasses]) }}
        >
            @if ($placeholder)
                <option value="" disabled selected>{{ $placeholder }}</option>
            @endif

            @foreach ($options as $key => $option)
                @if (is_array($option))
                     <option value="{{ $option['value'] }}" {{ isset($option['selected']) && $option['selected'] ? 'selected' : '' }}>
                        {{ $option['label'] }}
                    </option>
                @else
                    <option value="{{ $key }}">{{ $option }}</option>
                @endif
            @endforeach
            
            {{ $slot }}
        </select>

        {{-- Chevron Icon --}}
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-(--text-secondary)">
            <iconify-icon icon="solar:alt-arrow-down-linear" class="{{ $size === 'sm' ? 'text-sm' : 'text-lg' }}"></iconify-icon>
        </div>
    </div>

    @if ($error)
        <p class="{{ $messageClass }} {{ $errorClass }}">{{ $error }}</p>
    @elseif ($helper)
        <p class="{{ $messageClass }} {{ $helperClass }}">{{ $helper }}</p>
    @endif
</div>
