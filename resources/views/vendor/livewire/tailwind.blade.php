@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-4 py-2 text-sm font-medium text-(--text-muted)">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button type="button" wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-4 py-2 text-sm font-medium text-(--text-primary) transition-colors hover:bg-(--surface-hover)">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="ml-3 inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-4 py-2 text-sm font-medium text-(--text-primary) transition-colors hover:bg-(--surface-hover)">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span class="ml-3 inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-4 py-2 text-sm font-medium text-(--text-muted)">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-(--text-secondary)">
                    {!! __('Showing') !!}
                    <span class="font-medium text-(--text-primary)">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-medium text-(--text-primary)">{{ $paginator->lastItem() }}</span>
                    {!! __('of') !!}
                    <span class="font-medium text-(--text-primary)">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-md shadow-sm">
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="relative inline-flex items-center rounded-l-md border border-(--border-subtle) bg-(--surface-card) px-2 py-2 text-(--text-muted)">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <button type="button" wire:click="previousPage" wire:loading.attr="disabled" rel="prev" aria-label="{{ __('pagination.previous') }}" class="relative inline-flex items-center rounded-l-md border border-(--border-subtle) bg-(--surface-card) px-2 py-2 text-(--text-primary) transition-colors hover:bg-(--surface-hover)">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true" class="relative inline-flex items-center border border-(--border-subtle) bg-(--surface-card) px-4 py-2 text-sm font-medium text-(--text-muted)">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="relative inline-flex items-center border border-(--border-subtle) bg-(--surface-selected) px-4 py-2 text-sm font-semibold text-(--text-primary)">{{ $page }}</span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="relative inline-flex items-center border border-(--border-subtle) bg-(--surface-card) px-4 py-2 text-sm font-medium text-(--text-primary) transition-colors hover:bg-(--surface-hover)">{{ $page }}</button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage" wire:loading.attr="disabled" rel="next" aria-label="{{ __('pagination.next') }}" class="relative inline-flex items-center rounded-r-md border border-(--border-subtle) bg-(--surface-card) px-2 py-2 text-(--text-primary) transition-colors hover:bg-(--surface-hover)">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="relative inline-flex items-center rounded-r-md border border-(--border-subtle) bg-(--surface-card) px-2 py-2 text-(--text-muted)">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
