<div class="space-y-6"
    x-data="{
        async copyJson(id) {
            try {
                const res = await fetch(`/widgets/${id}/json`, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!res.ok) throw new Error('request_failed');

                const text = await res.text();

                try {
                    await navigator.clipboard.writeText(text);
                } catch (e) {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.setAttribute('readonly', '');
                    textarea.style.position = 'fixed';
                    textarea.style.left = '-9999px';
                    document.body.appendChild(textarea);
                    textarea.select();

                    const ok = document.execCommand('copy');
                    document.body.removeChild(textarea);

                    if (!ok) throw new Error('clipboard_failed');
                }

                $wire.dispatch('notify', { message: @js(__('app.widgets.messages.json_copied')), variant: 'success', title: @js(__('app.widgets.messages.success_title')) });
            } catch (e) {
                const isRequestError = e && e.message === 'request_failed';
                const msg = isRequestError
                    ? @js(__('app.widgets.messages.error_not_found'))
                    : @js(__('app.widgets.messages.copy_failed'));

                $wire.dispatch('notify', { message: msg, variant: 'danger', title: @js(__('app.widgets.messages.error_title')) });
            }
        },
    }"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.widgets.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.widgets.subtitle') }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                type="button"
                icon="solar:add-circle-linear"
                x-on:click="$dispatch('open-create-widget-offcanvas'); $wire.create()"
                wire:loading.attr="disabled"
            >
                {{ __('app.widgets.add') }}
            </x-ds::button>
        </div>
    </div>

    <x-ds::card class="mt-6">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input
                    icon="solar:magnifer-linear"
                    placeholder="{{ __('app.widgets.search_placeholder') }}"
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-40">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-medium text-(--text-primary)">{{ __('app.widgets.per_page') }}</span>
                        <x-ds::select
                            wire:model.live="perPage"
                            :options="[
                                ['value' => 12, 'label' => '12'],
                                ['value' => 24, 'label' => '24'],
                                ['value' => 52, 'label' => '52'],
                                ['value' => 104, 'label' => '104'],
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
                @forelse($widgets as $widget)
                    <x-ds::card class="relative overflow-hidden p-0 shadow-(--shadow-md) border border-(--border-default)" wire:key="grid-{{ $widget->id }}">
                        <div class="absolute bottom-3 right-3 z-10 flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:copy-linear"
                                x-on:click="copyJson({{ $widget->id }})"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:pen-linear"
                                wire:mouseenter="prefetch({{ $widget->id }})"
                                x-on:click="$dispatch('open-edit-widget-offcanvas'); $wire.edit({{ $widget->id }})"
                                wire:loading.attr="disabled"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-(--status-error)"
                                wire:click.prevent="confirmDelete({{ $widget->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="h-36 w-full bg-(--surface-hover) border-b border-(--border-subtle)">
                            @if ($widget->image_url)
                                <img
                                    src="{{ $widget->image_url }}"
                                    alt="{{ $widget->name }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                />
                            @else
                                <div class="flex h-full w-full items-center justify-center text-xs font-medium text-(--text-muted)">
                                    {{ __('app.widgets.grid.no_image') }}
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-(--text-primary) truncate">{{ $widget->name }}</div>
                                <div class="mt-0.5 text-xs text-(--text-muted) truncate">{{ $widget->author }}</div>
                            </div>

                            <div class="mt-3">
                                <x-ds::badge variant="secondary">{{ $widget->category }}</x-ds::badge>
                            </div>
                        </div>
                    </x-ds::card>
                @empty
                    <div class="py-8 text-center text-sm text-(--text-secondary) sm:col-span-2 xl:col-span-3">
                        {{ __('app.widgets.no_results', ['search' => $search]) }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $widgets->links() }}
            </div>
        @else
            <x-ds::table :headers="[__('app.widgets.table.widget'), __('app.widgets.table.category'), __('app.widgets.table.updated_at'), __('app.widgets.table.actions')]">
                @forelse($widgets as $widget)
                    <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="{{ $widget->id }}">
                        <x-ds::table-cell>
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 overflow-hidden rounded-md border border-(--border-subtle) bg-(--surface-hover) shrink-0">
                                    @if ($widget->image_url)
                                        <img
                                            src="{{ $widget->image_url }}"
                                            alt="{{ $widget->name }}"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                        />
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-(--text-primary) truncate">{{ $widget->name }}</div>
                                    <div class="text-xs text-(--text-muted) truncate">{{ $widget->author }}</div>
                                </div>
                            </div>
                        </x-ds::table-cell>

                        <x-ds::table-cell>
                            <x-ds::badge variant="secondary">{{ $widget->category }}</x-ds::badge>
                        </x-ds::table-cell>

                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ optional($widget->updated_at)->format('d/m/Y H:i') }}</div>
                        </x-ds::table-cell>

                        <x-ds::table-cell>
                            <div class="flex items-center gap-2">
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:copy-linear"
                                    x-on:click="copyJson({{ $widget->id }})"
                                />

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    wire:mouseenter="prefetch({{ $widget->id }})"
                                    x-on:click="$dispatch('open-edit-widget-offcanvas'); $wire.edit({{ $widget->id }})"
                                    wire:loading.attr="disabled"
                                />

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-(--status-error)"
                                    wire:click.prevent="confirmDelete({{ $widget->id }})"
                                    wire:loading.attr="disabled"
                                />
                            </div>
                        </x-ds::table-cell>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm text-(--text-secondary)">
                            {{ __('app.widgets.no_results', ['search' => $search]) }}
                        </td>
                    </tr>
                @endforelse

                <x-slot:footer>
                    <div class="mt-4">
                        {{ $widgets->links() }}
                    </div>
                </x-slot:footer>
            </x-ds::table>
        @endif
    </x-ds::card>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-create-widget-offcanvas.window="open = true"
        x-on:close-widget-offcanvas.window="open = false"
        title="{{ __('app.widgets.offcanvas.create_title') }}"
        description="{{ __('app.widgets.offcanvas.create_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.widgets.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.widgets.form.author') }}" wire:model="author" required :error="$errors->first('author')" />

            <x-ds::select
                label="{{ __('app.widgets.form.category') }}"
                wire:model="category"
                :options="$categoryOptions"
                :error="$errors->first('category')"
            />

            <x-ds::file-upload
                label="{{ __('app.widgets.form.image') }}"
                name="image"
                helper="{{ __('app.widgets.form.image_helper') }}"
                wire:model="image"
                :error="$errors->first('image')"
            />

            @if ($image)
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                    <div class="text-xs font-medium text-(--text-secondary)">{{ __('app.widgets.form.preview') }}</div>
                    <img
                        src="{{ $image->temporaryUrl() }}"
                        alt="{{ __('app.widgets.form.image') }}"
                        class="mt-3 h-32 w-full rounded-md object-cover"
                        loading="lazy"
                    />
                </div>
            @endif

            <x-ds::textarea
                label="{{ __('app.widgets.form.json_code') }}"
                wire:model="json_code"
                rows="10"
                required
                :error="$errors->first('json_code')"
                class="font-mono"
            />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.widgets.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.widgets.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.widgets.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-edit-widget-offcanvas.window="open = true"
        x-on:close-widget-offcanvas.window="open = false"
        title="{{ __('app.widgets.offcanvas.edit_title') }}"
        description="{{ __('app.widgets.offcanvas.edit_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.widgets.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.widgets.form.author') }}" wire:model="author" required :error="$errors->first('author')" />

            <x-ds::select
                label="{{ __('app.widgets.form.category') }}"
                wire:model="category"
                :options="$categoryOptions"
                :error="$errors->first('category')"
            />

            <x-ds::file-upload
                label="{{ __('app.widgets.form.image') }}"
                name="image"
                helper="{{ __('app.widgets.form.image_helper') }}"
                wire:model="image"
                :error="$errors->first('image')"
            />

            @if ($image)
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                    <div class="text-xs font-medium text-(--text-secondary)">{{ __('app.widgets.form.preview') }}</div>
                    <img
                        src="{{ $image->temporaryUrl() }}"
                        alt="{{ __('app.widgets.form.image') }}"
                        class="mt-3 h-32 w-full rounded-md object-cover"
                        loading="lazy"
                    />
                </div>
            @endif

            <x-ds::textarea
                label="{{ __('app.widgets.form.json_code') }}"
                wire:model="json_code"
                rows="10"
                required
                :error="$errors->first('json_code')"
                class="font-mono"
            />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.widgets.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.widgets.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.widgets.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.widgets.delete.title') }}"
        size="md"
    >
        <div class="space-y-4">
            <x-ds::alert variant="danger" icon="solar:danger-triangle-linear">
                {{ __('app.widgets.delete.warning') }}
            </x-ds::alert>

            <p class="text-sm text-(--text-secondary)">
                {!! __('app.widgets.delete.confirm_help', ['word' => '<span class="select-all font-mono font-bold text-(--status-error)">DELETE</span>']) !!}
            </p>

            <x-ds::input
                wire:model.live="deleteConfirmation"
                placeholder="{{ __('app.widgets.delete.placeholder', ['word' => 'DELETE']) }}"
                class="border-(--status-error) focus:border-(--status-error) focus:ring-(--status-error)/20"
            />
            @error('deleteConfirmation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.widgets.form.cancel') }}</x-ds::button>
                <x-ds::button
                    variant="danger"
                    icon="solar:trash-bin-trash-linear"
                    wire:click.prevent="delete"
                    wire:loading.attr="disabled"
                >
                    {{ __('app.widgets.delete.delete_permanently') }}
                </x-ds::button>
            </div>
        </x-slot:footer>
    </x-ds::modal>
</div>
