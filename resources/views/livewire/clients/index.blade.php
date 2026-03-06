<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.clients.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.clients.subtitle') }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                type="button"
                icon="solar:add-circle-linear"
                x-on:click="$dispatch('open-create-client-offcanvas'); $wire.create()"
                wire:loading.attr="disabled"
            >
                {{ __('app.clients.add') }}
            </x-ds::button>
        </div>
    </div>

    <x-ds::card class="mt-6">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input
                    icon="solar:magnifer-linear"
                    placeholder="{{ __('app.clients.search_placeholder') }}"
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-40">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-medium text-(--text-primary)">{{ __('app.clients.per_page') }}</span>
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
                @forelse($clients as $client)
                    <x-ds::card class="relative p-4" wire:key="grid-{{ $client->id }}">
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:pen-linear"
                                wire:mouseenter="prefetch({{ $client->id }})"
                                x-on:click="$dispatch('open-edit-client-offcanvas'); $wire.edit({{ $client->id }})"
                                wire:loading.attr="disabled"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-(--status-error)"
                                wire:click.prevent="confirmDelete({{ $client->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-(--text-primary) truncate">{{ $client->name }}</div>
                            <div class="mt-1 text-xs text-(--text-muted) truncate">{{ $client->cnpj ?: __('app.common.dash') }}</div>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-2">
                            <x-ds::badge variant="secondary">{{ $client->category ?: __('app.common.dash') }}</x-ds::badge>
                        </div>
                    </x-ds::card>
                @empty
                    <div class="py-8 text-center text-sm text-(--text-secondary) sm:col-span-2 xl:col-span-3">
                        {{ __('app.clients.no_results', ['search' => $search]) }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $clients->links() }}
            </div>
        @else
            <x-ds::table :headers="[__('app.clients.table.name'), __('app.clients.table.cnpj'), __('app.clients.table.category'), __('app.clients.table.actions')]">
                @forelse($clients as $client)
                    <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="{{ $client->id }}">
                        <x-ds::table-cell>
                            <div class="text-sm font-medium text-(--text-primary)">{{ $client->name }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ $client->cnpj ?: __('app.common.dash') }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <x-ds::badge variant="secondary">{{ $client->category ?: __('app.common.dash') }}</x-ds::badge>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="flex items-center gap-2">
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    wire:mouseenter="prefetch({{ $client->id }})"
                                    x-on:click="$dispatch('open-edit-client-offcanvas'); $wire.edit({{ $client->id }})"
                                    wire:loading.attr="disabled"
                                />

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-(--status-error)"
                                    wire:click.prevent="confirmDelete({{ $client->id }})"
                                    wire:loading.attr="disabled"
                                />
                            </div>
                        </x-ds::table-cell>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm text-(--text-secondary)">
                            {{ __('app.clients.no_results', ['search' => $search]) }}
                        </td>
                    </tr>
                @endforelse

                <x-slot:footer>
                    <div class="mt-4">
                        {{ $clients->links() }}
                    </div>
                </x-slot:footer>
            </x-ds::table>
        @endif
    </x-ds::card>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-create-client-offcanvas.window="open = true"
        x-on:close-client-offcanvas.window="open = false"
        title="{{ __('app.clients.offcanvas.create_title') }}"
        description="{{ __('app.clients.offcanvas.create_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.clients.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.clients.form.cnpj') }}" wire:model="cnpj" :error="$errors->first('cnpj')" />
            <x-ds::input label="{{ __('app.clients.form.category') }}" wire:model="category" :error="$errors->first('category')" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.clients.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.clients.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.clients.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-edit-client-offcanvas.window="open = true"
        x-on:close-client-offcanvas.window="open = false"
        title="{{ __('app.clients.offcanvas.edit_title') }}"
        description="{{ __('app.clients.offcanvas.edit_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.clients.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.clients.form.cnpj') }}" wire:model="cnpj" :error="$errors->first('cnpj')" />
            <x-ds::input label="{{ __('app.clients.form.category') }}" wire:model="category" :error="$errors->first('category')" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.clients.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.clients.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.clients.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.clients.delete.title') }}"
        size="md"
    >
        <div class="space-y-4">
            <x-ds::alert variant="danger" icon="solar:danger-triangle-linear">
                {{ __('app.clients.delete.warning') }}
            </x-ds::alert>

            <p class="text-sm text-(--text-secondary)">
                {!! __('app.clients.delete.confirm_help', ['word' => '<span class="select-all font-mono font-bold text-(--status-error)">DELETE</span>']) !!}
            </p>

            <x-ds::input
                wire:model.live="deleteConfirmation"
                placeholder="{{ __('app.clients.delete.placeholder', ['word' => 'DELETE']) }}"
                class="border-(--status-error) focus:border-(--status-error) focus:ring-(--status-error)/20"
            />
            @error('deleteConfirmation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.clients.form.cancel') }}</x-ds::button>
                <x-ds::button variant="danger" icon="solar:trash-bin-trash-linear" wire:click.prevent="delete" wire:loading.attr="disabled">
                    {{ __('app.clients.delete.delete_permanently') }}
                </x-ds::button>
            </div>
        </x-slot:footer>
    </x-ds::modal>
</div>
