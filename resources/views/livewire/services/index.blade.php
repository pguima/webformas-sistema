<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.services.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.services.subtitle') }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                type="button"
                icon="solar:add-circle-linear"
                x-on:click="$dispatch('open-create-service-offcanvas'); $wire.create()"
                wire:loading.attr="disabled"
            >
                {{ __('app.services.add') }}
            </x-ds::button>
        </div>
    </div>

    <x-ds::card class="mt-6">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input
                    icon="solar:magnifer-linear"
                    placeholder="{{ __('app.services.search_placeholder') }}"
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-40">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-medium text-(--text-primary)">{{ __('app.services.per_page') }}</span>
                        <x-ds::select
                            wire:model.live="perPage"
                            :options="[
                                ['value' => 10, 'label' => '10'],
                                ['value' => 25, 'label' => '25'],
                                ['value' => 50, 'label' => '50'],
                                ['value' => 100, 'label' => '100'],
                            ]"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-ds::button
                        type="button"
                        size="icon"
                        variant="{{ $viewMode === 'list' ? 'secondary' : 'ghost' }}"
                        icon="solar:list-linear"
                        wire:click="$set('viewMode','list')"
                        wire:loading.attr="disabled"
                    />

                    <x-ds::button
                        type="button"
                        size="icon"
                        variant="{{ $viewMode === 'grid' ? 'secondary' : 'ghost' }}"
                        icon="solar:widget-4-linear"
                        wire:click="$set('viewMode','grid')"
                        wire:loading.attr="disabled"
                    />
                </div>
            </div>
        </div>

        @if ($viewMode === 'grid')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($services as $service)
                    <x-ds::card class="relative p-4" wire:key="grid-{{ $service->id }}">
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:pen-linear"
                                wire:mouseenter="prefetch({{ $service->id }})"
                                x-on:click="$dispatch('open-edit-service-offcanvas'); $wire.edit({{ $service->id }})"
                                wire:loading.attr="disabled"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-(--status-error)"
                                wire:click.prevent="confirmDelete({{ $service->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-(--text-primary) truncate">{{ $service->name }}</div>
                            <div class="mt-1 text-xs text-(--text-muted) truncate">{{ number_format((float) $service->price, 2, ',', '.') }}</div>
                        </div>
                    </x-ds::card>
                @empty
                    <div class="py-8 text-center text-sm text-(--text-secondary) sm:col-span-2 xl:col-span-3">
                        {{ __('app.services.no_results', ['search' => $search]) }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $services->links() }}
            </div>
        @else
            <x-ds::table :headers="[__('app.services.table.name'), __('app.services.table.price'), __('app.services.table.actions')]">
                @forelse($services as $service)
                    <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="{{ $service->id }}">
                        <x-ds::table-cell>
                            <div class="text-sm font-medium text-(--text-primary)">{{ $service->name }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ number_format((float) $service->price, 2, ',', '.') }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="flex items-center gap-2">
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    wire:mouseenter="prefetch({{ $service->id }})"
                                    x-on:click="$dispatch('open-edit-service-offcanvas'); $wire.edit({{ $service->id }})"
                                    wire:loading.attr="disabled"
                                />

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-(--status-error)"
                                    wire:click.prevent="confirmDelete({{ $service->id }})"
                                    wire:loading.attr="disabled"
                                />
                            </div>
                        </x-ds::table-cell>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-sm text-(--text-secondary)">
                            {{ __('app.services.no_results', ['search' => $search]) }}
                        </td>
                    </tr>
                @endforelse

                <x-slot:footer>
                    <div class="mt-4">
                        {{ $services->links() }}
                    </div>
                </x-slot:footer>
            </x-ds::table>
        @endif
    </x-ds::card>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-create-service-offcanvas.window="open = true"
        x-on:close-service-offcanvas.window="open = false"
        title="{{ __('app.services.offcanvas.create_title') }}"
        description="{{ __('app.services.offcanvas.create_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.services.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.services.form.price') }}" wire:model="price" type="number" step="0.01" required :error="$errors->first('price')" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.services.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.services.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.services.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-edit-service-offcanvas.window="open = true"
        x-on:close-service-offcanvas.window="open = false"
        title="{{ __('app.services.offcanvas.edit_title') }}"
        description="{{ __('app.services.offcanvas.edit_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.services.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.services.form.price') }}" wire:model="price" type="number" step="0.01" required :error="$errors->first('price')" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.services.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.services.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.services.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.services.delete.title') }}"
        size="md"
    >
        <div class="space-y-4">
            <x-ds::alert variant="danger" icon="solar:danger-triangle-linear">
                {{ __('app.services.delete.warning') }}
            </x-ds::alert>

            <p class="text-sm text-(--text-secondary)">
                {!! __('app.services.delete.confirm_help', ['word' => '<span class="select-all font-mono font-bold text-(--status-error)">DELETE</span>']) !!}
            </p>

            <x-ds::input
                wire:model.live="deleteConfirmation"
                placeholder="{{ __('app.services.delete.placeholder', ['word' => 'DELETE']) }}"
                class="border-(--status-error) focus:border-(--status-error) focus:ring-(--status-error)/20"
            />
            @error('deleteConfirmation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.services.form.cancel') }}</x-ds::button>
                <x-ds::button variant="danger" icon="solar:trash-bin-trash-linear" wire:click.prevent="delete" wire:loading.attr="disabled">
                    {{ __('app.services.delete.delete_permanently') }}
                </x-ds::button>
            </div>
        </x-slot:footer>
    </x-ds::modal>
</div>
