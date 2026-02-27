@props([
    'tabs' => [],
    'defaultTab' => 0,
    'variant' => 'underline',
    'size' => 'md',
    'fullWidth' => false,
])

@php
    $tabsId = 'ds-tabs-' . uniqid();

    $navGapClass = match ($variant) {
        'pill' => 'gap-1',
        'button' => 'gap-2',
        default => 'gap-0',
    };

    $navBaseClass = match ($variant) {
        'pill' => 'rounded-lg bg-(--surface-hover) p-1',
        'button' => 'rounded-lg bg-transparent',
        default => 'border-b border-(--border-default)',
    };

    $tabPaddingClass = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-5 py-3 text-sm',
        default => 'px-4 py-2 text-sm',
    };

    $tabBaseClass = implode(' ', array_filter([
        'inline-flex items-center justify-center gap-2 font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20',
        $tabPaddingClass,
        $fullWidth ? 'flex-1' : '',
    ]));

    $tabInactiveClass = match ($variant) {
        'pill' => 'rounded-md text-(--text-secondary) hover:text-(--text-primary)',
        'button' => 'rounded-md border border-(--border-default) text-(--text-secondary) hover:text-(--text-primary) hover:bg-(--surface-hover)',
        default => 'border-b-2 border-transparent text-(--text-secondary) hover:text-(--text-primary)',
    };

    $tabActiveClass = match ($variant) {
        'pill' => 'rounded-md bg-(--surface-card) text-(--color-primary) shadow-(--shadow-sm)',
        'button' => 'rounded-md border border-(--color-primary) bg-(--color-primary-light) text-(--color-primary)',
        default => 'border-b-2 border-(--color-primary) text-(--color-primary)',
    };

    $tabDisabledClass = 'opacity-40 cursor-not-allowed pointer-events-none';
@endphp

<div
    {{ $attributes->class('w-full') }}
    x-data="{
        active: @js((int) $defaultTab),
        select(i) {
            if (this.isDisabled(i)) return;
            this.active = i;
        },
        isDisabled(i) {
            const disabled = @js(array_map(fn ($t) => (bool) ($t['disabled'] ?? false), $tabs));
            return !!disabled[i];
        }
    }"
>
    <div
        class="flex flex-wrap items-center {{ $navGapClass }} {{ $navBaseClass }}"
        role="tablist"
        aria-label="tabs"
    >
        @foreach ($tabs as $index => $tab)
            @php
                $label = $tab['label'] ?? '';
                $icon = $tab['icon'] ?? null;
                $badge = $tab['badge'] ?? null;
                $disabled = (bool) ($tab['disabled'] ?? false);

                $tabId = $tabsId . '-tab-' . $index;
                $panelId = $tabsId . '-panel-' . $index;
            @endphp

            <button
                type="button"
                id="{{ $tabId }}"
                role="tab"
                aria-controls="{{ $panelId }}"
                x-bind:aria-selected="active === {{ $index }} ? 'true' : 'false'"
                x-bind:tabindex="active === {{ $index }} ? 0 : -1"
                x-on:click="select({{ $index }})"
                @if ($disabled)
                    disabled
                    aria-disabled="true"
                @endif
                class="{{ $tabBaseClass }} {{ $disabled ? $tabDisabledClass : '' }}"
                x-bind:class="active === {{ $index }} ? '{{ $tabActiveClass }}' : '{{ $tabInactiveClass }}'"
            >
                @if ($icon)
                    <iconify-icon icon="{{ $icon }}" class="text-base"></iconify-icon>
                @endif

                <span class="truncate">{{ $label }}</span>

                @if ($badge)
                    <x-ds::badge style="soft" variant="info" size="sm">{{ $badge }}</x-ds::badge>
                @endif
            </button>
        @endforeach
    </div>

    <div class="mt-4">
        @foreach ($tabs as $index => $tab)
            @php
                $content = $tab['content'] ?? '';

                $tabId = $tabsId . '-tab-' . $index;
                $panelId = $tabsId . '-panel-' . $index;
            @endphp

            <div
                id="{{ $panelId }}"
                role="tabpanel"
                aria-labelledby="{{ $tabId }}"
                x-cloak
                x-show="active === {{ $index }}"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-x-1"
                x-transition:enter-end="opacity-100 translate-x-0"
                class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 text-sm text-(--text-secondary)"
            >
                {{ $content }}
            </div>
        @endforeach
    </div>
</div>
