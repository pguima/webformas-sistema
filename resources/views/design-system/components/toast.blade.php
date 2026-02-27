@props([
    'variant' => 'info',
    'style' => 'soft',
    'title' => null,
    'icon' => null,
    'dismissible' => true,
    'duration' => 0,
])

@php
    $variantTokens = match ($variant) {
        'primary' => ['c' => 'var(--color-primary)', 'light' => 'var(--color-primary-light)', 'fg' => 'var(--text-on-primary)'],
        'success' => ['c' => 'var(--status-success)', 'light' => 'var(--status-success-light)', 'fg' => 'white'],
        'warning' => ['c' => 'var(--status-warning)', 'light' => 'var(--status-warning-light)', 'fg' => 'white'],
        'danger' => ['c' => 'var(--status-error)', 'light' => 'var(--status-error-light)', 'fg' => 'white'],
        default => ['c' => 'var(--status-info)', 'light' => 'var(--status-info-light)', 'fg' => 'white'],
    };

    $base = 'relative w-full max-w-sm overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-card) shadow-(--shadow-lg)';

    $contentPadding = 'p-4';

    $styleAttr = '';
    $accentStyle = '';

    if ($style === 'solid') {
        $styleAttr = 'background-color: ' . $variantTokens['c'] . '; color: ' . $variantTokens['fg'] . '; border-color: ' . $variantTokens['c'] . ';';
        $accentStyle = 'background-color: rgba(255, 255, 255, 0.3);';
    } elseif ($style === 'outline') {
        $styleAttr = 'background-color: var(--surface-card); color: ' . $variantTokens['c'] . '; border-color: ' . $variantTokens['c'] . ';';
        $accentStyle = 'background-color: ' . $variantTokens['c'] . ';';
    } else {
        // soft style
        $styleAttr = 'background-color: ' . $variantTokens['light'] . '; color: var(--text-primary); border-color: ' . $variantTokens['light'] . ';';
        $accentStyle = 'background-color: ' . $variantTokens['c'] . ';';
    }

    $iconMarkup = null;
    if ($icon) {
        $iconMarkup = '<iconify-icon icon="' . e($icon) . '" class="text-xl shrink-0"></iconify-icon>';
    }

    $shouldProvideXData = ! $attributes->has('x-data');
    $shouldProvideXShow = ! $attributes->has('x-show');
    $shouldProvideXInit = ! $attributes->has('x-init');

    $xData = $shouldProvideXData ? "{ open: true, _t: null }" : null;
    $xShow = $shouldProvideXShow ? 'open' : null;

    $durationMs = is_numeric($duration) ? (int) $duration : 0;
    $xInit = null;
    if ($shouldProvideXInit && $durationMs > 0) {
        $xInit = "_t = setTimeout(() => { open = false }, {$durationMs})";
    }
@endphp

<div
    @if ($xData) x-data="{{ $xData }}" @endif
    @if ($xShow) x-show="{{ $xShow }}" @endif
    @if ($xInit) x-init="{{ $xInit }}" @endif
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
    x-cloak
    {{ $attributes->merge(['class' => $base])->merge(['style' => $styleAttr]) }}
    role="status"
>
    <!-- Accent bar -->
    <div class="absolute left-0 top-0 h-full w-1 rounded-l-lg" style="{{ $accentStyle }}"></div>

    <div class="{{ $contentPadding }} pl-5">
        <div class="flex items-start gap-3">
            @if ($iconMarkup)
                <div class="mt-0.5" style="color: {{ $style === 'solid' ? $variantTokens['fg'] : $variantTokens['c'] }};">
                    {!! $iconMarkup !!}
                </div>
            @endif

            <div class="min-w-0 flex-1">
                @if ($title)
                    <div class="text-sm font-semibold leading-5" style="color: {{ $style === 'solid' ? $variantTokens['fg'] : $variantTokens['c'] }};">
                        {{ $title }}
                    </div>
                @endif

                <div class="{{ $title ? 'mt-1' : '' }} text-sm leading-5" style="color: {{ $style === 'solid' ? 'rgba(255,255,255,0.9)' : 'var(--text-secondary)' }};">
                    {{ $slot }}
                </div>

                @isset($actions)
                    <div class="mt-3">
                        {{ $actions }}
                    </div>
                @endisset
            </div>

            @if ($dismissible)
                <button
                    type="button"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-md opacity-60 transition-opacity duration-150 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20"
                    @click="open = false"
                    aria-label="Close"
                >
                    <iconify-icon icon="iconamoon:sign-times-light" class="text-xl"></iconify-icon>
                </button>
            @endif
        </div>
    </div>
</div>
