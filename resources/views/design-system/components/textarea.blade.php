@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'placeholder' => null,
    'helper' => null,
    'error' => null,
    'rows' => 3,
    'disabled' => false,
    'readonly' => false,
    'required' => false,
])

@php
    $id = $id ?? $name ?? 'textarea-' . uniqid();
    
    $containerClass = 'flex flex-col gap-1.5 w-full';
    $labelClass = 'text-sm font-medium text-(--text-primary)';
    
    $baseClass = 'w-full rounded-lg border bg-(--surface-card) px-3 py-2 text-sm text-(--text-primary) transition-all duration-200 placeholder:text-(--text-muted) focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20 disabled:cursor-not-allowed disabled:bg-(--surface-hover) disabled:text-(--text-muted)';
    
    $borderClass = $error 
        ? 'border-(--status-error) focus:border-(--status-error) focus:ring-(--status-error)/20' 
        : 'border-(--border-default) focus:border-(--color-primary) hover:border-(--border-hover)';

    $textareaClasses = implode(' ', [$baseClass, $borderClass]);
    
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

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($required) required @endif
        {{ $attributes->merge(['class' => $textareaClasses]) }}
    >{{ $slot }}</textarea>

    @if ($error)
        <p class="{{ $messageClass }} {{ $errorClass }}">{{ $error }}</p>
    @elseif ($helper)
        <p class="{{ $messageClass }} {{ $helperClass }}">{{ $helper }}</p>
    @endif
</div>
