<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.api_tokens.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.api_tokens.subtitle') }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                type="button"
                icon="solar:add-circle-linear"
                x-on:click="$dispatch('open-create-api-token-offcanvas'); $wire.create()"
                wire:loading.attr="disabled"
            >
                {{ __('app.api_tokens.add') }}
            </x-ds::button>
        </div>
    </div>

    <x-ds::card :title="__('app.api_tokens.list.title')" :description="__('app.api_tokens.list.description')">
        <x-ds::table :headers="[__('app.api_tokens.table.name'), __('app.api_tokens.table.abilities'), __('app.api_tokens.table.last_used_at'), __('app.api_tokens.table.created_at'), __('app.api_tokens.table.actions')]">
            @forelse($tokens as $token)
                <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="token-{{ $token->id }}">
                    <x-ds::table-cell>
                        <div class="text-sm font-medium text-(--text-primary)">{{ $token->name }}</div>
                    </x-ds::table-cell>

                    <x-ds::table-cell>
                        <div class="text-sm text-(--text-secondary)">
                            {{ is_array($token->abilities) && count($token->abilities) ? implode(', ', $token->abilities) : '—' }}
                        </div>
                    </x-ds::table-cell>

                    <x-ds::table-cell>
                        <div class="text-sm text-(--text-secondary)">{{ $token->last_used_at?->format('d/m/Y H:i') ?? '—' }}</div>
                    </x-ds::table-cell>

                    <x-ds::table-cell>
                        <div class="text-sm text-(--text-secondary)">{{ $token->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
                    </x-ds::table-cell>

                    <x-ds::table-cell>
                        <div class="flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-(--status-error)"
                                wire:click.prevent="confirmDelete({{ $token->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>
                    </x-ds::table-cell>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-sm text-(--text-secondary)">
                        {{ __('app.api_tokens.empty') }}
                    </td>
                </tr>
            @endforelse
        </x-ds::table>
    </x-ds::card>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-create-api-token-offcanvas.window="open = true"
        x-on:close-api-token-offcanvas.window="open = false"
        title="{{ __('app.api_tokens.offcanvas.create_title') }}"
        description="{{ __('app.api_tokens.offcanvas.create_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="store" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.api_tokens.form.name') }}" wire:model="name" required :error="$errors->first('name')" />

            <x-ds::textarea
                label="{{ __('app.api_tokens.form.abilities') }}"
                description="{{ __('app.api_tokens.form.abilities_help') }}"
                wire:model="abilitiesText"
                rows="4"
                :error="$errors->first('abilitiesText')"
            />

            @if ($plainToken)
                <x-ds::alert variant="warning" icon="solar:danger-triangle-linear">
                    <div class="space-y-2">
                        <div class="text-sm font-medium text-(--text-primary)">{{ __('app.api_tokens.token_once.title') }}</div>
                        <div class="text-xs text-(--text-secondary)">{{ __('app.api_tokens.token_once.description') }}</div>
                        <div class="mt-2 rounded-md border border-(--border-default) bg-(--surface-card) p-3 font-mono text-sm text-(--text-primary) select-all">{{ $plainToken }}</div>
                    </div>
                </x-ds::alert>
            @endif

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.api_tokens.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="store">
                    <span wire:loading.remove wire:target="store">{{ __('app.api_tokens.form.save') }}</span>
                    <span wire:loading wire:target="store">{{ __('app.api_tokens.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.api_tokens.delete.title') }}"
        size="md"
    >
        <div class="space-y-4">
            <x-ds::alert variant="danger" icon="solar:danger-triangle-linear">
                {{ __('app.api_tokens.delete.warning') }}
            </x-ds::alert>

            <p class="text-sm text-(--text-secondary)">
                {!! __('app.api_tokens.delete.confirm_help', ['word' => '<span class="select-all font-mono font-bold text-(--status-error)">DELETE</span>']) !!}
            </p>

            <x-ds::input
                wire:model.live="deleteConfirmation"
                placeholder="{{ __('app.api_tokens.delete.placeholder', ['word' => 'DELETE']) }}"
                class="border-(--status-error) focus:border-(--status-error) focus:ring-(--status-error)/20"
            />
            @error('deleteConfirmation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.api_tokens.form.cancel') }}</x-ds::button>
                <x-ds::button
                    variant="danger"
                    icon="solar:trash-bin-trash-linear"
                    wire:click.prevent="delete"
                    wire:loading.attr="disabled"
                >
                    {{ __('app.api_tokens.delete.delete_permanently') }}
                </x-ds::button>
            </div>
        </x-slot:footer>
    </x-ds::modal>
</div>
