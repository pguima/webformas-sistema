<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.users.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.users.subtitle') }}</p>
        </div>
        
        <div class="flex gap-2">
            <x-ds::button type="button" variant="secondary" icon="solar:file-download-linear" wire:click="exportCsv" wire:loading.attr="disabled">{{ __('app.users.export_csv') }}</x-ds::button>
            <x-ds::button type="button" variant="secondary" icon="solar:printer-linear" wire:click="exportPdf" wire:loading.attr="disabled">{{ __('app.users.pdf') }}</x-ds::button>
            <x-ds::button type="button" icon="solar:user-plus-linear" x-on:click="$dispatch('open-create-user-offcanvas'); $wire.create()" wire:loading.attr="disabled">{{ __('app.users.add_user') }}</x-ds::button>
        </div>
    </div>

    <!-- Filters & Table -->
    <x-ds::card class="mt-6">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input icon="solar:magnifer-linear" placeholder="{{ __('app.users.search_placeholder') }}" wire:model.live.debounce.300ms="search" />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-40">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-medium text-(--text-primary)">{{ __('app.users.per_page') }}</span>
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
                @forelse($users as $user)
                    <x-ds::card class="relative p-4" wire:key="grid-{{ $user->id }}">
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:pen-linear"
                                wire:mouseenter="prefetch({{ $user->id }})"
                                x-on:click="$dispatch('open-edit-user-offcanvas'); $wire.edit({{ $user->id }})"
                                wire:loading.attr="disabled"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-(--status-error)"
                                wire:click.prevent="confirmDelete({{ $user->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="flex flex-col items-center text-center">
                            @if ($user->avatar_path)
                                <img
                                    src="{{ asset('storage/' . $user->avatar_path) }}"
                                    alt="{{ __('app.profile.avatar_alt') }}"
                                    class="h-12 w-12 rounded-full object-cover border border-(--border-subtle)"
                                    loading="lazy"
                                />
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-(--surface-hover) text-sm font-bold text-(--text-secondary) border border-(--border-subtle)">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                            @endif

                            <div class="mt-3 min-w-0 w-full">
                                <div class="text-sm font-semibold text-(--text-primary) truncate">{{ $user->name }}</div>
                                <div class="text-xs text-(--text-muted) truncate">{{ $user->email }}</div>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-2">
                            @php
                                $roleVariant = match($user->role) {
                                    'SuperAdmin' => 'danger',
                                    'Admin' => 'warning',
                                    'Cliente' => 'primary',
                                    'Funcionário' => 'info',
                                    default => 'secondary',
                                };
                            @endphp
                            <x-ds::badge :variant="$roleVariant">{{ $user->role }}</x-ds::badge>
                            <x-ds::badge variant="{{ $user->status === 'Active' ? 'success' : 'secondary' }}" :dot="true">{{ $user->status }}</x-ds::badge>
                        </div>
                    </x-ds::card>
                @empty
                    <div class="py-8 text-center text-sm text-(--text-secondary) sm:col-span-2 xl:col-span-3">
                        {{ __('app.users.no_results', ['search' => $search]) }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @else
            <x-ds::table :headers="[__('app.users.table.user'), __('app.users.table.role'), __('app.users.table.status'), __('app.users.table.actions')]">
                @forelse($users as $user)
                    <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="{{ $user->id }}">
                        <x-ds::table-cell>
                            <div class="flex items-center gap-3">
                                @if ($user->avatar_path)
                                    <img
                                        src="{{ asset('storage/' . $user->avatar_path) }}"
                                        alt="{{ __('app.profile.avatar_alt') }}"
                                        class="h-9 w-9 rounded-full object-cover border border-(--border-subtle)"
                                        loading="lazy"
                                    />
                                @else
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-(--surface-hover) text-xs font-bold text-(--text-secondary) border border-(--border-subtle)">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-(--text-primary)">{{ $user->name }}</div>
                                    <div class="text-xs text-(--text-muted)">{{ $user->email }}</div>
                                </div>
                            </div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            @php
                                $roleVariant = match($user->role) {
                                    'SuperAdmin' => 'danger',
                                    'Admin' => 'warning',
                                    'Cliente' => 'primary',
                                    'Funcionário' => 'info',
                                    default => 'secondary',
                                };
                            @endphp
                            <x-ds::badge :variant="$roleVariant">{{ $user->role }}</x-ds::badge>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <x-ds::badge variant="{{ $user->status === 'Active' ? 'success' : 'secondary' }}" :dot="true">{{ $user->status }}</x-ds::badge>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="flex items-center gap-2">
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    wire:mouseenter="prefetch({{ $user->id }})"
                                    x-on:click="$dispatch('open-edit-user-offcanvas'); $wire.edit({{ $user->id }})"
                                    wire:loading.attr="disabled"
                                />
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-(--status-error)"
                                    wire:click.prevent="confirmDelete({{ $user->id }})"
                                    wire:loading.attr="disabled"
                                />
                            </div>
                        </x-ds::table-cell>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-sm text-(--text-secondary)">
                            {{ __('app.users.no_results', ['search' => $search]) }}
                        </td>
                    </tr>
                @endforelse

                <x-slot:footer>
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </x-slot:footer>
            </x-ds::table>
        @endif
    </x-ds::card>

    <!-- Create User Offcanvas -->
    <x-ds::offcanvas 
        x-data="{ open: false }"
        x-on:open-create-user-offcanvas.window="open = true"
        x-on:close-user-offcanvas.window="open = false"
        title="{{ __('app.users.offcanvas.create_title') }}"
        description="{{ __('app.users.offcanvas.create_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.users.form.full_name') }}" wire:model="name" required placeholder="John Doe" :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.users.form.email') }}" type="email" wire:model="email" required placeholder="john@company.com" :error="$errors->first('email')" />
            
            <x-ds::select label="{{ __('app.users.form.role') }}" wire:model="role" :options="['Admin' => 'Admin', 'Cliente' => 'Cliente', 'Funcionário' => 'Funcionário']" />
            <x-ds::select label="{{ __('app.users.form.status') }}" wire:model="status" :options="['Active' => 'Active', 'Inactive' => 'Inactive']" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.users.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.users.form.save_user') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.users.form.save_user') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <!-- Edit User Offcanvas -->
    <x-ds::offcanvas 
        x-data="{ open: false }"
        x-on:open-edit-user-offcanvas.window="open = true"
        x-on:close-user-offcanvas.window="open = false"
        title="{{ __('app.users.offcanvas.edit_title') }}"
        description="{{ __('app.users.offcanvas.edit_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.users.form.full_name') }}" wire:model="name" required placeholder="John Doe" :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.users.form.email') }}" type="email" wire:model="email" required placeholder="john@company.com" :error="$errors->first('email')" />

            <x-ds::select label="{{ __('app.users.form.status') }}" wire:model="status" :options="['Active' => 'Active', 'Inactive' => 'Inactive']" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.users.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.users.form.save_user') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.users.form.save_user') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <!-- Delete Modal -->
    <x-ds::modal 
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.users.delete.title') }}"
        size="md"
    >
        <div class="space-y-4">
            <x-ds::alert variant="danger" icon="solar:danger-triangle-linear">
                {{ __('app.users.delete.warning') }}
            </x-ds::alert>
            
            <p class="text-sm text-(--text-secondary)">
                {!! __('app.users.delete.confirm_help', ['word' => '<span class="select-all font-mono font-bold text-(--status-error)">DELETE</span>']) !!}
            </p>

            <x-ds::input 
                wire:model.live="deleteConfirmation" 
                placeholder="{{ __('app.users.delete.placeholder', ['word' => 'DELETE']) }}"
                class="border-(--status-error) focus:border-(--status-error) focus:ring-(--status-error)/20"
            />
            @error('deleteConfirmation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.users.form.cancel') }}</x-ds::button>
                <x-ds::button 
                    variant="danger" 
                    icon="solar:trash-bin-trash-linear" 
                    wire:click.prevent="delete"
                    wire:loading.attr="disabled"
                >
                    {{ __('app.users.delete.delete_permanently') }}
                </x-ds::button>
            </div>
        </x-slot:footer>
    </x-ds::modal>
</div>