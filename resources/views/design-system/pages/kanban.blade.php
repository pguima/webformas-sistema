@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-(--text-primary)">
                    {{ __('ds.pages.kanban.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--text-secondary)">
                    {{ __('ds.pages.kanban.subtitle') }}
                </div>
            </div>
            <div class="flex gap-2">
                <x-ds::button variant="ghost" icon="solar:arrow-left-linear" onclick="history.back()">
                    {{ __('ds.actions.back') }}
                </x-ds::button>
            </div>
        </div>

        <div class="mt-8">
            <x-ds::card :title="__('ds.pages.kanban.sections.board_title')" :description="__('ds.pages.kanban.sections.board_description')">
                <div
                    x-data="{
                        dragged: null,
                        badgeStyle(variant) {
                            const map = {
                                success: { bg: 'var(--status-success-light)', fg: 'var(--status-success)' },
                                info: { bg: 'var(--status-info-light)', fg: 'var(--status-info)' },
                                warning: { bg: 'var(--status-warning-light)', fg: 'var(--status-warning)' },
                                danger: { bg: 'var(--status-error-light)', fg: 'var(--status-error)' },
                                secondary: { bg: 'var(--surface-hover)', fg: 'var(--text-secondary)' },
                                dark: { bg: 'var(--text-primary)', fg: 'var(--surface-card)' },
                                light: { bg: 'var(--surface-hover)', fg: 'var(--text-primary)' },
                                primary: { bg: 'var(--color-primary)', fg: 'var(--text-on-primary)' },
                            };

                            return map[variant] ?? map.primary;
                        },
                        columns: [
                            {
                                id: 'todo',
                                title: '{{ __('ds.pages.kanban.columns.todo') }}',
                                countVariant: 'secondary',
                                tasks: [
                                    { id: 't1', title: '{{ __('ds.pages.kanban.tasks.t1.title') }}', tag: '{{ __('ds.pages.kanban.tasks.t1.tag') }}', tagVariant: 'info' },
                                    { id: 't2', title: '{{ __('ds.pages.kanban.tasks.t2.title') }}', tag: '{{ __('ds.pages.kanban.tasks.t2.tag') }}', tagVariant: 'warning' },
                                    { id: 't3', title: '{{ __('ds.pages.kanban.tasks.t3.title') }}', tag: '{{ __('ds.pages.kanban.tasks.t3.tag') }}', tagVariant: 'secondary' },
                                ],
                            },
                            {
                                id: 'doing',
                                title: '{{ __('ds.pages.kanban.columns.doing') }}',
                                countVariant: 'warning',
                                tasks: [
                                    { id: 't4', title: '{{ __('ds.pages.kanban.tasks.t4.title') }}', tag: '{{ __('ds.pages.kanban.tasks.t4.tag') }}', tagVariant: 'warning' },
                                    { id: 't5', title: '{{ __('ds.pages.kanban.tasks.t5.title') }}', tag: '{{ __('ds.pages.kanban.tasks.t5.tag') }}', tagVariant: 'info' },
                                ],
                            },
                            {
                                id: 'done',
                                title: '{{ __('ds.pages.kanban.columns.done') }}',
                                countVariant: 'success',
                                tasks: [
                                    { id: 't6', title: '{{ __('ds.pages.kanban.tasks.t6.title') }}', tag: '{{ __('ds.pages.kanban.tasks.t6.tag') }}', tagVariant: 'success' },
                                ],
                            },
                        ],

                        startDrag(taskId, fromColumnId, fromIndex) {
                            this.dragged = { taskId, fromColumnId, fromIndex };
                        },

                        clearDrag() {
                            this.dragged = null;
                        },

                        moveTask(toColumnId, toIndex = null) {
                            if (!this.dragged) return;

                            const { taskId, fromColumnId } = this.dragged;

                            const fromCol = this.columns.find(c => c.id === fromColumnId);
                            const toCol = this.columns.find(c => c.id === toColumnId);

                            if (!fromCol || !toCol) return;

                            const fromTaskIndex = fromCol.tasks.findIndex(t => t.id === taskId);
                            if (fromTaskIndex === -1) return;

                            const [task] = fromCol.tasks.splice(fromTaskIndex, 1);

                            if (toIndex === null || toIndex === undefined) {
                                toCol.tasks.push(task);
                            } else {
                                const safeIndex = Math.max(0, Math.min(toIndex, toCol.tasks.length));
                                toCol.tasks.splice(safeIndex, 0, task);
                            }

                            this.clearDrag();
                        },

                        isDraggingTask(taskId) {
                            return this.dragged && this.dragged.taskId === taskId;
                        },
                    }"
                    class="mt-2"
                >
                    <div class="flex gap-4 overflow-x-auto pb-2">
                        <template x-for="column in columns" :key="column.id">
                            <div
                                class="w-[320px] shrink-0 rounded-lg bg-(--surface-hover) p-4"
                                x-on:dragover.prevent
                                x-on:drop.prevent="moveTask(column.id)"
                            >
                                <div class="flex items-center justify-between gap-3 pb-3 mb-3 border-b border-(--border-subtle)">
                                    <div class="text-sm font-semibold text-(--text-primary)" x-text="column.title"></div>
                                    <span
                                        class="inline-flex items-center rounded-md text-xs px-2 py-0.5 font-medium"
                                        :style="(() => { const s = badgeStyle(column.countVariant); return `background-color: ${s.bg}; color: ${s.fg};`; })()"
                                    >
                                        <span x-text="column.tasks.length"></span>
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <template x-for="(task, index) in column.tasks" :key="task.id">
                                        <div
                                            class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4 shadow-(--shadow-sm) transition-opacity"
                                            :class="isDraggingTask(task.id) ? 'opacity-50' : ''"
                                            draggable="true"
                                            x-on:dragstart="startDrag(task.id, column.id, index)"
                                            x-on:dragend="clearDrag()"
                                            x-on:dragover.prevent
                                            x-on:drop.prevent="moveTask(column.id, index)"
                                        >
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-(--text-primary)" x-text="task.title"></div>
                                                    <div class="mt-2">
                                                        <span
                                                            class="inline-flex items-center rounded-md text-xs px-2 py-0.5 font-medium"
                                                            :style="(() => { const s = badgeStyle(task.tagVariant); return `background-color: ${s.bg}; color: ${s.fg};`; })()"
                                                        >
                                                            <span x-text="task.tag"></span>
                                                        </span>
                                                    </div>
                                                </div>

                                                <x-ds::button
                                                    type="button"
                                                    size="icon"
                                                    variant="ghost"
                                                    icon="solar:menu-dots-linear"
                                                    x-on:click.prevent
                                                />
                                            </div>

                                            <div class="mt-3 flex items-center justify-between gap-3 text-xs text-(--text-muted)">
                                                <div class="inline-flex items-center gap-2">
                                                    <iconify-icon icon="solar:calendar-linear" class="text-sm"></iconify-icon>
                                                    <span>{{ __('ds.pages.kanban.labels.due') }}</span>
                                                </div>
                                                <div class="inline-flex items-center gap-2">
                                                    <iconify-icon icon="solar:user-circle-linear" class="text-sm"></iconify-icon>
                                                    <span>{{ __('ds.pages.kanban.labels.assigned') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="pt-2">
                                        <x-ds::link
                                            href="javascript:void(0)"
                                            variant="secondary"
                                            icon="solar:add-circle-linear"
                                        >
                                            {{ __('ds.pages.kanban.actions.add_task') }}
                                        </x-ds::link>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-6 text-sm text-(--text-secondary)">
                        <x-ds::alert variant="info" icon="solar:info-circle-linear">
                            {{ __('ds.pages.kanban.hints.drag_hint') }}
                        </x-ds::alert>
                    </div>
                </div>
            </x-ds::card>
        </div>

        <div class="mt-10">
            <div class="mb-6 text-sm font-semibold text-(--text-secondary)">
                {{ __('ds.pages.kanban.docs.title') }}
            </div>

            <x-ds::card :title="__('ds.pages.kanban.docs.links_component.title')" :description="__('ds.pages.kanban.docs.links_component.subtitle')">
                <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <div class="text-sm font-semibold text-(--text-primary)">{{ __('ds.pages.kanban.docs.links_component.example_code_title') }}</div>
                        <div class="mt-3 overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-hover)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
<x-ds::link href="/design-system" variant="primary">
    Go to Design System
</x-ds::link>

<x-ds::link href="https://example.com" external variant="secondary">
    External Link
</x-ds::link>

<x-ds::link href="#" icon="solar:arrow-right-linear" iconPosition="right">
    Next step
</x-ds::link>
@endverbatim</code></pre>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-(--text-primary)">{{ __('ds.pages.kanban.docs.links_component.props_title') }}</div>
                        <div class="mt-3 grid grid-cols-1 gap-3 text-sm text-(--text-secondary)">
                            <div><span class="font-semibold text-(--text-primary)">href</span> — {{ __('ds.pages.kanban.docs.links_component.props.href') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">variant</span> — {{ __('ds.pages.kanban.docs.links_component.props.variant') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">size</span> — {{ __('ds.pages.kanban.docs.links_component.props.size') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">icon</span> — {{ __('ds.pages.kanban.docs.links_component.props.icon') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">iconPosition</span> — {{ __('ds.pages.kanban.docs.links_component.props.icon_position') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">external</span> — {{ __('ds.pages.kanban.docs.links_component.props.external') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">underline</span> — {{ __('ds.pages.kanban.docs.links_component.props.underline') }}</div>
                            <div><span class="font-semibold text-(--text-primary)">disabled</span> — {{ __('ds.pages.kanban.docs.links_component.props.disabled') }}</div>
                        </div>
                    </div>
                </div>
            </x-ds::card>
        </div>
    </div>
@endsection
