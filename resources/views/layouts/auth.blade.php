<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ isDark: document.documentElement.classList.contains('dark') }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Auth')</title>

    @php
        $companySettings = \App\Models\CompanySetting::current();
        $companyName = $companySettings?->company_name;
        $logoLightUrl = $companySettings?->logo_light_path ? route('company.assets.show', ['asset' => 'logo-light']) : null;
        $logoDarkUrl = $companySettings?->logo_dark_path ? route('company.assets.show', ['asset' => 'logo-dark']) : null;
        $faviconUrl = $companySettings?->favicon_path ? route('company.assets.show', ['asset' => 'favicon']) : null;
        $authSideImageUrl = $companySettings?->auth_side_image_path ? route('company.assets.show', ['asset' => 'auth-side']) : null;
    @endphp

    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
    @endif

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script>
        (() => {
            const stored = localStorage.getItem('ds_theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = stored ?? (prefersDark ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>

    <script>
        (() => {
            const syncThemeState = () => {
                if (window.Alpine) {
                    window.Alpine.store('ds_theme_state', {
                        isDark: document.documentElement.classList.contains('dark'),
                    });
                }
            };

            const observer = new MutationObserver(() => {
                if (document.documentElement.__x) {
                    document.documentElement.__x.$data.isDark = document.documentElement.classList.contains('dark');
                }
                syncThemeState();
            });

            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        })();
    </script>

    <script defer src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>
<body class="min-h-screen bg-(--surface-page) text-(--text-primary) antialiased">
    <main class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
        @if ($authSideImageUrl)
            <section class="hidden lg:block relative overflow-hidden bg-(--surface-card) border-r border-(--border-subtle)">
                <img src="{{ $authSideImageUrl }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" />
            </section>
        @else
            <section class="hidden lg:block bg-(--surface-card) border-r border-(--border-subtle)"></section>
        @endif

        <section class="flex items-center justify-center p-6 lg:p-10">
            <div class="w-full max-w-md">
                @if ($logoLightUrl || $logoDarkUrl)
                    <div class="mb-6 flex justify-center">
                        @if ($logoLightUrl)
                            <img src="{{ $logoLightUrl }}" alt="{{ $companyName ?: '' }}" class="h-10 w-auto dark:hidden" />
                        @endif
                        @if ($logoDarkUrl)
                            <img src="{{ $logoDarkUrl }}" alt="{{ $companyName ?: '' }}" class="h-10 w-auto hidden dark:block" />
                        @endif
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
