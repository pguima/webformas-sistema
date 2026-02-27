@props([
    'title' => null,
    'subtitle' => null,
    'padded' => true,
])

<div {{ $attributes->class(implode(' ', array_filter([
    'flex items-center justify-between gap-3',
    $padded ? 'px-6 py-4' : '',
]))) }}>
    <div class="min-w-0">
        @if ($title)
            <div class="text-sm font-semibold">
                {{ $title }}
            </div>
        @endif

        @if ($subtitle)
            <div class="mt-0.5 text-xs text-(--ds-muted-foreground)">
                {{ $subtitle }}
            </div>
        @endif

        {{ $slot }}
    </div>

    @isset($actions)
        <div class="shrink-0">
            {{ $actions }}
        </div>
    @endisset
</div>
