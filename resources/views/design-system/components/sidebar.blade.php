@props([
    'menu' => null
])
@php
    // Menu Padrão (Design System)
    $designSystemMenu = [
        [
            'title' => null,
            'items' => [
                ['url' => '/design-system', 'icon' => 'solar:home-smile-angle-outline', 'label' => 'ds.sidebar.dashboard'],
            ]
        ],
        [
            'title' => 'ds.sidebar.groups.application',
            'items' => [
                ['url' => 'javascript:void(0)', 'icon' => 'mage:email', 'label' => 'ds.sidebar.items.email'],
                ['url' => 'javascript:void(0)', 'icon' => 'bi:chat-dots', 'label' => 'ds.sidebar.items.chat'],
            ]
        ],
        [
            'title' => 'ds.sidebar.groups.ui_elements',
            'items' => [
                ['url' => '/design-system/card', 'icon' => 'solar:card-outline', 'label' => 'ds.sidebar.items.card'],
                ['url' => '/design-system/button', 'icon' => 'mdi:gesture-tap-button', 'label' => 'ds.sidebar.items.button'],
                ['url' => '/design-system/badges', 'icon' => 'solar:tag-outline', 'label' => 'ds.sidebar.items.badges'],
                ['url' => '/design-system/alerts', 'icon' => 'mdi:alert-circle-outline', 'label' => 'ds.sidebar.items.alerts'],
                ['url' => '/design-system/toasts', 'icon' => 'solar:notification-unread-outline', 'label' => 'ds.sidebar.items.toasts'],
                ['url' => '/design-system/spinners', 'icon' => 'line-md:loading-twotone-loop', 'label' => 'ds.sidebar.items.spinners'],
                ['url' => '/design-system/tooltips', 'icon' => 'solar:chat-square-like-outline', 'label' => 'ds.sidebar.items.tooltips'],
                ['url' => '/design-system/accordion', 'icon' => 'solar:menu-dots-square-outline', 'label' => 'ds.sidebar.items.accordion'],
                ['url' => '/design-system/tabs', 'icon' => 'solar:sidebar-minimalistic-outline', 'label' => 'ds.sidebar.items.tabs'],
                ['url' => '/design-system/tags', 'icon' => 'solar:tag-outline', 'label' => 'ds.sidebar.items.tags'],
                ['url' => '/design-system/modals', 'icon' => 'solar:window-frame-outline', 'label' => 'ds.sidebar.items.modals'],
                ['url' => '/design-system/offcanvas', 'icon' => 'solar:sidebar-outline', 'label' => 'ds.sidebar.items.offcanvas'],
                ['url' => '/design-system/tables', 'icon' => 'solar:list-check-linear', 'label' => 'ds.sidebar.items.tables'],
                ['url' => '/design-system/forms', 'icon' => 'solar:checklist-minimalistic-linear', 'label' => 'ds.sidebar.items.forms'],
                ['url' => '/design-system/forms-advanced', 'icon' => 'solar:checklist-linear', 'label' => 'ds.sidebar.items.forms_advanced'],
                ['url' => '/design-system/links', 'icon' => 'solar:link-circle-linear', 'label' => 'ds.sidebar.items.links'],
                ['url' => '/design-system/kanban', 'icon' => 'solar:notes-minimalistic-linear', 'label' => 'ds.sidebar.items.kanban'],
            ]
        ]
    ];

    $finalMenu = $menu ?? $designSystemMenu;
@endphp

<aside
    class="fixed left-0 top-16 z-50 h-[calc(100vh-4rem)] w-60 -translate-x-full overflow-hidden border-r border-(--border-subtle) bg-(--surface-sidebar) transition-[transform,width] duration-200 ease-out lg:sticky lg:translate-x-0"
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        sidebarCollapsed ? 'lg:w-20' : 'lg:w-60'
    ]">
    <div class="flex h-14 items-center justify-between px-4 lg:hidden">
        <div class="text-sm font-medium text-(--text-secondary)">
            {{ __('ds.sidebar.title') }}
        </div>
        <button type="button"
            class="rounded-md border border-(--border-default) bg-(--surface-hover) px-2 py-1 text-sm text-(--text-primary) hover:bg-(--surface-selected)"
            @click="sidebarOpen = false">
            {{ __('ds.actions.close') }}
        </button>
    </div>

<div class="h-full overflow-y-auto px-3 py-4" :class="sidebarCollapsed ? 'lg:px-2' : ''">
    @foreach($finalMenu as $group)
           <div class="mb-6">
                @if(isset($group['title']) && $group['title'])
                     <div class="mb-2 px-3 text-xs font-semibold uppercase tracking-wider text-(--text-muted)"
                        :class="sidebarCollapsed ? 'lg:hidden' : ''">
                            {{ __($group['title']) }}
                            </div>
                @endif
                <div class="space-y-0.5">
                @foreach($group['items'] as $element)
                    @php
                        $path = ltrim($element['url'], '/');
                        $isJs = str_starts_with($element['url'], 'javascript');
                        // Custom logic: if url is /dashboard, exact match. else recursive.
                        if ($path === 'dashboard') {
                            $isActive = request()->is('dashboard');
                        } else {
                            $isActive = !$isJs && (request()->is($path) || request()->is($path . '/*'));
                        }

                        $baseClass = "flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-all duration-150";
                        $stateClass = $isActive
                            ? "bg-(--color-primary)/10 text-(--color-primary) font-medium"
                            : "text-(--text-secondary) hover:bg-(--surface-hover) hover:text-(--color-primary)";

                        $iconWrapperClass = "inline-flex h-8 w-8 items-center justify-center rounded-md bg-(--surface-hover)";
                        $iconColorClass = $isActive ? "text-(--color-primary)" : "text-current group-hover:text-(--color-primary)";
                    @endphp

                        <a href="{{ str_starts_with($element['url'], 'http') ? $element['url'] : url($element['url']) }}"
                                    class="group {{ $baseClass }} {{ $stateClass }}"
                            :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''" 
                            @if(!$isJs) wire:navigate @endif>
                            <span class="{{ $iconWrapperClass }} {{ $iconColorClass }}">
                                <iconify-icon icon="{{ $element['icon'] }}" class="text-lg"></iconify-icon>
                            </span>
                                <span :class="sidebarCollapsed ? 'lg:hidden' : ''">{{ __($element['label']) }}</span>
                            </a>
                @endforeach
                        </div>
                    </div>
    @endforeach
    </div>
</aside>