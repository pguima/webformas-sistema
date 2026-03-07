<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.plans.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.plans.subtitle') }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                type="button"
                icon="solar:add-circle-linear"
                x-on:click="$dispatch('open-create-plan-offcanvas'); $wire.create()"
                wire:loading.attr="disabled"
            >
                {{ __('app.plans.add') }}
            </x-ds::button>
        </div>
    </div>

    <x-ds::card class="mt-6">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input
                    icon="solar:magnifer-linear"
                    placeholder="{{ __('app.plans.search_placeholder') }}"
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-40">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-medium text-(--text-primary)">{{ __('app.plans.per_page') }}</span>
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
                @forelse($plans as $plan)
                    <x-ds::card class="relative p-4" wire:key="grid-{{ $plan->id }}">
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:pen-linear"
                                wire:mouseenter="prefetch({{ $plan->id }})"
                                x-on:click="$dispatch('open-edit-plan-offcanvas'); $wire.edit({{ $plan->id }})"
                                wire:loading.attr="disabled"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-(--status-error)"
                                wire:click.prevent="confirmDelete({{ $plan->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-(--text-primary) truncate">{{ $plan->name }}</div>
                            <div class="mt-1 text-xs text-(--text-muted) truncate">{{ number_format((float) $plan->price, 2, ',', '.') }}</div>
                        </div>

                        <div class="mt-4">
                            <x-ds::badge variant="secondary">{{ __('app.plans.card.services_count', ['count' => $plan->services_count]) }}</x-ds::badge>
                        </div>
                    </x-ds::card>
                @empty
                    <div class="py-8 text-center text-sm text-(--text-secondary) sm:col-span-2 xl:col-span-3">
                        {{ __('app.plans.no_results', ['search' => $search]) }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $plans->links() }}
            </div>
        @else
            <x-ds::table :headers="[__('app.plans.table.name'), __('app.plans.table.price'), __('app.plans.table.services'), __('app.plans.table.actions')]">
                @forelse($plans as $plan)
                    <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="{{ $plan->id }}">
                        <x-ds::table-cell>
                            <div class="text-sm font-medium text-(--text-primary)">{{ $plan->name }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ number_format((float) $plan->price, 2, ',', '.') }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <x-ds::badge variant="secondary">{{ $plan->services_count }}</x-ds::badge>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="flex items-center gap-2">
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    wire:mouseenter="prefetch({{ $plan->id }})"
                                    x-on:click="$dispatch('open-edit-plan-offcanvas'); $wire.edit({{ $plan->id }})"
                                    wire:loading.attr="disabled"
                                />

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-(--status-error)"
                                    wire:click.prevent="confirmDelete({{ $plan->id }})"
                                    wire:loading.attr="disabled"
                                />
                            </div>
                        </x-ds::table-cell>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm text-(--text-secondary)">
                            {{ __('app.plans.no_results', ['search' => $search]) }}
                        </td>
                    </tr>
                @endforelse

                <x-slot:footer>
                    <div class="mt-4">
                        {{ $plans->links() }}
                    </div>
                </x-slot:footer>
            </x-ds::table>
        @endif
    </x-ds::card>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-create-plan-offcanvas.window="open = true"
        x-on:close-plan-offcanvas.window="open = false"
        title="{{ __('app.plans.offcanvas.create_title') }}"
        description="{{ __('app.plans.offcanvas.create_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.plans.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.plans.form.price') }}" wire:model="price" type="number" step="0.01" required :error="$errors->first('price')" />

            <x-ds::select-search
                label="{{ __('app.plans.form.services') }}"
                :multiple="true"
                :options="$serviceOptions"
                placeholder="{{ __('app.plans.form.services_placeholder') }}"
                helper="{{ __('app.plans.form.services_helper') }}"
                :error="$errors->first('service_ids')"
                wireModel="service_ids"
                :wireLive="false"
            />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.plans.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.plans.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.plans.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-edit-plan-offcanvas.window="open = true"
        x-on:close-plan-offcanvas.window="open = false"
        title="{{ __('app.plans.offcanvas.edit_title') }}"
        description="{{ __('app.plans.offcanvas.edit_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.plans.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.plans.form.price') }}" wire:model="price" type="number" step="0.01" required :error="$errors->first('price')" />

            <x-ds::select-search
                label="{{ __('app.plans.form.services') }}"
                :multiple="true"
                :options="$serviceOptions"
                placeholder="{{ __('app.plans.form.services_placeholder') }}"
                helper="{{ __('app.plans.form.services_helper') }}"
                :error="$errors->first('service_ids')"
                wireModel="service_ids"
                :wireLive="false"
            />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.plans.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.plans.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.plans.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.plans.delete.title') }}"
        size="md"
    >
        <div class="space-y-4">
            <x-ds::alert variant="danger" icon="solar:danger-triangle-linear">
                {{ __('app.plans.delete.warning') }}
            </x-ds::alert>

            <p class="text-sm text-(--text-secondary)">
                {!! __('app.plans.delete.confirm_help', ['word' => '<span class="select-all font-mono font-bold text-(--status-error)">DELETE</span>']) !!}
            </p>

            <x-ds::input
                wire:model.live="deleteConfirmation"
                placeholder="{{ __('app.plans.delete.placeholder', ['word' => 'DELETE']) }}"
                class="border-(--status-error) focus:border-(--status-error) focus:ring-(--status-error)/20"
            />
            @error('deleteConfirmation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.plans.form.cancel') }}</x-ds::button>
                <x-ds::button variant="danger" icon="solar:trash-bin-trash-linear" wire:click.prevent="delete" wire:loading.attr="disabled">
                    {{ __('app.plans.delete.delete_permanently') }}
                </x-ds::button>
            </div>
        </x-slot:footer>
    </x-ds::modal>
</div>
