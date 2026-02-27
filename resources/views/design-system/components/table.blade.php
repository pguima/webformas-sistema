@props([
    'headers' => [],
    'striped' => false,
    'hoverable' => true,
    'bordered' => false,
    'compact' => false,
    'checkbox' => false,
])

@php
    $wrapperClass = 'w-full overflow-hidden rounded-lg border border-(--border-subtle) bg-(--surface-card) shadow-(--shadow-sm)';
    $tableClass = 'w-full text-left text-sm text-(--text-primary)';
    
    $theadClass = 'border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)';
    $thClass = 'px-6 py-3 font-semibold';
    
    $tbodyClass = 'divide-y divide-(--border-subtle) bg-(--surface-card)';
    $trClass = implode(' ', array_filter([
        'transition-colors duration-150',
        $hoverable ? 'hover:bg-(--surface-hover)' : '',
        $striped ? 'even:bg-(--surface-page)' : '',
    ]));
    
    $tdBasePadding = $compact ? 'px-6 py-2' : 'px-6 py-4';
@endphp

<div {{ $attributes->merge(['class' => $wrapperClass]) }}>
    <div class="overflow-x-auto">
        <table class="{{ $tableClass }}">
            {{-- Header --}}
            <thead class="{{ $theadClass }}">
                <tr>
                    @if ($checkbox)
                        <th class="w-4 p-4">
                            <div class="flex items-center">
                                <input id="checkbox-all" type="checkbox" class="h-4 w-4 rounded border-(--border-default) bg-(--surface-card) text-(--color-primary) focus:ring-(--color-primary)/20">
                            </div>
                        </th>
                    @endif

                    @if (count($headers) > 0)
                        @foreach ($headers as $header)
                            <th scope="col" class="{{ $thClass }}">
                                {{ $header }}
                            </th>
                        @endforeach
                    @else
                        {{ $thead ?? '' }}
                    @endif
                </tr>
            </thead>

            {{-- Body --}}
            <tbody class="{{ $tbodyClass }}">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    {{-- Footer / Pagination --}}
    @isset($footer)
        <div class="border-t border-(--border-subtle) bg-(--surface-card) px-6 py-4">
            {{ $footer }}
        </div>
    @endisset
</div>
