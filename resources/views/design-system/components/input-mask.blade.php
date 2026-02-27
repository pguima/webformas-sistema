@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => null,
    'helper' => null,
    'error' => null,
    'icon' => null,
    'mask' => null, // 'phone', 'cpf', 'cnpj', 'date', 'cep', 'money' or pattern like '999.999'
    'disabled' => false,
    'required' => false,
])

@php
    $id = $id ?? $name ?? 'input-mask-' . uniqid();
    
    // JS Logic for masking
    // Using vanilla JS logic inside Alpine component
    // Assuming simple replacement: 9 = digit, a = letter, * = any
@endphp

<div
    class="flex flex-col gap-1.5 w-full"
    x-data="{
        value: '',
        maskType: '{{ $mask }}',
        
        format(input) {
            let val = input.replace(/\D/g, ''); // Remove non-digits
            let type = this.maskType;
            
            if (type === 'money') {
               // Simple money formatting
               if (!val) return '';
               val = (parseInt(val) / 100).toFixed(2) + '';
               val = val.replace('.', ',');
               val = val.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
               return 'R$ ' + val;
            }
            
            // Standard masks
            let maskPattern = '';
            
            if (type === 'cpf') maskPattern = '999.999.999-99';
            else if (type === 'cnpj') maskPattern = '99.999.999/9999-99';
            else if (type === 'cep') maskPattern = '99999-999';
            else if (type === 'date') maskPattern = '99/99/9999';
            else if (type === 'phone') {
                maskPattern = val.length > 10 ? '(99) 99999-9999' : '(99) 9999-9999';
            } else {
                maskPattern = type; // Custom pattern
            }
            
            // Apply maskPattern to val
            if (!maskPattern) return input;
            
            let masked = '';
            let valIndex = 0;
            
            for (let i = 0; i < maskPattern.length; i++) {
                if (valIndex >= val.length) break;
                
                let maskChar = maskPattern[i];
                let valChar = val[valIndex];
                
                if (maskChar === '9') {
                    if (/\d/.test(valChar)) {
                        masked += valChar;
                        valIndex++;
                    } else {
                         valIndex++; // Skip invalid char if strictly matching (optional)
                    }
                } else {
                    masked += maskChar;
                    if (valChar === maskChar) valIndex++; // If user typed the separator
                }
            }
            return masked;
        },
        
        handleInput(e) {
            let el = e.target;
            // Store cursor position logic could be added here for perfection
            this.value = this.format(el.value);
            el.value = this.value;
        }
    }"
>
    {{-- Label --}}
    @if ($label)
        <label for="{{ $id }}" class="text-sm font-medium text-(--text-primary)">
            {{ $label }}
            @if ($required) <span class="text-(--status-error)">*</span> @endif
        </label>
    @endif

    <div class="relative">
        {{-- Icon --}}
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-(--text-secondary)">
                <iconify-icon icon="{{ $icon }}" class="text-lg"></iconify-icon>
            </div>
        @endif

        {{-- Input --}}
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            name="{{ $name }}"
            @input="handleInput"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($disabled) disabled @endif
            @if($required) required @endif
            class="w-full appearance-none rounded-lg border border-(--border-default) bg-(--surface-card) px-3 py-2 text-sm text-(--text-primary) transition-all duration-200 placeholder:text-(--text-muted) focus:border-(--color-primary) focus:outline-none focus:ring-2 focus:ring-(--color-primary)/20 disabled:cursor-not-allowed disabled:bg-(--surface-hover) disabled:text-(--text-muted) {{ $icon ? 'pl-10' : '' }} {{ $error ? 'border-(--status-error)' : '' }}"
        />
    </div>

    {{-- Error / Helper --}}
    @if ($error)
        <p class="text-xs text-(--status-error)">{{ $error }}</p>
    @elseif ($helper)
        <p class="text-xs text-(--text-secondary)">{{ $helper }}</p>
    @endif
</div>
