<div class="fixed top-20 right-4 z-50 flex flex-col gap-2 pointer-events-none p-4 w-full max-w-sm">
    @foreach($toasts as $toast)
        <div wire:key="{{ $toast['id'] }}" class="pointer-events-auto">
            <x-ds::toast :variant="$toast['variant']" :title="$toast['title']" class="!w-full" x-init="
                        setTimeout(() => { open = false }, 3000); 
                        setTimeout(() => { $wire.remove('{{ $toast['id'] }}') }, 3500);
                    ">
                {{ $toast['message'] }}
            </x-ds::toast>
        </div>
    @endforeach
</div>