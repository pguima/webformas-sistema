@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'helper' => 'SVG, PNG, JPG or GIF (MAX. 800x400px)',
    'error' => null,
    'disabled' => false,
])

@php
    $id = $id ?? $name ?? 'file-' . uniqid();
@endphp

<div class="w-full">
    @if ($label)
        <label class="mb-2 block text-sm font-medium text-(--text-primary)">
            {{ $label }}
        </label>
    @endif
    
    <div class="relative flex w-full flex-col items-center justify-center rounded-lg border-2 border-dashed border-(--border-default) bg-(--surface-page) transition-colors hover:bg-(--surface-hover) {{ $disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
        <div class="flex flex-col items-center justify-center pb-6 pt-5">
            <iconify-icon icon="solar:cloud-upload-linear" class="mb-3 text-3xl text-(--text-muted)"></iconify-icon>
            <p class="mb-2 text-sm text-(--text-secondary)">
                <span class="font-semibold text-(--color-primary)">Click to upload</span> or drag and drop
            </p>
            <p class="text-xs text-(--text-muted)">{{ $helper }}</p>
        </div>
        <input 
            id="{{ $id }}"
            name="{{ $name }}"
            type="file" 
            class="absolute inset-0 h-full w-full opacity-0 {{ $disabled ? 'cursor-not-allowed' : 'cursor-pointer' }}" 
            @if($disabled) disabled @endif
            {{ $attributes }} 
        />
    </div>

    @if ($error)
        <p class="mt-1 text-xs text-(--status-error)">{{ $error }}</p>
    @endif
</div>
