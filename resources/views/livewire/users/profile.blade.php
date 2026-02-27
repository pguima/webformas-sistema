<div class="space-y-6">
    @php
        $name = $user?->name;
        $email = $user?->email;
        $role = $user?->role;
        $avatarUrl = $user?->avatar_url;
        $initials = $name
            ? collect(preg_split('/\s+/', trim($name)))
                ->filter()
                ->take(2)
                ->map(fn ($p) => mb_substr($p, 0, 1))
                ->implode('')
            : null;
    @endphp

    @if($saved)
        <x-ds::alert variant="success" icon="solar:check-circle-linear">
            {{ __('app.profile.updated') }}
        </x-ds::alert>
    @endif

    @if($passwordSaved)
        <x-ds::alert variant="success" icon="solar:check-circle-linear">
            {{ __('app.profile.password_updated') }}
        </x-ds::alert>
    @endif

    <x-ds::card>
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4 min-w-0">
                <div class="h-16 w-16 overflow-hidden rounded-full border border-(--border-default) bg-(--surface-hover) shrink-0">
                    @if($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" alt="{{ __('app.profile.avatar_alt') }}" class="h-full w-full object-cover" />
                    @elseif($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $name ?? '' }}" class="h-full w-full object-cover" />
                    @else
                        <div class="flex h-full w-full items-center justify-center text-sm font-semibold text-(--text-secondary)">
                            {{ $initials ?: 'U' }}
                        </div>
                    @endif
                </div>

                <div class="min-w-0">
                    <div class="text-lg font-semibold text-(--text-primary) truncate">
                        {{ $name }}
                    </div>
                    <div class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-(--text-secondary)">
                        <span class="inline-flex items-center gap-2">
                            <iconify-icon icon="solar:letter-linear"></iconify-icon>
                            <span class="truncate">{{ $email }}</span>
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <iconify-icon icon="solar:shield-user-linear"></iconify-icon>
                            <span>{{ $role }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-ds::modal
                    title="{{ __('app.profile.edit_title') }}"
                    description="{{ __('app.profile.edit_description') }}"
                    x-on:profile-saved.window="closeModal()"
                >
                    <x-slot:trigger>
                        <x-ds::button icon="solar:pen-new-square-linear">{{ __('app.profile.edit_button') }}</x-ds::button>
                    </x-slot:trigger>

                    <form wire:submit="saveProfile" class="space-y-5">
                        <div>
                            <div class="mb-3 text-sm font-medium text-(--text-primary)">{{ __('app.profile.picture') }}</div>
                            <div class="flex items-start gap-4">
                                <div class="h-14 w-14 overflow-hidden rounded-full border border-(--border-default) bg-(--surface-hover) shrink-0">
                                    @if($avatar)
                                        <img src="{{ $avatar->temporaryUrl() }}" alt="{{ __('app.profile.avatar_alt') }}" class="h-full w-full object-cover" />
                                    @elseif($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $name ?? '' }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-(--text-secondary)">
                                            {{ $initials ?: 'U' }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1">
                                    <x-ds::file-upload
                                        label="{{ __('app.profile.upload') }}"
                                        name="avatar"
                                        helper="{{ __('app.profile.upload_helper') }}"
                                        :error="$errors->first('avatar')"
                                        wire:model="avatar"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <x-ds::input label="{{ __('app.profile.name') }}" name="name" wire:model.defer="name" :error="$errors->first('name')" />
                            <x-ds::input label="{{ __('app.profile.email') }}" name="email" type="email" wire:model.defer="email" :error="$errors->first('email')" />
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <x-ds::button type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('app.profile.save') }}</span>
                                <span wire:loading>{{ __('app.profile.saving') }}</span>
                            </x-ds::button>
                        </div>
                    </form>
                </x-ds::modal>

                <x-ds::modal
                    title="{{ __('app.profile.change_password_title') }}"
                    description="{{ __('app.profile.change_password_description') }}"
                    x-on:password-saved.window="closeModal()"
                >
                    <x-slot:trigger>
                        <x-ds::button variant="secondary" icon="solar:shield-keyhole-linear">{{ __('app.profile.security_button') }}</x-ds::button>
                    </x-slot:trigger>

                    <form wire:submit="savePassword" class="space-y-4">
                        <x-ds::input
                            label="{{ __('app.profile.current_password') }}"
                            type="password"
                            wire:model.defer="current_password"
                            :error="$errors->first('current_password')"
                        />

                        <x-ds::input
                            label="{{ __('app.profile.new_password') }}"
                            type="password"
                            wire:model.defer="password"
                            :error="$errors->first('password')"
                        />

                        <x-ds::input
                            label="{{ __('app.profile.confirm_new_password') }}"
                            type="password"
                            wire:model.defer="password_confirmation"
                        />

                        <div class="flex items-center justify-end">
                            <x-ds::button type="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('app.profile.update') }}</span>
                                <span wire:loading>{{ __('app.profile.updating') }}</span>
                            </x-ds::button>
                        </div>
                    </form>
                </x-ds::modal>
            </div>
        </div>
    </x-ds::card>
</div>
