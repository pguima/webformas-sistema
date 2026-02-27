@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'options' => [],
    'multiple' => false,
    'placeholder' => 'Select option...',
    'searchPlaceholder' => 'Search...',
    'error' => null,
    'helper' => null,
    'disabled' => false,
    'required' => false,
])

@php
    $id = $id ?? $name ?? 'select-search-' . uniqid();
    
    // Normalizar opções para o formato [['value' => 'v', 'label' => 'l']]
    $normalizedOptions = [];
    foreach ($options as $key => $option) {
        if (is_array($option)) {
            $normalizedOptions[] = $option;
        } else {
            $normalizedOptions[] = ['value' => $key, 'label' => $option];
        }
    }
    
    // Converter para JSON para uso no Alpine
    $optionsJson = json_encode($normalizedOptions);
@endphp

<div
    class="flex flex-col gap-1.5 w-full"
    x-data="{
        options: {{ $optionsJson }},
        selected: @if($multiple) [] @else null @endif,
        search: '',
        open: false,
        
        get filteredOptions() {
            if (this.search === '') return this.options;
            return this.options.filter(opt =>
                opt.label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        
        get displayValue() {
            if ({{ $multiple ? 'true' : 'false' }}) {
                if (this.selected.length === 0) return '{{ $placeholder }}';
                return this.selected.length + ' selected';
            } else {
                if (!this.selected) return '{{ $placeholder }}';
                const opt = this.options.find(o => o.value == this.selected);
                return opt ? opt.label : '{{ $placeholder }}';
            }
        },
        
        select(value) {
            if ({{ $multiple ? 'true' : 'false' }}) {
                if (this.selected.includes(value)) {
                    this.selected = this.selected.filter(v => v !== value);
                } else {
                    this.selected.push(value);
                }
            } else {
                this.selected = value;
                this.open = false;
            }
        },
        
        remove(value) {
            this.selected = this.selected.filter(v => v !== value);
        },
        
        isSelected(value) {
            if ({{ $multiple ? 'true' : 'false' }}) {
                return this.selected.includes(value);
            }
            return this.selected == value;
        }
    }"
    @click.outside="open = false"
>
    {{-- Label --}}
    @if ($label)
        <label for="{{ $id }}" class="text-sm font-medium text-(--text-primary)">
            {{ $label }}
            @if ($required) <span class="text-(--status-error)">*</span> @endif
        </label>
    @endif

    {{-- Trigger --}}
    <div class="relative">
        <button
            type="button"
            @click="if(!{{ $disabled ? 'true' : 'false' }}) { open = !open; $nextTick(() => $refs.searchInput?.focus()); }"
            class="flex min-h-[40px] w-full items-center justify-between rounded-lg border bg-(--surface-card) px-3 py-2 text-sm text-(--text-primary) transition-all focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20 {{ $disabled ? 'cursor-not-allowed bg-(--surface-hover) text-(--text-muted)' : 'hover:border-(--border-hover)' }} {{ $error ? 'border-(--status-error)' : 'border-(--border-default)' }}"
        >
            <div class="flex flex-wrap gap-1">
                @if ($multiple)
                    <template x-if="selected.length === 0">
                        <span class="text-(--text-muted)">{{ $placeholder }}</span>
                    </template>
                    <template x-for="val in selected" :key="val">
                        <span class="inline-flex items-center gap-1 rounded bg-(--surface-hover) px-2 py-0.5 text-xs font-medium text-(--text-primary) border border-(--border-subtle)">
                            <span x-text="options.find(o => o.value == val)?.label || val"></span>
                            <span @click.stop="remove(val)" class="cursor-pointer text-(--text-secondary) hover:text-(--text-primary)">
                                <iconify-icon icon="solar:close-circle-linear"></iconify-icon>
                            </span>
                        </span>
                    </template>
                @else
                    <span x-text="displayValue" :class="!selected && 'text-(--text-muted)'"></span>
                @endif
            </div>
            
            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-(--text-secondary) transition-transform" :class="open ? 'rotate-180' : ''"></iconify-icon>
        </button>

        {{-- Dropdown --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 mt-1 max-h-60 w-full overflow-hidden rounded-lg border border-(--border-subtle) bg-(--surface-card) shadow-lg"
            style="display: none;"
        >
            {{-- Search --}}
            <div class="border-b border-(--border-subtle) p-2">
                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-(--text-muted)"></iconify-icon>
                    <input
                        x-ref="searchInput"
                        x-model="search"
                        type="text"
                        placeholder="{{ $searchPlaceholder }}"
                        class="w-full rounded bg-(--surface-page) py-1.5 pl-8 pr-3 text-sm text-(--text-primary) placeholder-(--text-muted) focus:outline-none"
                    >
                </div>
            </div>

            {{-- Options List --}}
            <ul class="max-h-48 overflow-auto py-1">
                <template x-for="option in filteredOptions" :key="option.value">
                    <li
                        @click="select(option.value)"
                        class="relative flex cursor-pointer select-none items-center px-3 py-2 text-sm text-(--text-primary) hover:bg-(--surface-hover)"
                        :class="isSelected(option.value) ? 'bg-(--color-primary)/5 text-(--color-primary) font-medium' : ''"
                    >
                        <span x-text="option.label"></span>
                        <iconify-icon
                            x-show="isSelected(option.value)"
                            icon="solar:check-read-linear"
                            class="absolute right-3 text-(--color-primary)"
                        ></iconify-icon>
                    </li>
                </template>
                <li x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-(--text-muted) text-center">
                    No results found
                </li>
            </ul>
        </div>
    </div>
    
    {{-- Hidden Input for Forms --}}
    @if ($multiple)
        <template x-for="val in selected" :key="val">
            <input type="hidden" name="{{ $name }}[]" :value="val">
        </template>
    @else
        <input type="hidden" name="{{ $name }}" :value="selected">
    @endif

    {{-- Helpers --}}
    @if ($error)
        <p class="text-xs text-(--status-error)">{{ $error }}</p>
    @elseif ($helper)
        <p class="text-xs text-(--text-secondary)">{{ $helper }}</p>
    @endif
</div>
