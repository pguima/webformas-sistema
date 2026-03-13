<div
    class="space-y-6"
    x-data="{}"
    x-on:client-web-tab-activated.window="$wire.set('pagespeedOffcanvasOpen', false); $wire.set('pagespeedWebId', null)"
>
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
            <h2 class="text-lg font-semibold text-(--text-primary)">{{ __('app.webs.client_tab.title') }}</h2>
            <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.webs.client_tab.subtitle', ['name' => $client->name]) }}</p>
        </div>

        <div class="flex gap-2">
            <x-ds::button
                type="button"
                icon="solar:add-circle-linear"
                x-on:click="$dispatch('open-create-client-web-offcanvas'); $wire.create()"
                wire:loading.attr="disabled"
            >
                {{ __('app.webs.add') }}
            </x-ds::button>
        </div>
    </div>

    <x-ds::card>
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
                                icon="solar:shield-check-linear"
                                title="Auditoria"
                                wire:click="audit({{ $web->id }})"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-60 cursor-wait"
                                wire:target="audit({{ $web->id }})"
                                class="relative"
                            >
                                <span
                                    wire:loading
                                    wire:target="audit({{ $web->id }})"
                                    class="absolute inset-0 flex items-center justify-center"
                                >
                                    <x-ds::spinner size="sm" variant="secondary" />
                                </span>
                            </x-ds::button>

                            <x-ds::button
                                type="button"
                                size="icon"
                                variant="ghost"
                                icon="solar:graph-new-linear"
                                title="PageSpeed"
                                wire:click="pagespeed({{ $web->id }})"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-60 cursor-wait"
                                wire:target="pagespeed({{ $web->id }})"
                                class="relative"
                            >
                                <span
                                    wire:loading
                                    wire:target="pagespeed({{ $web->id }})"
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
                                x-on:click="$dispatch('open-edit-client-web-offcanvas'); $wire.edit({{ $web->id }})"
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
                            <div class="text-sm font-semibold text-(--text-primary) truncate">{{ $web->url }}</div>
                            <div class="mt-1 text-xs text-(--text-muted) truncate">{{ $web->type }}</div>
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
            <x-ds::table :headers="[__('app.webs.table.url'), __('app.webs.table.platform'), __('app.webs.table.responsible'), __('app.webs.table.pagespeed'), __('app.webs.table.status'), __('app.webs.table.actions')]">
                @forelse($webs as $web)
                    <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)" wire:key="{{ $web->id }}">
                        <x-ds::table-cell>
                            <div class="text-sm font-medium text-(--text-primary)">{{ $web->url }}</div>
                            <div class="mt-1 text-xs text-(--text-muted)">{{ $web->type }}</div>
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
                                    icon="solar:shield-check-linear"
                                    title="Auditoria"
                                    wire:click="audit({{ $web->id }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-60 cursor-wait"
                                    wire:target="audit({{ $web->id }})"
                                    class="relative"
                                >
                                    <span
                                        wire:loading
                                        wire:target="audit({{ $web->id }})"
                                        class="absolute inset-0 flex items-center justify-center"
                                    >
                                        <x-ds::spinner size="sm" variant="secondary" />
                                    </span>
                                </x-ds::button>

                                <x-ds::button
                                    type="button"
                                    size="icon"
                                    variant="ghost"
                                    icon="solar:graph-new-linear"
                                    title="PageSpeed"
                                    wire:click="pagespeed({{ $web->id }})"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-60 cursor-wait"
                                    wire:target="pagespeed({{ $web->id }})"
                                    class="relative"
                                >
                                    <span
                                        wire:loading
                                        wire:target="pagespeed({{ $web->id }})"
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
                                    x-on:click="$dispatch('open-edit-client-web-offcanvas'); $wire.edit({{ $web->id }})"
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
                        <td colspan="6" class="py-8 text-center text-sm text-(--text-secondary)">
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
        x-on:open-create-client-web-offcanvas.window="open = true"
        x-on:open-edit-client-web-offcanvas.window="open = true"
        x-on:close-client-web-offcanvas.window="open = false"
        title="{{ $webId ? __('app.webs.offcanvas.edit_title') : __('app.webs.offcanvas.create_title') }}"
        description="{{ $webId ? __('app.webs.offcanvas.edit_description') : __('app.webs.offcanvas.create_description') }}"
        position="right"
        size="xl"
    >
        <form wire:submit.prevent="save" class="space-y-5">
            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::input label="{{ __('app.webs.form.url') }}" wire:model="url" required :error="$errors->first('url')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::select name="type" label="{{ __('app.webs.form.type') }}" wire:model="type" :value="$type" :options="__('app.webs.types')" :error="$errors->first('type')" />
                <x-ds::select name="objective" label="{{ __('app.webs.form.objective') }}" wire:model="objective" :value="$objective" :options="__('app.webs.objectives')" :error="$errors->first('objective')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::input label="{{ __('app.webs.form.cta_main') }}" wire:model="cta_main" :error="$errors->first('cta_main')" />
                <x-ds::select name="platform" label="{{ __('app.webs.form.platform') }}" wire:model="platform" :value="$platform" :options="__('app.webs.platforms')" :error="$errors->first('platform')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::select name="status" label="{{ __('app.webs.form.status') }}" wire:model="status" :value="$status" :options="__('app.webs.statuses')" :error="$errors->first('status')" />
                <x-ds::input label="{{ __('app.webs.form.responsible') }}" wire:model="responsible" :error="$errors->first('responsible')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::input label="{{ __('app.webs.form.site_created_at') }}" wire:model="site_created_at" type="date" :error="$errors->first('site_created_at')" />
                <x-ds::input label="{{ __('app.webs.form.site_updated_at') }}" wire:model="site_updated_at" type="date" :error="$errors->first('site_updated_at')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::input label="{{ __('app.webs.form.hosting') }}" wire:model="hosting" :error="$errors->first('hosting')" />
                <x-ds::input label="{{ __('app.webs.form.domain_until') }}" wire:model="domain_until" type="date" :error="$errors->first('domain_until')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::input label="{{ __('app.webs.form.ssl') }}" wire:model="ssl" :error="$errors->first('ssl')" />
                <x-ds::input label="{{ __('app.webs.form.certificate_until') }}" wire:model="certificate_until" type="date" :error="$errors->first('certificate_until')" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <x-ds::input label="{{ __('app.webs.form.gtm_analytics') }}" wire:model="gtm_analytics" :error="$errors->first('gtm_analytics')" />
                <x-ds::input label="{{ __('app.webs.form.priority') }}" wire:model="priority" type="number" :error="$errors->first('priority')" />
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
        x-data="{ open: false }"
        x-on:open-client-web-audit-offcanvas.window="open = true"
        x-on:close-client-web-audit-offcanvas.window="open = false"
        title="Auditoria"
        description="Auditoria técnica do WordPress."
        position="right"
        size="full"
    >
        @if($auditWebId)
            <livewire:clients.web-audit :webId="$auditWebId" :key="'client-web-audit-' . $client->id . '-' . $auditWebId" />
        @else
            <div class="text-sm text-(--text-secondary)">{{ __('app.common.dash') }}</div>
        @endif
    </x-ds::offcanvas>

    <x-ds::offcanvas
        x-data="{ open: $wire.entangle('pagespeedOffcanvasOpen') }"
        x-on:open-client-web-pagespeed-offcanvas.window="open = true"
        x-on:close-client-web-pagespeed-offcanvas.window="open = false"
        title="PageSpeed"
        description="Dashboard e detalhes do PageSpeed Insights."
        position="right"
        size="full"
    >
        @if($pagespeedWebId)
            <livewire:clients.web-page-speed :webId="$pagespeedWebId" :key="'client-web-pagespeed-' . $client->id . '-' . $pagespeedWebId" />
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
