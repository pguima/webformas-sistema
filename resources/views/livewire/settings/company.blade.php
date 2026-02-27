<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.settings.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.settings.subtitle') }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button icon="solar:diskette-linear" wire:click="save" wire:loading.attr="disabled">
                {{ __('app.settings.save') }}
            </x-ds::button>
        </div>
    </div>

    @if ($saved)
        <x-ds::alert variant="success" icon="solar:check-circle-linear">
            {{ __('app.settings.saved') }}
        </x-ds::alert>
    @endif

    <x-ds::card>
        <div class="space-y-6">
            <x-ds::input
                :label="__('app.settings.company_name')"
                wire:model="company_name"
                :error="$errors->first('company_name')"
                placeholder="WebFormas"
            />

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="space-y-3">
                    <x-ds::file-upload
                        :label="__('app.settings.logo_light')"
                        name="logo_light"
                        helper="PNG, JPG, SVG (max 2MB)"
                        wire:model="logo_light"
                        :error="$errors->first('logo_light')"
                    />

                    @if ($logo_light)
                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                            <div class="text-xs font-medium text-(--text-secondary)">Preview</div>
                            <img
                                src="{{ $logo_light->temporaryUrl() }}"
                                alt="logo"
                                class="mt-3 h-10 w-auto"
                                loading="lazy"
                            />
                        </div>
                    @endif

                    @if ($current_logo_light_path)
                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                            <div class="text-xs font-medium text-(--text-secondary)">{{ __('app.settings.current') }}</div>
                            <img
                                src="{{ route('company.assets.show', ['asset' => 'logo-light']) }}"
                                alt="logo"
                                class="mt-3 h-10 w-auto"
                                loading="lazy"
                            />
                        </div>
                    @endif
                </div>

                <div class="space-y-3">
                    <x-ds::file-upload
                        :label="__('app.settings.logo_dark')"
                        name="logo_dark"
                        helper="PNG, JPG, SVG (max 2MB)"
                        wire:model="logo_dark"
                        :error="$errors->first('logo_dark')"
                    />

                    @if ($logo_dark)
                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                            <div class="text-xs font-medium text-(--text-secondary)">Preview</div>
                            <img
                                src="{{ $logo_dark->temporaryUrl() }}"
                                alt="logo"
                                class="mt-3 h-10 w-auto"
                                loading="lazy"
                            />
                        </div>
                    @endif

                    @if ($current_logo_dark_path)
                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                            <div class="text-xs font-medium text-(--text-secondary)">{{ __('app.settings.current') }}</div>
                            <img
                                src="{{ route('company.assets.show', ['asset' => 'logo-dark']) }}"
                                alt="logo"
                                class="mt-3 h-10 w-auto"
                                loading="lazy"
                            />
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-3">
                <x-ds::file-upload
                    :label="__('app.settings.favicon')"
                    name="favicon"
                    helper="PNG or ICO (max 2MB)"
                    wire:model="favicon"
                    :error="$errors->first('favicon')"
                />

                @if ($favicon)
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                        <div class="text-xs font-medium text-(--text-secondary)">Preview</div>
                        <img
                            src="{{ $favicon->temporaryUrl() }}"
                            alt="favicon"
                            class="mt-3 h-8 w-8"
                            loading="lazy"
                        />
                    </div>
                @endif

                @if ($current_favicon_path)
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                        <div class="text-xs font-medium text-(--text-secondary)">{{ __('app.settings.current') }}</div>
                        <img
                            src="{{ route('company.assets.show', ['asset' => 'favicon']) }}"
                            alt="favicon"
                            class="mt-3 h-8 w-8"
                            loading="lazy"
                        />
                    </div>
                @endif
            </div>

            <div class="space-y-3">
                <x-ds::file-upload
                    :label="__('app.settings.auth_side_image')"
                    name="auth_side_image"
                    helper="PNG, JPG, WEBP (max 4MB)"
                    wire:model="auth_side_image"
                    :error="$errors->first('auth_side_image')"
                />

                @if ($auth_side_image)
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                        <div class="text-xs font-medium text-(--text-secondary)">Preview</div>
                        <img
                            src="{{ $auth_side_image->temporaryUrl() }}"
                            alt="auth-side"
                            class="mt-3 h-40 w-full rounded-md object-cover"
                            loading="lazy"
                        />
                    </div>
                @endif

                @if ($current_auth_side_image_path)
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                        <div class="text-xs font-medium text-(--text-secondary)">{{ __('app.settings.current') }}</div>
                        <img
                            src="{{ route('company.assets.show', ['asset' => 'auth-side']) }}"
                            alt="auth-side"
                            class="mt-3 h-40 w-full rounded-md object-cover"
                            loading="lazy"
                        />
                    </div>
                @endif
            </div>
        </div>
    </x-ds::card>
</div>
