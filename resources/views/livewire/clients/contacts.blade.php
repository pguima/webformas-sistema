<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-(--text-primary)">{{ __('app.clients.profile.tabs.contacts') }}</h2>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ $client->name }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                type="button"
                icon="solar:add-circle-linear"
                x-on:click="$dispatch('open-create-client-contact-offcanvas'); $wire.create()"
                wire:loading.attr="disabled"
            >
                + Contato
            </x-ds::button>
        </div>
    </div>

    <x-ds::card>
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input
                    icon="solar:magnifer-linear"
                    placeholder="Buscar contatos..."
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-40">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-medium text-(--text-primary)">Por página</span>
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
            </div>
        </div>

        <x-ds::table :headers="['Nome', 'Whatsapp', 'Cargo', 'Ações']">
            @forelse($contacts as $contact)
                <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="client-contact-{{ $contact->id }}">
                    <x-ds::table-cell>
                        <div class="text-sm font-medium text-(--text-primary)">{{ $contact->name }}</div>
                    </x-ds::table-cell>
                    <x-ds::table-cell>
                        <div class="text-sm text-(--text-secondary)">{{ $contact->whatsapp ?: __('app.common.dash') }}</div>
                    </x-ds::table-cell>
                    <x-ds::table-cell>
                        <div class="text-sm text-(--text-secondary)">{{ $contact->role ?: __('app.common.dash') }}</div>
                    </x-ds::table-cell>
                    <x-ds::table-cell>
                        <div class="flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:pen-linear"
                                wire:mouseenter="prefetch({{ $contact->id }})"
                                x-on:click="$dispatch('open-edit-client-contact-offcanvas'); $wire.edit({{ $contact->id }})"
                                wire:loading.attr="disabled"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-(--status-error)"
                                wire:click.prevent="confirmDelete({{ $contact->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>
                    </x-ds::table-cell>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-sm text-(--text-secondary)">
                        Nenhum contato cadastrado.
                    </td>
                </tr>
            @endforelse

            <x-slot:footer>
                <div class="mt-4">
                    {{ $contacts->links() }}
                </div>
            </x-slot:footer>
        </x-ds::table>
    </x-ds::card>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-create-client-contact-offcanvas.window="open = true"
        x-on:open-edit-client-contact-offcanvas.window="open = true"
        x-on:close-client-contact-offcanvas.window="open = false"
        title="{{ $contactId ? 'Editar contato' : 'Novo contato' }}"
        description="{{ $contactId ? 'Atualize os dados do contato.' : 'Cadastre um novo contato para este cliente.' }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="Nome" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input-mask label="Whatsapp" wire:model="whatsapp" mask="phone" :error="$errors->first('whatsapp')" />
            <x-ds::input label="Cargo" wire:model="role" :error="$errors->first('role')" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">Cancelar</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Salvar</span>
                    <span wire:loading wire:target="save">Salvar</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="Excluir contato"
        description="Essa ação não pode ser desfeita."
    >
        <div class="space-y-4">
            <x-ds::input
                label="Confirmação"
                placeholder="Digite DELETE para confirmar"
                wire:model="deleteConfirmation"
                :error="$errors->first('deleteConfirmation')"
            />

            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" x-on:click="closeModal()">Cancelar</x-ds::button>
                <x-ds::button type="button" variant="danger" wire:click="delete" wire:loading.attr="disabled">
                    Excluir
                </x-ds::button>
            </div>
        </div>
    </x-ds::modal>
</div>
