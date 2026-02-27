@props([
    'align' => 'left',
])

@php
    $alignClass = match ($align) {
        'center' => 'justify-center',
        'right' => 'justify-end',
        default => 'justify-start',
    };
@endphp

<div {{ $attributes->class("flex flex-wrap items-center gap-2 {$alignClass}") }}>
    {{ $slot }}
</div>
