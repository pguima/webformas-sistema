<header
    class="fixed top-0 z-50 w-full border-b border-(--border-subtle) bg-(--surface-card)/95 backdrop-blur-sm">
    <div class="flex h-16 items-center justify-between gap-4 px-4">
        <!-- Left side -->
        <div class="flex items-center gap-3 min-w-0">
            @php
                $showSidebarToggle = $showSidebarToggle ?? true;
                $brandHref = $brandHref ?? null;
            @endphp

            @if ($showSidebarToggle)
                <!-- Mobile menu button -->
                <button type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-(--border-default) bg-(--surface-hover) text-(--text-secondary) transition-colors duration-150 hover:bg-(--surface-selected) hover:text-(--text-primary) lg:hidden"
                    @click="sidebarOpen = true">
                    <iconify-icon icon="heroicons:bars-3-solid" class="text-lg"></iconify-icon>
                </button>

                <!-- Desktop sidebar toggle -->
                <button type="button"
                    class="hidden lg:inline-flex h-9 w-9 items-center justify-center rounded-md border border-(--border-default) bg-(--surface-hover) text-(--text-secondary) transition-colors duration-150 hover:bg-(--surface-selected) hover:text-(--text-primary)"
                    @click="sidebarCollapsed = !sidebarCollapsed">
                    <iconify-icon x-show="!sidebarCollapsed" x-cloak icon="heroicons:bars-3-solid"
                        class="text-lg"></iconify-icon>
                    <iconify-icon x-show="sidebarCollapsed" x-cloak icon="iconoir:arrow-right"
                        class="text-lg"></iconify-icon>
                </button>
            @endif

            <!-- Brand -->
            <a href="{{ $brandHref ?: (request()->is('design-system*') ? url('/design-system') : url('/profile')) }}"
                class="font-semibold text-(--text-primary) tracking-tight shrink-0 hover:text-(--color-primary) transition-colors duration-150">
                @php
                    $companySettings = \App\Models\CompanySetting::current();
                    $companyName = $companySettings?->company_name;
                    $logoLightPath = $companySettings?->logo_light_path;
                    $logoDarkPath = $companySettings?->logo_dark_path;
                    $logoLightUrl = $logoLightPath ? route('company.assets.show', ['asset' => 'logo-light']) : null;
                    $logoDarkUrl = $logoDarkPath ? route('company.assets.show', ['asset' => 'logo-dark']) : null;
                @endphp

                @if ($logoLightPath || $logoDarkPath)
                    @if ($logoLightUrl)
                        <img x-show="!isDark" x-cloak src="{{ $logoLightUrl }}" alt="{{ $companyName ?: '' }}" class="h-7 w-auto" />
                    @endif
                    @if ($logoDarkUrl)
                        <img x-show="isDark" x-cloak src="{{ $logoDarkUrl }}" alt="{{ $companyName ?: '' }}" class="h-7 w-auto" />
                    @endif
                @else
                    {{ $companyName ?: __('ds.brand') }}
                @endif
            </a>

            
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-2">
            <!-- Theme toggle -->
            <button type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-(--surface-hover) text-(--text-secondary) transition-all duration-150 hover:bg-(--surface-selected) hover:text-(--text-primary)"
                @click="(() => { isDark = document.documentElement.classList.toggle('dark'); localStorage.setItem('ds_theme', isDark ? 'dark' : 'light'); })()">
                <iconify-icon x-show="!isDark" x-cloak icon="ri:moon-line" class="text-xl"></iconify-icon>
                <iconify-icon x-show="isDark" x-cloak icon="ri:sun-line" class="text-xl"></iconify-icon>
            </button>

            <!-- Notifications -->
            <div class="relative hidden" x-data="{ open: false }">
                <button type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-(--surface-hover) text-(--text-secondary) transition-all duration-150 hover:bg-(--surface-selected) hover:text-(--text-primary)"
                    @click="open = !open">
                    <iconify-icon icon="iconoir:bell" class="text-xl"></iconify-icon>
                </button>

                <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    @click.outside="open = false"
                    class="absolute right-0 mt-2 w-72 origin-top-right overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-card) shadow-(--shadow-lg)">
                    <div class="p-3 border-b border-(--border-subtle)">
                        <div class="text-sm font-semibold text-(--text-primary)">
                            {{ __('ds.navbar.notifications') }}
                        </div>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        <a href="javascript:void(0)"
                            class="flex items-start gap-3 px-4 py-3 text-sm text-(--text-secondary) transition-colors duration-150 hover:bg-(--surface-hover)">
                            <span class="h-2 w-2 mt-1.5 rounded-full bg-(--color-primary) shrink-0"></span>
                            <span>{{ __('ds.navbar.notification_item') }}</span>
                        </a>
                    </div>
                    <div class="p-3 border-t border-(--border-subtle)">
                        <a href="#" class="text-sm font-medium text-(--color-primary) hover:underline">{{ __('ds.navbar.view_all') }}</a>
                    </div>
                </div>
            </div>

            <!-- Language -->
            <div class="relative" x-data="{ open: false }">
                <button type="button"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-(--surface-hover) px-3 text-sm font-medium text-(--text-secondary) transition-all duration-150 hover:bg-(--surface-selected) hover:text-(--text-primary)"
                    @click="open = !open">
                    <iconify-icon icon="solar:global-linear" class="text-lg"></iconify-icon>
                    <span class="hidden sm:inline">{{ __('ds.navbar.language') }}</span>
                </button>

                <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false"
                    class="absolute right-0 mt-2 w-44 origin-top-right overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-card) shadow-(--shadow-lg)">
                    <div class="py-2">
                        <a href="{{ route('locale.set', ['locale' => 'pt_BR']) }}"
                            class="block px-4 py-2 text-sm text-(--text-secondary) hover:bg-(--surface-hover) hover:text-(--text-primary)">{{ __('ds.navbar.locales.pt_BR') }}</a>
                        <a href="{{ route('locale.set', ['locale' => 'en']) }}"
                            class="block px-4 py-2 text-sm text-(--text-secondary) hover:bg-(--surface-hover) hover:text-(--text-primary)">{{ __('ds.navbar.locales.en') }}</a>
                        <a href="{{ route('locale.set', ['locale' => 'es']) }}"
                            class="block px-4 py-2 text-sm text-(--text-secondary) hover:bg-(--surface-hover) hover:text-(--text-primary)">{{ __('ds.navbar.locales.es') }}</a>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="relative" x-data="{ open: false }">
                <button type="button"
                    class="inline-flex items-center justify-center rounded-full transition-opacity duration-150 hover:opacity-80"
                    @click="open = !open">
                    @php
                        $user = auth()->user();
                        $name = $user?->name;
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

                    @if($user && $avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $name ?? '' }}"
                            class="h-10 w-10 rounded-full object-cover" />
                    @else
                        <span
                            class="h-10 w-10 rounded-full bg-(--color-primary) inline-flex items-center justify-center text-sm font-semibold text-white">
                            {{ $initials ?: 'U' }}
                        </span>
                    @endif
                </button>

                <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    @click.outside="open = false"
                    class="absolute right-0 mt-2 w-64 origin-top-right overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-card) shadow-(--shadow-lg)">
                    <div class="p-4 border-b border-(--border-subtle)">
                        <div class="flex items-center gap-3">
                            @if($user && $avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="{{ $name ?? '' }}"
                                    class="h-12 w-12 rounded-full object-cover shrink-0" />
                            @else
                                <span
                                    class="h-12 w-12 rounded-full bg-(--color-primary) inline-flex items-center justify-center text-base font-semibold text-white shrink-0">
                                    {{ $initials ?: 'U' }}
                                </span>
                            @endif
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-(--text-primary) truncate">
                                    {{ $name ?: __('ds.navbar.profile_name') }}
                                </div>
                                <div class="text-xs text-(--text-secondary) truncate">
                                    {{ $role ?: __('ds.navbar.profile_role') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="py-2">
                        <a href="{{ url('/profile') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-(--text-secondary) transition-colors duration-150 hover:bg-(--surface-hover) hover:text-(--text-primary)">
                            <iconify-icon icon="solar:user-linear" class="text-xl"></iconify-icon>
                            <span>{{ __('ds.navbar.my_profile') }}</span>
                        </a>
                        <a href="javascript:void(0)"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-(--text-secondary) transition-colors duration-150 hover:bg-(--surface-hover) hover:text-(--text-primary)">
                            <iconify-icon icon="tabler:message-check" class="text-xl"></iconify-icon>
                            <span>{{ __('ds.navbar.inbox') }}</span>
                        </a>
                        <a href="javascript:void(0)"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-(--text-secondary) transition-colors duration-150 hover:bg-(--surface-hover) hover:text-(--text-primary)">
                            <iconify-icon icon="icon-park-outline:setting-two" class="text-xl"></iconify-icon>
                            <span>{{ __('ds.navbar.settings') }}</span>
                        </a>
                    </div>
                    <div class="py-2 border-t border-(--border-subtle)">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-(--status-error) transition-colors duration-150 hover:bg-(--status-error-light)">
                                <iconify-icon icon="lucide:power" class="text-xl"></iconify-icon>
                                <span>{{ __('ds.navbar.logout') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>