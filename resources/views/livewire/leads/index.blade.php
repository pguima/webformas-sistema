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
                icon="solar:notes-minimalistic-linear"
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
                    isPanning: false,
                    panStartX: 0,
                    panScrollLeft: 0,

                    isLocked(leadOrStage) {
                        const stage = typeof leadOrStage === 'string' ? leadOrStage : (leadOrStage?.stage ?? null);
                        return stage === 'Ganho' || stage === 'Perdido';
                    },

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
                        const fromCol = this.columns.find(c => c.stage === fromStage);
                        const lead = fromCol?.leads?.find(t => t.id === leadId);
                        if (lead && this.isLocked(lead)) {
                            return;
                        }

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

                    startPan(e) {
                        if (e.button !== 1) return;
                        const el = this.$refs.kanbanScroll;
                        if (!el) return;
                        this.isPanning = true;
                        this.panStartX = e.pageX;
                        this.panScrollLeft = el.scrollLeft;
                        e.preventDefault();
                    },

                    movePan(e) {
                        if (!this.isPanning) return;
                        const el = this.$refs.kanbanScroll;
                        if (!el) return;
                        const dx = e.pageX - this.panStartX;
                        el.scrollLeft = this.panScrollLeft - dx;
                    },

                    endPan() {
                        this.isPanning = false;
                    },

                    onWheel(e) {
                        const el = this.$refs.kanbanScroll;
                        if (!el) return;

                        const dx = Math.abs(e.deltaX || 0);
                        const dy = Math.abs(e.deltaY || 0);

                        if (dx > 0) {
                            el.scrollLeft += e.deltaX;
                            e.preventDefault();
                            return;
                        }

                        if (dy > 0) {
                            el.scrollLeft += e.deltaY;
                            e.preventDefault();
                        }
                    },
                }"
                x-on:leads-updated.window="columns = $event.detail.columns"
                class="mt-2"
            >
                <div
                    x-ref="kanbanScroll"
                    class="flex gap-4 overflow-x-auto pb-3"
                    style="scrollbar-width: thin; scrollbar-color: var(--border-default) transparent;"
                    x-on:mousedown="startPan($event)"
                    x-on:wheel="onWheel($event)"
                    x-on:mousemove.window="movePan($event)"
                    x-on:mouseup.window="endPan()"
                    x-on:mouseleave.window="endPan()"
                >
                    <template x-for="column in columns" :key="column.stage">
                        <div
                            class="w-80 shrink-0 rounded-lg bg-(--surface-hover) p-4"
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
                                        :draggable="!isLocked(lead)"
                                        x-on:dragstart="startDrag(lead.id, column.stage, index)"
                                        x-on:dragend="clearDrag()"
                                        x-on:dragover.prevent
                                        x-on:drop.prevent="moveLead(column.stage, index)"
                                    >
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="text-sm font-semibold text-(--text-primary) leading-5" x-text="lead.name"></div>
                                                </div>

                                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                                    <template x-if="lead.whatsapp">
                                                        <x-ds::badge style="soft" variant="success" icon="solar:phone-linear">{{ __('app.leads.card.whatsapp') }}: <span class="ml-1" x-text="lead.whatsapp"></span></x-ds::badge>
                                                    </template>

                                                    <template x-if="lead.empresa">
                                                        <x-ds::badge style="soft" variant="info" icon="solar:buildings-2-linear">Empresa: <span class="ml-1" x-text="lead.empresa"></span></x-ds::badge>
                                                    </template>

                                                    <template x-if="lead.value">
                                                        <x-ds::badge style="soft" variant="secondary" icon="solar:tag-price-linear">{{ __('app.leads.card.value') }}: <span class="ml-1" x-text="lead.value"></span></x-ds::badge>
                                                    </template>

                                                    <template x-if="lead.responsible">
                                                        <x-ds::badge style="soft" variant="secondary" icon="solar:user-linear">{{ __('app.leads.card.responsible') }}: <span class="ml-1" x-text="lead.responsible"></span></x-ds::badge>
                                                    </template>

                                                    <template x-if="lead.origin">
                                                        <x-ds::badge style="soft" variant="info" icon="solar:map-point-linear">{{ __('app.leads.card.origin') }}: <span class="ml-1" x-text="lead.origin"></span></x-ds::badge>
                                                    </template>

                                                    <template x-if="lead.campaign">
                                                        <x-ds::badge style="soft" variant="secondary" icon="solar:target-linear">{{ __('app.leads.card.campaign') }}: <span class="ml-1" x-text="lead.campaign"></span></x-ds::badge>
                                                    </template>

                                                    <template x-if="lead.plan">
                                                        <x-ds::badge style="soft" variant="secondary" icon="solar:bookmark-linear">{{ __('app.leads.card.plan') }}: <span class="ml-1" x-text="lead.plan"></span></x-ds::badge>
                                                    </template>
                                                </div>

                                                <template x-if="lead.services">
                                                    <div class="mt-3 text-xs text-(--text-secondary) line-clamp-2">
                                                        <span class="font-medium">{{ __('app.leads.card.services') }}:</span>
                                                        <span class="ml-1" x-text="lead.services"></span>
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0">
                                                <x-ds::button
                                                    type="button"
                                                    size="icon"
                                                    variant="ghost"
                                                    icon="solar:pen-linear"
                                                    x-bind:disabled="isLocked(lead)"
                                                    x-on:click="$dispatch('open-edit-lead-offcanvas'); $wire.edit(lead.id)"
                                                />

                                                <x-ds::button
                                                    type="button"
                                                    size="icon"
                                                    variant="ghost"
                                                    icon="solar:trash-bin-trash-linear"
                                                    class="hover:text-(--status-error)"
                                                    x-bind:disabled="isLocked(lead)"
                                                    x-on:click="$wire.confirmDelete(lead.id)"
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
                                @php
                                    $isLocked = ($lead['stage'] ?? null) === 'Ganho' || ($lead['stage'] ?? null) === 'Perdido';
                                @endphp
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    :disabled="$isLocked"
                                    x-on:click="$dispatch('open-edit-lead-offcanvas'); $wire.edit({{ $lead['id'] }})"
                                />
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-(--status-error)"
                                    :disabled="$isLocked"
                                    wire:click.prevent="confirmDelete({{ $lead['id'] }})"
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

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.leads.delete.title') }}"
        size="md"
    >
        <div class="space-y-4">
            <x-ds::alert variant="danger" icon="solar:danger-triangle-linear">
                {{ __('app.leads.delete.warning') }}
            </x-ds::alert>

            <p class="text-sm text-(--text-secondary)">
                {!! __('app.leads.delete.confirm_help', ['word' => '<span class="select-all font-mono font-bold text-(--status-error)">DELETE</span>']) !!}
            </p>

            <x-ds::input
                wire:model.live="deleteConfirmation"
                placeholder="{{ __('app.leads.delete.placeholder', ['word' => 'DELETE']) }}"
                class="border-(--status-error) focus:border-(--status-error) focus:ring-(--status-error)/20"
            />
            @error('deleteConfirmation') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.leads.form.cancel') }}</x-ds::button>
                <x-ds::button
                    variant="danger"
                    icon="solar:trash-bin-trash-linear"
                    wire:click.prevent="delete"
                    wire:loading.attr="disabled"
                >
                    {{ __('app.leads.delete.delete_permanently') }}
                </x-ds::button>
            </div>
        </x-slot:footer>
    </x-ds::modal>

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
            <x-ds::input-mask label="{{ __('app.leads.form.whatsapp') }}" wire:model="whatsapp" mask="phone" :error="$errors->first('whatsapp')" />
            <x-ds::input label="Empresa" wire:model="empresa" :error="$errors->first('empresa')" />
            <x-ds::input-mask label="CNPJ" wire:model="cnpj" mask="cnpj" :error="$errors->first('cnpj')" />
            <x-ds::select
                label="{{ __('app.leads.form.plan') }}"
                wire:model.live="plan_id"
                :options="$planOptions"
                :error="$errors->first('plan_id')"
            />

            <x-ds::select-search
                label="{{ __('app.leads.form.services') }}"
                :multiple="true"
                :options="$serviceOptions"
                placeholder="{{ __('app.leads.form.services_placeholder') }}"
                helper="{{ __('app.leads.form.services_helper') }}"
                :disabled="(bool) $plan_id"
                :error="$errors->first('service_ids')"
                wireModel="service_ids"
                :wireLive="true"
            />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ds::input
                    label="{{ __('app.leads.form.value_base') }}"
                    wire:model.live="value_base"
                    type="number"
                    step="0.01"
                    disabled
                    :error="$errors->first('value_base')"
                />

                <x-ds::input
                    label="{{ __('app.leads.form.value_final') }}"
                    wire:model.live="value_final"
                    type="number"
                    step="0.01"
                    disabled
                    :error="$errors->first('value_final')"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ds::select
                    label="{{ __('app.leads.form.discount_type') }}"
                    wire:model.live="discount_type"
                    :options="[
                        ['value' => 'value', 'label' => __('app.leads.form.discount_type_value')],
                        ['value' => 'percent', 'label' => __('app.leads.form.discount_type_percent')],
                    ]"
                    :error="$errors->first('discount_type')"
                />

                <x-ds::input
                    label="{{ __('app.leads.form.discount_value') }}"
                    wire:model.live="discount_value"
                    type="number"
                    step="0.01"
                    :error="$errors->first('discount_value')"
                />
            </div>

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
            <x-ds::input-mask label="{{ __('app.leads.form.whatsapp') }}" wire:model="whatsapp" mask="phone" :error="$errors->first('whatsapp')" />
            <x-ds::input label="Empresa" wire:model="empresa" :error="$errors->first('empresa')" />
            <x-ds::input-mask label="CNPJ" wire:model="cnpj" mask="cnpj" :error="$errors->first('cnpj')" />

            <x-ds::select
                label="{{ __('app.leads.form.plan') }}"
                wire:model.live="plan_id"
                :options="$planOptions"
                :error="$errors->first('plan_id')"
            />

            <x-ds::select-search
                label="{{ __('app.leads.form.services') }}"
                :multiple="true"
                :options="$serviceOptions"
                placeholder="{{ __('app.leads.form.services_placeholder') }}"
                helper="{{ __('app.leads.form.services_helper') }}"
                :disabled="(bool) $plan_id"
                :error="$errors->first('service_ids')"
                wireModel="service_ids"
            />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ds::input
                    label="{{ __('app.leads.form.value_base') }}"
                    wire:model.live="value_base"
                    type="number"
                    step="0.01"
                    disabled
                    :error="$errors->first('value_base')"
                />

                <x-ds::input
                    label="{{ __('app.leads.form.value_final') }}"
                    wire:model.live="value_final"
                    type="number"
                    step="0.01"
                    disabled
                    :error="$errors->first('value_final')"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-ds::select
                    label="{{ __('app.leads.form.discount_type') }}"
                    wire:model.live="discount_type"
                    :options="[
                        ['value' => 'value', 'label' => __('app.leads.form.discount_type_value')],
                        ['value' => 'percent', 'label' => __('app.leads.form.discount_type_percent')],
                    ]"
                    :error="$errors->first('discount_type')"
                />

                <x-ds::input
                    label="{{ __('app.leads.form.discount_value') }}"
                    wire:model.live="discount_value"
                    type="number"
                    step="0.01"
                    :error="$errors->first('discount_value')"
                />
            </div>

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
