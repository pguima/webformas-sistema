@extends('layouts.layout-ds')

@section('content')
    <div>
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-(--text-primary)">
                    {{ __('ds.pages.tables.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--text-secondary)">
                    {{ __('ds.pages.tables.subtitle') }}
                </div>
            </div>
            
            <div class="flex gap-2">
                <x-ds::button variant="secondary" icon="solar:download-linear">Export</x-ds::button>
                <x-ds::button icon="solar:add-circle-linear">New Entry</x-ds::button>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8">
            <!-- Basic Table -->
            <x-ds::card :title="__('ds.pages.tables.sections.basic')" :description="__('ds.pages.tables.sections.basic_desc')">
                <div class="mt-4">
                    <x-ds::table :headers="[
                        __('ds.pages.tables.headers.user'),
                        __('ds.pages.tables.headers.role'),
                        __('ds.pages.tables.headers.status'),
                        __('ds.pages.tables.headers.last_login')
                    ]">
                        @foreach([
                            ['name' => 'Alice Freeman', 'email' => 'alice@example.com', 'role' => 'Admin', 'status' => 'active', 'login' => '2 min ago'],
                            ['name' => 'Bob Smith', 'email' => 'bob@example.com', 'role' => 'Editor', 'status' => 'offline', 'login' => '4 hours ago'],
                            ['name' => 'Charlie Kim', 'email' => 'charlie@example.com', 'role' => 'Viewer', 'status' => 'active', 'login' => '1 day ago'],
                        ] as $user)
                            <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)">
                                <x-ds::table-cell variant="bold">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-(--surface-hover) text-xs font-bold text-(--text-secondary)">
                                            {{ substr($user['name'], 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-(--text-primary)">{{ $user['name'] }}</div>
                                            <div class="text-xs text-(--text-muted)">{{ $user['email'] }}</div>
                                        </div>
                                    </div>
                                </x-ds::table-cell>
                                <x-ds::table-cell>{{ $user['role'] }}</x-ds::table-cell>
                                <x-ds::table-cell>
                                    @if($user['status'] === 'active')
                                        <x-ds::badge variant="success" size="sm" :dot="true">Active</x-ds::badge>
                                    @else
                                        <x-ds::badge variant="secondary" size="sm" :dot="true">Offline</x-ds::badge>
                                    @endif
                                </x-ds::table-cell>
                                <x-ds::table-cell variant="muted">{{ $user['login'] }}</x-ds::table-cell>
                            </tr>
                        @endforeach
                    </x-ds::table>
                </div>
            </x-ds::card>

            <!-- Projects Table (Rich Content) -->
            <x-ds::card :title="__('ds.pages.tables.sections.projects')" :description="__('ds.pages.tables.sections.projects_desc')">
                <div class="mt-4">
                    <x-ds::table :headers="['Project', 'Progress', 'Team', 'Due Date', 'Actions']">
                        <!-- Row 1 -->
                        <tr class="border-b border-(--border-subtle) hover:bg-(--surface-hover)">
                            <x-ds::table-cell>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded bg-(--tag-purple) text-(--tag-purple-text)">
                                        <iconify-icon icon="solar:figma-linear" class="text-lg"></iconify-icon>
                                    </span>
                                    <span class="font-medium text-(--text-primary)">Website Redesign</span>
                                </div>
                            </x-ds::table-cell>
                            <x-ds::table-cell>
                                <div class="w-32">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span>75%</span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-(--surface-hover)">
                                        <div class="h-full rounded-full bg-(--color-primary)" style="width: 75%"></div>
                                    </div>
                                </div>
                            </x-ds::table-cell>
                            <x-ds::table-cell>
                                <div class="flex -space-x-2">
                                    <img class="h-7 w-7 rounded-full border-2 border-(--surface-card)" src="https://ui-avatars.com/api/?name=A+B&background=random" alt="">
                                    <img class="h-7 w-7 rounded-full border-2 border-(--surface-card)" src="https://ui-avatars.com/api/?name=C+D&background=random" alt="">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full border-2 border-(--surface-card) bg-(--surface-hover) text-xs text-(--text-secondary)">+3</span>
                                </div>
                            </x-ds::table-cell>
                            <x-ds::table-cell variant="muted">Oct 24, 2024</x-ds::table-cell>
                            <x-ds::table-cell>
                                <div class="flex items-center gap-2">
                                    <x-ds::button variant="ghost" size="icon" icon="solar:pen-linear"></x-ds::button>
                                    <x-ds::button variant="ghost" size="icon" icon="solar:trash-bin-trash-linear" class="text-(--status-error) hover:bg-(--status-error-light)"></x-ds::button>
                                </div>
                            </x-ds::table-cell>
                        </tr>

                        <!-- Row 2 -->
                        <tr class="border-b border-(--border-subtle) hover:bg-(--surface-hover)">
                            <x-ds::table-cell>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded bg-(--tag-blue) text-(--tag-blue-text)">
                                        <iconify-icon icon="solar:code-circle-linear" class="text-lg"></iconify-icon>
                                    </span>
                                    <span class="font-medium text-(--text-primary)">Mobile App</span>
                                </div>
                            </x-ds::table-cell>
                            <x-ds::table-cell>
                                <div class="w-32">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span>45%</span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-(--surface-hover)">
                                        <div class="h-full rounded-full bg-(--status-info)" style="width: 45%"></div>
                                    </div>
                                </div>
                            </x-ds::table-cell>
                            <x-ds::table-cell>
                                <div class="flex -space-x-2">
                                    <img class="h-7 w-7 rounded-full border-2 border-(--surface-card)" src="https://ui-avatars.com/api/?name=E+F&background=random" alt="">
                                </div>
                            </x-ds::table-cell>
                            <x-ds::table-cell variant="muted">Nov 12, 2024</x-ds::table-cell>
                            <x-ds::table-cell>
                                <div class="flex items-center gap-2">
                                    <x-ds::button variant="ghost" size="icon" icon="solar:pen-linear"></x-ds::button>
                                    <x-ds::button variant="ghost" size="icon" icon="solar:trash-bin-trash-linear" class="text-(--status-error) hover:bg-(--status-error-light)"></x-ds::button>
                                </div>
                            </x-ds::table-cell>
                        </tr>
                        
                         <!-- Row 3 -->
                        <tr class="border-b border-(--border-subtle) hover:bg-(--surface-hover)">
                            <x-ds::table-cell>
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded bg-(--tag-green) text-(--tag-green-text)">
                                        <iconify-icon icon="solar:chart-square-linear" class="text-lg"></iconify-icon>
                                    </span>
                                    <span class="font-medium text-(--text-primary)">Marketing Campaign</span>
                                </div>
                            </x-ds::table-cell>
                            <x-ds::table-cell>
                                <div class="w-32">
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span>100%</span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-(--surface-hover)">
                                        <div class="h-full rounded-full bg-(--status-success)" style="width: 100%"></div>
                                    </div>
                                </div>
                            </x-ds::table-cell>
                            <x-ds::table-cell>
                                <div class="flex -space-x-2">
                                    <img class="h-7 w-7 rounded-full border-2 border-(--surface-card)" src="https://ui-avatars.com/api/?name=G+H&background=random" alt="">
                                    <img class="h-7 w-7 rounded-full border-2 border-(--surface-card)" src="https://ui-avatars.com/api/?name=I+J&background=random" alt="">
                                </div>
                            </x-ds::table-cell>
                            <x-ds::table-cell variant="muted">Sep 30, 2024</x-ds::table-cell>
                            <x-ds::table-cell>
                                <div class="flex items-center gap-2">
                                    <x-ds::badge variant="success" style="soft">Completed</x-ds::badge>
                                </div>
                            </x-ds::table-cell>
                        </tr>
                    </x-ds::table>
                </div>
            </x-ds::card>

            <!-- Selection and Pagination -->
            <x-ds::card :title="__('ds.pages.tables.sections.selection')" :description="__('ds.pages.tables.sections.selection_desc')">
                 <div class="mt-4">
                    <x-ds::table :headers="['Invoice', 'Client', 'Amount', 'Status']" :checkbox="true">
                        @foreach([
                            ['id' => 'INV-001', 'client' => 'Acme Corp', 'amount' => '$1,200.00', 'status' => 'paid'],
                            ['id' => 'INV-002', 'client' => 'Global Inc', 'amount' => '$3,450.00', 'status' => 'pending'],
                            ['id' => 'INV-003', 'client' => 'Tech Solutions', 'amount' => '$900.00', 'status' => 'overdue'],
                        ] as $inv)
                            <tr class="border-b border-(--border-subtle) hover:bg-(--surface-hover) group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" class="h-4 w-4 rounded border-(--border-default) bg-(--surface-card) text-(--color-primary) focus:ring-(--color-primary)/20">
                                    </div>
                                </td>
                                <x-ds::table-cell>
                                    <span class="font-mono font-medium text-(--text-primary)">{{ $inv['id'] }}</span>
                                </x-ds::table-cell>
                                <x-ds::table-cell>{{ $inv['client'] }}</x-ds::table-cell>
                                <x-ds::table-cell variant="bold">{{ $inv['amount'] }}</x-ds::table-cell>
                                <x-ds::table-cell>
                                     @if($inv['status'] === 'paid')
                                        <x-ds::tag variant="green" style="outline" :dot="true">Paid</x-ds::tag>
                                    @elseif($inv['status'] === 'pending')
                                        <x-ds::tag variant="warning" style="outline" :dot="true">Pending</x-ds::tag>
                                    @else
                                        <x-ds::tag variant="primary" style="outline" :dot="true">Overdue</x-ds::tag>
                                    @endif
                                </x-ds::table-cell>
                            </tr>
                        @endforeach

                         <x-slot:footer>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-(--text-muted)">
                                    Showing <span class="font-medium text-(--text-primary)">1</span> to <span class="font-medium text-(--text-primary)">3</span> of <span class="font-medium text-(--text-primary)">12</span> results
                                </div>
                                <div class="flex items-center gap-1">
                                    <x-ds::button variant="outline" size="sm" icon="solar:arrow-left-linear" disabled>Previous</x-ds::button>
                                    <x-ds::button variant="outline" size="sm">1</x-ds::button>
                                    <x-ds::button variant="ghost" size="sm">2</x-ds::button>
                                    <x-ds::button variant="ghost" size="sm">...</x-ds::button>
                                    <x-ds::button variant="ghost" size="sm">8</x-ds::button>
                                    <x-ds::button variant="outline" size="sm" icon="solar:arrow-right-linear">Next</x-ds::button>
                                </div>
                            </div>
                        </x-slot:footer>
                    </x-ds::table>
                </div>
            </x-ds::card>

             <!-- Empty State -->
            <x-ds::card :title="__('ds.pages.tables.sections.empty')" :description="__('ds.pages.tables.sections.empty_desc')">
                <div class="mt-4">
                    <x-ds::table :headers="['Task', 'Assignee', 'Status', 'Due Date']">
                        <!-- No rows -->
                        <x-slot:footer>
                             <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-(--surface-hover)">
                                    <iconify-icon icon="solar:clipboard-remove-linear" class="text-2xl text-(--text-muted)"></iconify-icon>
                                </div>
                                <h3 class="mt-3 text-sm font-semibold text-(--text-primary)">No tasks found</h3>
                                <p class="mt-1 text-sm text-(--text-secondary)">Get started by creating a new task.</p>
                                <div class="mt-4">
                                     <x-ds::button size="sm" icon="solar:add-circle-linear">Create Task</x-ds::button>
                                </div>
                            </div>
                        </x-slot:footer>
                    </x-ds::table>
                </div>
            </x-ds::card>

            <!-- Documentation -->
            <div class="mt-12">
                <div class="mb-6 text-sm font-semibold text-(--text-secondary)">
                    {{ __('ds.pages.tables.docs.title') }}
                </div>

                <x-ds::card class="h-full" :title="__('ds.pages.tables.docs.usage_title')" :description="__('ds.pages.tables.docs.usage_subtitle')">
                    <div class="mt-4 grid grid-cols-1 gap-6">
                        <div>
                             <div class="text-sm font-semibold text-(--text-primary)">{{ __('ds.pages.tables.docs.example_code_title') }}</div>
                            <div class="mt-3 overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-hover)">
                                <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::table :headers="['Name', 'Email', 'Status']">
    <tr class="border-b border-(--border-subtle) hover:bg-(--surface-hover)">
        <x-ds::table-cell variant="bold">John Doe</x-ds::table-cell>
        <x-ds::table-cell>john@example.com</x-ds::table-cell>
        <x-ds::table-cell>
            <x-ds::badge variant="success" size="sm">Active</x-ds::badge>
        </x-ds::table-cell>
    </tr>
    
    <x-slot:footer>
        <!-- Pagination controls -->
    </x-slot:footer>
</x-ds::table>
@endverbatim</code></pre>
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-(--text-primary)">{{ __('ds.pages.tables.docs.props_title') }}</div>
                            <div class="mt-3 grid grid-cols-1 gap-4 text-sm text-(--text-secondary) sm:grid-cols-2">
                                <div><span class="font-semibold text-(--text-primary)">headers</span> — Array of column titles.</div>
                                <div><span class="font-semibold text-(--text-primary)">striped</span> — Alternate row background color.</div>
                                <div><span class="font-semibold text-(--text-primary)">hoverable</span> — Highlight row on hover.</div>
                                <div><span class="font-semibold text-(--text-primary)">bordered</span> — Add borders to cells.</div>
                                <div><span class="font-semibold text-(--text-primary)">checkbox</span> — Add a select-all checkbox.</div>
                                <div><span class="font-semibold text-(--text-primary)">compact</span> — reduce padding.</div>
                            </div>
                        </div>
                    </div>
                </x-ds::card>
            </div>
        </div>
    </div>
@endsection
