<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.leads.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.leads.subtitle') }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                type="button"
                icon="solar:add-circle-linear"
                x-on:click="$dispatch('open-create-lead-offcanvas'); $wire.create()"
                wire:loading.attr="disabled"
            >
                {{ __('app.leads.add') }}
            </x-ds::button>

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
                variant="{{ $viewMode === 'kanban' ? 'secondary' : 'ghost' }}"
                icon="solar:kanban-linear"
                wire:click="$set('viewMode','kanban')"
                wire:loading.attr="disabled"
            />
        </div>
    </div>

    @if ($viewMode === 'kanban')
        <x-ds::card :title="__('app.leads.kanban.title')" :description="__('app.leads.kanban.description')">
            <div
                x-data="{
                    dragged: null,
                    columns: @js($columns),

                    badgeStyle(variant) {
                        const map = {
                            success: { bg: 'var(--status-success-light)', fg: 'var(--status-success)' },
                            info: { bg: 'var(--status-info-light)', fg: 'var(--status-info)' },
                            warning: { bg: 'var(--status-warning-light)', fg: 'var(--status-warning)' },
                            danger: { bg: 'var(--status-error-light)', fg: 'var(--status-error)' },
                            secondary: { bg: 'var(--surface-hover)', fg: 'var(--text-secondary)' },
                            primary: { bg: 'var(--color-primary)', fg: 'var(--text-on-primary)' },
                        };

                        return map[variant] ?? map.secondary;
                    },

                    columnVariant(stage) {
                        const map = {
                            'Novo': 'secondary',
                            'Em Contato': 'info',
                            'Reunião Marcada': 'warning',
                            'Proposta': 'primary',
                            'Ganho': 'success',
                            'Perdido': 'danger',
                        };

                        return map[stage] ?? 'secondary';
                    },

                    startDrag(leadId, fromStage, fromIndex) {
                        this.dragged = { leadId, fromStage, fromIndex };
                    },

                    clearDrag() {
                        this.dragged = null;
                    },

                    moveLead(toStage, toIndex = null) {
                        if (!this.dragged) return;

                        const { leadId, fromStage } = this.dragged;

                        const fromCol = this.columns.find(c => c.stage === fromStage);
                        const toCol = this.columns.find(c => c.stage === toStage);

                        if (!fromCol || !toCol) return;

                        const fromIndex = fromCol.leads.findIndex(t => t.id === leadId);
                        if (fromIndex === -1) return;

                        const [lead] = fromCol.leads.splice(fromIndex, 1);

                        let finalIndex;
                        if (toIndex === null || toIndex === undefined) {
                            toCol.leads.push(lead);
                            finalIndex = toCol.leads.length - 1;
                        } else {
                            const safeIndex = Math.max(0, Math.min(toIndex, toCol.leads.length));
                            toCol.leads.splice(safeIndex, 0, lead);
                            finalIndex = safeIndex;
                        }

                        lead.stage = toStage;

                        this.clearDrag();

                        $wire.moveLead(lead.id, toStage, finalIndex);
                    },

                    isDragging(leadId) {
                        return this.dragged && this.dragged.leadId === leadId;
                    },
                }"
                class="mt-2"
            >
                <div class="flex gap-4 overflow-x-auto pb-2">
                    <template x-for="column in columns" :key="column.stage">
                        <div
                            class="w-90 shrink-0 rounded-lg bg-(--surface-hover) p-4"
                            x-on:dragover.prevent
                            x-on:drop.prevent="moveLead(column.stage)"
                        >
                            <div class="flex items-center justify-between gap-3 pb-3 mb-3 border-b border-(--border-subtle)">
                                <div class="text-sm font-semibold text-(--text-primary)" x-text="column.stage"></div>
                                <span
                                    class="inline-flex items-center rounded-md text-xs px-2 py-0.5 font-medium"
                                    :style="(() => { const s = badgeStyle(columnVariant(column.stage)); return `background-color: ${s.bg}; color: ${s.fg};`; })()"
                                >
                                    <span x-text="column.count"></span>
                                </span>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(lead, index) in column.leads" :key="lead.id">
                                    <div
                                        class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4 shadow-(--shadow-sm) transition-opacity"
                                        :class="isDragging(lead.id) ? 'opacity-50' : ''"
                                        draggable="true"
                                        x-on:dragstart="startDrag(lead.id, column.stage, index)"
                                        x-on:dragend="clearDrag()"
                                        x-on:dragover.prevent
                                        x-on:drop.prevent="moveLead(column.stage, index)"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="text-sm font-semibold text-(--text-primary)" x-text="lead.name"></div>
                                                <div class="mt-2 space-y-1 text-xs text-(--text-secondary)">
                                                    <template x-if="lead.whatsapp"><div><span class="font-medium">{{ __('app.leads.card.whatsapp') }}:</span> <span x-text="lead.whatsapp"></span></div></template>
                                                    <template x-if="lead.plan"><div><span class="font-medium">{{ __('app.leads.card.plan') }}:</span> <span x-text="lead.plan"></span></div></template>
                                                    <template x-if="lead.services"><div class="line-clamp-2"><span class="font-medium">{{ __('app.leads.card.services') }}:</span> <span x-text="lead.services"></span></div></template>
                                                    <template x-if="lead.value"><div><span class="font-medium">{{ __('app.leads.card.value') }}:</span> <span x-text="lead.value"></span></div></template>
                                                    <template x-if="lead.responsible"><div><span class="font-medium">{{ __('app.leads.card.responsible') }}:</span> <span x-text="lead.responsible"></span></div></template>
                                                    <template x-if="lead.origin"><div><span class="font-medium">{{ __('app.leads.card.origin') }}:</span> <span x-text="lead.origin"></span></div></template>
                                                    <template x-if="lead.campaign"><div><span class="font-medium">{{ __('app.leads.card.campaign') }}:</span> <span x-text="lead.campaign"></span></div></template>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <x-ds::button
                                                    type="button"
                                                    size="icon"
                                                    variant="ghost"
                                                    icon="solar:pen-linear"
                                                    x-on:click="$dispatch('open-edit-lead-offcanvas'); $wire.edit(lead.id)"
                                                />

                                                <x-ds::button
                                                    type="button"
                                                    size="icon"
                                                    variant="ghost"
                                                    icon="solar:trash-bin-trash-linear"
                                                    class="hover:text-(--status-error)"
                                                    x-on:click="$wire.delete(lead.id)"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </x-ds::card>
    @else
        <x-ds::card :title="__('app.leads.list.title')" :description="__('app.leads.list.description')">
            <x-ds::table :headers="[__('app.leads.table.name'), __('app.leads.table.whatsapp'), __('app.leads.table.stage'), __('app.leads.table.responsible'), __('app.leads.table.updated_at'), __('app.leads.table.actions')]">
                @forelse(collect($columns)->flatMap(fn ($c) => $c['leads']) as $lead)
                    <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="lead-row-{{ $lead['id'] }}">
                        <x-ds::table-cell>
                            <div class="text-sm font-medium text-(--text-primary)">{{ $lead['name'] }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ $lead['whatsapp'] ?? '—' }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ $lead['stage'] ?? '—' }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ $lead['responsible'] ?? '—' }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">
                                {{ isset($lead['updated_at']) && $lead['updated_at'] ? \Illuminate\Support\Carbon::parse($lead['updated_at'])->format('d/m/Y H:i') : '—' }}
                            </div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="flex items-center gap-2">
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    x-on:click="$dispatch('open-edit-lead-offcanvas'); $wire.edit({{ $lead['id'] }})"
                                />
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-(--status-error)"
                                    wire:click.prevent="delete({{ $lead['id'] }})"
                                />
                            </div>
                        </x-ds::table-cell>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-sm text-(--text-secondary)">
                            {{ __('app.leads.empty') }}
                        </td>
                    </tr>
                @endforelse
            </x-ds::table>
        </x-ds::card>
    @endif

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-create-lead-offcanvas.window="open = true"
        x-on:close-lead-offcanvas.window="open = false"
        title="{{ __('app.leads.offcanvas.create_title') }}"
        description="{{ __('app.leads.offcanvas.create_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.leads.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.leads.form.whatsapp') }}" wire:model="whatsapp" :error="$errors->first('whatsapp')" />
            <x-ds::input label="{{ __('app.leads.form.plan') }}" wire:model="plan" :error="$errors->first('plan')" />
            <x-ds::textarea label="{{ __('app.leads.form.services') }}" wire:model="services" rows="3" :error="$errors->first('services')" />
            <x-ds::input label="{{ __('app.leads.form.value') }}" wire:model="value" type="number" step="0.01" :error="$errors->first('value')" />

            <x-ds::select
                label="{{ __('app.leads.form.responsible') }}"
                wire:model="responsible_user_id"
                :options="$users->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->prepend(['value' => '', 'label' => __('app.leads.form.responsible_empty')])->all()"
                :error="$errors->first('responsible_user_id')"
            />

            <x-ds::input label="{{ __('app.leads.form.origin') }}" wire:model="origin" :error="$errors->first('origin')" />
            <x-ds::input label="{{ __('app.leads.form.campaign') }}" wire:model="campaign" :error="$errors->first('campaign')" />

            <x-ds::select
                label="{{ __('app.leads.form.stage') }}"
                wire:model="stage"
                :options="collect(\App\Livewire\Leads\Index::STAGES)->map(fn($s) => ['value' => $s, 'label' => $s])->all()"
                :error="$errors->first('stage')"
            />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.leads.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.leads.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.leads.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-edit-lead-offcanvas.window="open = true"
        x-on:close-lead-offcanvas.window="open = false"
        title="{{ __('app.leads.offcanvas.edit_title') }}"
        description="{{ __('app.leads.offcanvas.edit_description') }}"
        position="right"
        size="md"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::input label="{{ __('app.leads.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
            <x-ds::input label="{{ __('app.leads.form.whatsapp') }}" wire:model="whatsapp" :error="$errors->first('whatsapp')" />
            <x-ds::input label="{{ __('app.leads.form.plan') }}" wire:model="plan" :error="$errors->first('plan')" />
            <x-ds::textarea label="{{ __('app.leads.form.services') }}" wire:model="services" rows="3" :error="$errors->first('services')" />
            <x-ds::input label="{{ __('app.leads.form.value') }}" wire:model="value" type="number" step="0.01" :error="$errors->first('value')" />

            <x-ds::select
                label="{{ __('app.leads.form.responsible') }}"
                wire:model="responsible_user_id"
                :options="$users->map(fn($u) => ['value' => $u->id, 'label' => $u->name])->prepend(['value' => '', 'label' => __('app.leads.form.responsible_empty')])->all()"
                :error="$errors->first('responsible_user_id')"
            />

            <x-ds::input label="{{ __('app.leads.form.origin') }}" wire:model="origin" :error="$errors->first('origin')" />
            <x-ds::input label="{{ __('app.leads.form.campaign') }}" wire:model="campaign" :error="$errors->first('campaign')" />

            <x-ds::select
                label="{{ __('app.leads.form.stage') }}"
                wire:model="stage"
                :options="collect(\App\Livewire\Leads\Index::STAGES)->map(fn($s) => ['value' => $s, 'label' => $s])->all()"
                :error="$errors->first('stage')"
            />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.leads.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.leads.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.leads.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>
</div>
