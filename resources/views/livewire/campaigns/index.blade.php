<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.campaigns.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.campaigns.subtitle') }}</p>
        </div>
    </div>

    <x-ds::card class="mt-6">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input
                    icon="solar:magnifer-linear"
                    placeholder="{{ __('app.campaigns.search_placeholder') }}"
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-40">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-medium text-(--text-primary)">{{ __('app.campaigns.per_page') }}</span>
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
                @forelse($campaigns as $campaign)
                    <x-ds::card class="relative p-4" wire:key="grid-{{ $campaign->id }}">
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <x-ds::button
                                href="/campaigns/{{ $campaign->id }}"
                                size="icon"
                                variant="ghost"
                                icon="solar:eye-linear"
                                wire:navigate
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:pen-linear"
                                wire:mouseenter="prefetch({{ $campaign->id }})"
                                x-on:click="$dispatch('open-edit-campaign-offcanvas'); $wire.edit({{ $campaign->id }})"
                                wire:loading.attr="disabled"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-(--status-error)"
                                wire:click.prevent="confirmDelete({{ $campaign->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-(--text-primary) truncate">{{ $campaign->client?->name }}</div>
                            <div class="mt-1 text-xs text-(--text-muted) truncate">{{ __('app.campaigns.fields.manager_customer_id') }}: {{ $campaign->manager_customer_id ?: __('app.common.dash') }}</div>
                            <div class="mt-1 text-xs text-(--text-muted) truncate">{{ __('app.campaigns.fields.client_customer_id') }}: {{ $campaign->client_customer_id ?: __('app.common.dash') }}</div>
                        </div>
                    </x-ds::card>
                @empty
                    <div class="py-8 text-center text-sm text-(--text-secondary) sm:col-span-2 xl:col-span-3">
                        {{ __('app.campaigns.no_results', ['search' => $search]) }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $campaigns->links() }}
            </div>
        @else
            <x-ds::table :headers="[__('app.campaigns.table.client'), __('app.campaigns.fields.manager_customer_id'), __('app.campaigns.fields.client_customer_id'), __('app.campaigns.table.actions')]">
                @forelse($campaigns as $campaign)
                    <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="{{ $campaign->id }}">
                        <x-ds::table-cell>
                            <div class="text-sm font-medium text-(--text-primary)">{{ $campaign->client?->name }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ $campaign->manager_customer_id ?: __('app.common.dash') }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ $campaign->client_customer_id ?: __('app.common.dash') }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="flex items-center gap-2">
                                <x-ds::button
                                    href="/campaigns/{{ $campaign->id }}"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:eye-linear"
                                    wire:navigate
                                />

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    wire:mouseenter="prefetch({{ $campaign->id }})"
                                    x-on:click="$dispatch('open-edit-campaign-offcanvas'); $wire.edit({{ $campaign->id }})"
                                    wire:loading.attr="disabled"
                                />

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-(--status-error)"
                                    wire:click.prevent="confirmDelete({{ $campaign->id }})"
                                    wire:loading.attr="disabled"
                                />
                            </div>
                        </x-ds::table-cell>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm text-(--text-secondary)">
                            {{ __('app.campaigns.no_results', ['search' => $search]) }}
                        </td>
                    </tr>
                @endforelse

                <x-slot:footer>
                    <div class="mt-4">
                        {{ $campaigns->links() }}
                    </div>
                </x-slot:footer>
            </x-ds::table>
        @endif
    </x-ds::card>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-edit-campaign-offcanvas.window="open = true"
        x-on:close-campaign-offcanvas.window="open = false"
        title="{{ __('app.campaigns.offcanvas.edit_title') }}"
        description="{{ __('app.campaigns.offcanvas.edit_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::select
                label="{{ __('app.campaigns.table.client') }}"
                :options="$clientOptions"
                placeholder="{{ __('app.campaigns.form.client_placeholder') }}"
                wire:model="client_id"
                required
                :error="$errors->first('client_id')"
            />

            <x-ds::input label="{{ __('app.campaigns.fields.manager_customer_id') }}" wire:model="manager_customer_id" :error="$errors->first('manager_customer_id')" />
            <x-ds::input label="{{ __('app.campaigns.fields.client_customer_id') }}" wire:model="client_customer_id" :error="$errors->first('client_customer_id')" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.campaigns.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.campaigns.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.campaigns.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.campaigns.delete.title') }}"
        description="{{ __('app.campaigns.delete.description') }}"
    >
        <div class="space-y-4">
            <x-ds::input
                label="{{ __('app.campaigns.delete.confirmation_label') }}"
                placeholder="{{ __('app.campaigns.delete.placeholder', ['word' => 'DELETE']) }}"
                wire:model="deleteConfirmation"
                :error="$errors->first('deleteConfirmation')"
            />

            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" x-on:click="closeModal()">{{ __('app.campaigns.delete.cancel') }}</x-ds::button>
                <x-ds::button type="button" variant="danger" wire:click="delete" wire:loading.attr="disabled">
                    {{ __('app.campaigns.delete.confirm') }}
                </x-ds::button>
            </div>
        </div>
    </x-ds::modal>
</div>
