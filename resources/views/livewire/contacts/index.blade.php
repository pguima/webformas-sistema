<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">Contatos</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">Lista completa de contatos de todas as empresas.</p>
        </div>
    </div>

    <x-ds::card>
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input
                    icon="solar:magnifer-linear"
                    placeholder="Buscar por nome, whatsapp, cargo ou empresa..."
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

        <x-ds::table :headers="['Nome', 'Whatsapp', 'Cargo', 'Cliente (Empresa/CNPJ)', 'Ações']">
            @forelse($contacts as $contact)
                <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="contact-{{ $contact->id }}">
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
                        <div class="text-sm font-medium text-(--text-primary)">{{ $contact->client?->name ?: __('app.common.dash') }}</div>
                        <div class="mt-1 text-xs text-(--text-muted)">{{ $contact->client?->cnpj ?: __('app.common.dash') }}</div>
                    </x-ds::table-cell>
                    <x-ds::table-cell>
                        <div class="flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="sm"
                                variant="secondary"
                                href="/clients/{{ $contact->client_id }}"
                                icon="solar:arrow-right-linear"
                            >
                                Ver cliente
                            </x-ds::button>
                        </div>
                    </x-ds::table-cell>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-sm text-(--text-secondary)">
                        Nenhum contato encontrado.
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
</div>
