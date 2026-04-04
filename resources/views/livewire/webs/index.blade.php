<div class="space-y-6">
    @php
        $scoreVariantInt = function ($score) {
            if ($score === null || $score === '') return 'secondary';
            $pct = (int) $score;
            if ($pct >= 90) return 'success';
            if ($pct >= 50) return 'warning';
            return 'danger';
        };

        $scoreLabelInt = function ($score) {
            if ($score === null || $score === '') return __('app.common.dash');
            return (string) (int) $score;
        };
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-(--text-primary)">{{ __('app.webs.title') }}</h1>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.webs.subtitle') }}</p>
        </div>
    </div>

    <x-ds::card class="mt-6">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="w-full max-w-sm">
                <x-ds::input
                    icon="solar:magnifer-linear"
                    placeholder="{{ __('app.webs.search_placeholder') }}"
                    wire:model.live.debounce.300ms="search"
                />
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <div class="w-full sm:w-40">
                    <div class="flex items-center gap-3">
                        <span class="shrink-0 text-sm font-medium text-(--text-primary)">{{ __('app.webs.per_page') }}</span>
                        <x-ds::select
                            wire:model.live="perPage"
                            :options="[
                                ['value' => 10, 'label' => '10'],
                                ['value' => 25, 'label' => '25'],
                                ['value' => 50, 'label' => '50'],
                                ['value' => 100, 'label' => '100'],
                            ]"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-2">
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
                        variant="{{ $viewMode === 'grid' ? 'secondary' : 'ghost' }}"
                        icon="solar:widget-4-linear"
                        wire:click="$set('viewMode','grid')"
                        wire:loading.attr="disabled"
                    />
                </div>
            </div>
        </div>

        @if ($viewMode === 'grid')
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse($webs as $web)
                    <x-ds::card class="relative p-4" wire:key="grid-{{ $web->id }}">
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:chart-2-linear"
                                title="Análise"
                                wire:click="analyze({{ $web->id }})"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-60 cursor-wait"
                                wire:target="analyze({{ $web->id }})"
                                class="relative"
                            >
                                <span
                                    wire:loading
                                    wire:target="analyze({{ $web->id }})"
                                    class="absolute inset-0 flex items-center justify-center"
                                >
                                    <x-ds::spinner size="sm" variant="secondary" />
                                </span>
                            </x-ds::button>

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:pen-linear"
                                wire:mouseenter="prefetch({{ $web->id }})"
                                x-on:click="$dispatch('open-edit-web-offcanvas'); $wire.edit({{ $web->id }})"
                                wire:loading.attr="disabled"
                            />

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:trash-bin-trash-linear"
                                class="hover:text-(--status-error)"
                                wire:click.prevent="confirmDelete({{ $web->id }})"
                                wire:loading.attr="disabled"
                            />
                        </div>

                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-(--text-primary) truncate">{{ $web->name }}</div>
                            <div class="mt-1 text-xs text-(--text-muted) truncate">{{ $web->client?->name }}</div>
                            <div class="mt-1 text-xs text-(--text-muted) truncate">{{ $web->url }}</div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($web->status)
                                <x-ds::badge variant="secondary">{{ $web->status }}</x-ds::badge>
                            @endif
                            @if($web->platform)
                                <x-ds::badge variant="secondary">{{ $web->platform }}</x-ds::badge>
                            @endif
                            @if($web->responsible)
                                <x-ds::badge variant="secondary">{{ $web->responsible }}</x-ds::badge>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <x-ds::badge :variant="$scoreVariantInt($web->performance)" style="soft">P {{ $scoreLabelInt($web->performance) }}</x-ds::badge>
                            <x-ds::badge :variant="$scoreVariantInt($web->seo)" style="soft">S {{ $scoreLabelInt($web->seo) }}</x-ds::badge>
                            <x-ds::badge :variant="$scoreVariantInt($web->accessibility)" style="soft">A {{ $scoreLabelInt($web->accessibility) }}</x-ds::badge>
                            <x-ds::badge :variant="$scoreVariantInt($web->best_practices)" style="soft">B {{ $scoreLabelInt($web->best_practices) }}</x-ds::badge>
                        </div>

                        @if($web->pagespeed_last_checked_at)
                            <div class="mt-2 text-[11px] text-(--text-muted)">
                                {{ $web->pagespeed_last_checked_at->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    </x-ds::card>
                @empty
                    <div class="py-8 text-center text-sm text-(--text-secondary) sm:col-span-2 xl:col-span-3">
                        {{ __('app.webs.no_results', ['search' => $search]) }}
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $webs->links() }}
            </div>
        @else
            <x-ds::table :headers="[__('app.webs.table.name'), __('app.webs.table.client'), __('app.webs.table.platform'), __('app.webs.table.responsible'), __('app.webs.table.pagespeed'), __('app.webs.table.status'), __('app.webs.table.actions')]">
                @forelse($webs as $web)
                    <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="{{ $web->id }}">
                        <x-ds::table-cell>
                            <div class="text-sm font-medium text-(--text-primary)">{{ $web->name }}</div>
                            <div class="mt-1 text-xs text-(--text-muted)">{{ $web->url }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ $web->client?->name ?: __('app.common.dash') }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ $web->platform ?: __('app.common.dash') }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="text-sm text-(--text-secondary)">{{ $web->responsible ?: __('app.common.dash') }}</div>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="flex flex-wrap gap-2">
                                <x-ds::badge :variant="$scoreVariantInt($web->performance)" style="soft">P {{ $scoreLabelInt($web->performance) }}</x-ds::badge>
                                <x-ds::badge :variant="$scoreVariantInt($web->seo)" style="soft">S {{ $scoreLabelInt($web->seo) }}</x-ds::badge>
                                <x-ds::badge :variant="$scoreVariantInt($web->accessibility)" style="soft">A {{ $scoreLabelInt($web->accessibility) }}</x-ds::badge>
                                <x-ds::badge :variant="$scoreVariantInt($web->best_practices)" style="soft">B {{ $scoreLabelInt($web->best_practices) }}</x-ds::badge>
                            </div>

                            @if($web->pagespeed_last_checked_at)
                                <div class="mt-1 text-[11px] text-(--text-muted)">
                                    {{ $web->pagespeed_last_checked_at->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <x-ds::badge variant="secondary">{{ $web->status ?: __('app.common.dash') }}</x-ds::badge>
                        </x-ds::table-cell>
                        <x-ds::table-cell>
                            <div class="flex items-center gap-2">
                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:chart-2-linear"
                                    title="Análise"
                                    wire:click="analyze({{ $web->id }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-60 cursor-wait"
                                    wire:target="analyze({{ $web->id }})"
                                    class="relative"
                                >
                                    <span
                                        wire:loading
                                        wire:target="analyze({{ $web->id }})"
                                        class="absolute inset-0 flex items-center justify-center"
                                    >
                                        <x-ds::spinner size="sm" variant="secondary" />
                                    </span>
                                </x-ds::button>

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:pen-linear"
                                    wire:mouseenter="prefetch({{ $web->id }})"
                                    x-on:click="$dispatch('open-edit-web-offcanvas'); $wire.edit({{ $web->id }})"
                                    wire:loading.attr="disabled"
                                />

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:trash-bin-trash-linear"
                                    class="hover:text-(--status-error)"
                                    wire:click.prevent="confirmDelete({{ $web->id }})"
                                    wire:loading.attr="disabled"
                                />
                            </div>
                        </x-ds::table-cell>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-sm text-(--text-secondary)">
                            {{ __('app.webs.no_results', ['search' => $search]) }}
                        </td>
                    </tr>
                @endforelse

                <x-slot:footer>
                    <div class="mt-4">
                        {{ $webs->links() }}
                    </div>
                </x-slot:footer>
            </x-ds::table>
        @endif
    </x-ds::card>

    <x-ds::offcanvas
        x-data="{ open: false }"
        x-on:open-edit-web-offcanvas.window="open = true"
        x-on:close-web-offcanvas.window="open = false"
        title="{{ __('app.webs.offcanvas.edit_title') }}"
        description="{{ __('app.webs.offcanvas.edit_description') }}"
        position="right"
        size="xl"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <x-ds::select
                label="{{ __('app.webs.form.client') }}"
                :options="$clientOptions"
                placeholder="{{ __('app.webs.form.client_placeholder') }}"
                wire:model="client_id"
                required
                :error="$errors->first('client_id')"
            />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::input label="{{ __('app.webs.form.name') }}" wire:model="name" required :error="$errors->first('name')" />
                <x-ds::input label="{{ __('app.webs.form.url') }}" wire:model="url" :error="$errors->first('url')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::select label="{{ __('app.webs.form.type') }}" wire:model="type" :options="__('app.webs.types')" :error="$errors->first('type')" />
                <x-ds::select label="{{ __('app.webs.form.objective') }}" wire:model="objective" :options="__('app.webs.objectives')" :error="$errors->first('objective')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::input label="{{ __('app.webs.form.cta_main') }}" wire:model="cta_main" :error="$errors->first('cta_main')" />
                <x-ds::select label="{{ __('app.webs.form.platform') }}" wire:model="platform" :options="__('app.webs.platforms')" :error="$errors->first('platform')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::select label="{{ __('app.webs.form.status') }}" wire:model="status" :options="__('app.webs.statuses')" :error="$errors->first('status')" />
                <x-ds::input label="{{ __('app.webs.form.responsible') }}" wire:model="responsible" :error="$errors->first('responsible')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::input label="{{ __('app.webs.form.gtm_analytics') }}" wire:model="gtm_analytics" :error="$errors->first('gtm_analytics')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <x-ds::input label="{{ __('app.webs.form.pagespeed_mobile') }}" wire:model="pagespeed_mobile" type="number" :error="$errors->first('pagespeed_mobile')" />
                <x-ds::input label="{{ __('app.webs.form.pagespeed_desktop') }}" wire:model="pagespeed_desktop" type="number" :error="$errors->first('pagespeed_desktop')" />
                <x-ds::input label="{{ __('app.webs.form.seo_score') }}" wire:model="seo_score" type="number" :error="$errors->first('seo_score')" />
            </div>

            <div>
                <label class="block text-sm font-medium text-(--text-secondary) mb-1">Análise PageSpeed agendada</label>
                <select wire:model="pagespeed_schedule" class="w-full rounded-lg border border-(--border-default) bg-(--surface-input) px-3 py-2 text-sm text-(--text-primary) focus:outline-none focus:ring-2 focus:ring-(--brand-primary)">
                    <option value="none">Sem agendamento</option>
                    <option value="daily">Diário</option>
                    <option value="weekly">Semanal</option>
                    <option value="monthly">Mensal</option>
                </select>
                <p class="mt-1 text-xs text-(--text-muted)">O sistema rodará o PageSpeed automaticamente na frequência escolhida.</p>
            </div>

            <x-ds::textarea label="{{ __('app.webs.form.notes') }}" wire:model="notes" :error="$errors->first('notes')" rows="4" />

            <div class="pt-4 flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" @click="open = false">{{ __('app.webs.form.cancel') }}</x-ds::button>
                <x-ds::button type="submit" icon="solar:diskette-linear" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ __('app.webs.form.save') }}</span>
                    <span wire:loading wire:target="save">{{ __('app.webs.form.save') }}</span>
                </x-ds::button>
            </div>
        </form>
    </x-ds::offcanvas>

    <x-ds::offcanvas
        id="web-analysis-offcanvas"
        x-data="{ open: false }"
        x-on:open-web-analysis-offcanvas.window="open = true"
        x-on:close-web-analysis-offcanvas.window="open = false"
        title="Análise do Site"
        description="PageSpeed, SEO, segurança, schema, GEO e muito mais."
        position="right"
        size="full"
    >
        @if($analysisWebId)
            <livewire:clients.web-analysis :webId="$analysisWebId" :key="'web-analysis-' . $analysisWebId" />
        @else
            <div class="text-sm text-(--text-secondary)">{{ __('app.common.dash') }}</div>
        @endif
    </x-ds::offcanvas>

    <x-ds::modal
        x-on:open-delete-modal.window="openModal()"
        x-on:close-delete-modal.window="closeModal()"
        title="{{ __('app.webs.delete.title') }}"
        description="{{ __('app.webs.delete.description') }}"
    >
        <div class="space-y-4">
            <x-ds::input
                label="{{ __('app.webs.delete.confirmation_label') }}"
                placeholder="{{ __('app.webs.delete.placeholder', ['word' => 'DELETE']) }}"
                wire:model="deleteConfirmation"
                :error="$errors->first('deleteConfirmation')"
            />

            <div class="flex justify-end gap-2">
                <x-ds::button type="button" variant="secondary" x-on:click="closeModal()">{{ __('app.webs.delete.cancel') }}</x-ds::button>
                <x-ds::button type="button" variant="danger" wire:click="delete" wire:loading.attr="disabled">
                    {{ __('app.webs.delete.confirm') }}
                </x-ds::button>
            </div>
        </div>
    </x-ds::modal>
</div>
