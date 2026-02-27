<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ __('ds.title') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @livewireStyles

    <!-- Theme initialization - prevents flash -->
    <script>
        (() => {
            const stored = localStorage.getItem('ds_theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const theme = stored ?? (prefersDark ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', theme === 'dark');
        })();
    </script>

    <!-- External dependencies -->
    <script defer src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>

<body class="min-h-screen bg-(--surface-page) text-(--text-primary) antialiased">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false, isDark: document.documentElement.classList.contains('dark') }"
        class="min-h-screen">
        <!-- Navbar -->
        <x-ds::navbar />

        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"
            @click="sidebarOpen = false"></div>

        <!-- Main layout -->
        <div class="pt-16 lg:flex">
            <!-- Sidebar spacer -->
            <div class="shrink-0 transition-all duration-200" :class="sidebarCollapsed ? 'lg:w-20' : 'lg:w-60'">
                <x-ds::sidebar />
            </div>

            <!-- Content area -->
            <div class="min-w-0 flex-1">
                <main class="p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>