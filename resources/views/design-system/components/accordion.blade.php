@props([
    'items' => [],
    'multiple' => false,
    'defaultOpen' => null,
    'variant' => 'default',
    'size' => 'md',
])

@php
    $baseItemClass = 'overflow-hidden rounded-lg transition-all duration-200';

    $itemBorderClass = match ($variant) {
        'ghost' => 'border border-(--border-subtle)',
        default => 'border border-(--border-default)',
    };

    $buttonPaddingClass = match ($size) {
        'sm' => 'px-4 py-3 text-sm',
        'lg' => 'px-6 py-5 text-base',
        default => 'px-5 py-4 text-sm',
    };

    $contentPaddingClass = match ($size) {
        'sm' => 'px-4 pb-4 text-sm',
        'lg' => 'px-6 pb-6 text-sm',
        default => 'px-5 pb-5 text-sm',
    };

    $defaultOpenIndexes = [];
    if (is_numeric($defaultOpen)) {
        $defaultOpenIndexes = [(int) $defaultOpen];
    } elseif (is_array($defaultOpen)) {
        $defaultOpenIndexes = array_values(array_map(fn ($v) => (int) $v, $defaultOpen));
    }

    $accordionId = 'ds-accordion-' . uniqid();
@endphp

<div
    {{ $attributes->class(['space-y-3']) }}
    x-data="{
        open: @js($multiple ? $defaultOpenIndexes : (count($defaultOpenIndexes) ? $defaultOpenIndexes[0] : null)),
        isOpen(i) {
            return Array.isArray(this.open) ? this.open.includes(i) : this.open === i;
        },
        toggle(i) {
            if (Array.isArray(this.open)) {
                if (this.open.includes(i)) {
                    this.open = this.open.filter(x => x !== i);
                } else {
                    this.open = [...this.open, i];
                }
                return;
            }

            this.open = this.open === i ? null : i;
        }
    }"
>
    @foreach ($items as $index => $item)
        @php
            $title = $item['title'] ?? '';
            $content = $item['content'] ?? '';
            $badge = $item['badge'] ?? null;
            $icon = $item['icon'] ?? null;

            $headingId = $accordionId . '-heading-' . $index;
            $panelId = $accordionId . '-panel-' . $index;
        @endphp

        <div
            class="{{ $baseItemClass }} {{ $itemBorderClass }} bg-(--surface-card)"
            :class="isOpen({{ $index }}) ? 'shadow-(--shadow-sm)' : ''"
        >
            <h3 id="{{ $headingId }}">
                <button
                    type="button"
                    class="flex w-full items-center justify-between gap-3 bg-transparent text-left font-semibold text-(--text-primary) transition-colors duration-150 hover:text-(--color-primary) {{ $buttonPaddingClass }}"
                    x-on:click="toggle({{ $index }})"
                    x-bind:aria-expanded="isOpen({{ $index }}) ? 'true' : 'false'"
                    aria-controls="{{ $panelId }}"
                >
                    <span class="flex min-w-0 items-center gap-3">
                        @if ($icon)
                            <iconify-icon icon="{{ $icon }}" class="text-lg text-(--text-secondary)"></iconify-icon>
                        @endif
                        <span class="truncate">{{ $title }}</span>
                        @if ($badge)
                            <x-ds::badge style="soft" variant="info">{{ $badge }}</x-ds::badge>
                        @endif
                    </span>

                    <span
                        class="inline-flex h-6 w-6 items-center justify-center rounded-md border border-(--border-subtle) text-(--text-secondary) transition-all duration-200"
                        :class="isOpen({{ $index }}) ? 'bg-(--color-primary-light) border-(--color-primary-light) text-(--color-primary)' : ''"
                    >
                        <iconify-icon
                            icon="solar:alt-arrow-down-linear"
                            class="text-sm transition-transform duration-200"
                            x-bind:class="isOpen({{ $index }}) ? 'rotate-180' : ''"
                        ></iconify-icon>
                    </span>
                </button>
            </h3>

            <div
                id="{{ $panelId }}"
                role="region"
                aria-labelledby="{{ $headingId }}"
                x-cloak
                x-show="isOpen({{ $index }})"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="text-(--text-secondary)"
            >
                <div class="{{ $contentPaddingClass }} border-t border-(--border-subtle)">
                    {{ $content }}
                </div>
            </div>
        </div>
    @endforeach
</div>
