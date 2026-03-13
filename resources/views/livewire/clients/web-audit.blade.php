<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm font-semibold text-(--text-primary)">Auditoria WordPress</div>
            <div class="mt-1 text-xs text-(--text-secondary)">
                {{ $web?->name ?: __('app.common.dash') }}
                @if($general && !empty($general['url']))
                    <span class="mx-2">•</span>
                    <a class="underline text-(--status-info)" href="{{ $general['url'] }}" target="_blank" rel="noopener noreferrer">{{ $general['url'] }}</a>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-ds::button
                type="button"
                variant="secondary"
                icon="solar:refresh-linear"
                wire:click="loadAudit"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="loadAudit"
            >
                <span wire:loading.remove wire:target="loadAudit">Atualizar auditoria</span>
                <span wire:loading wire:target="loadAudit">Atualizando...</span>
            </x-ds::button>
        </div>
    </div>

    @if($loading)
        <x-ds::card>
            <div class="py-8">
                <x-ds::spinner label="Carregando auditoria..." />
            </div>
        </x-ds::card>
    @endif

    @if($errorMessage)
        <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
            {{ $errorMessage }}
        </x-ds::alert>
    @endif

    @php
        $scoreVariant = function ($score) {
            if ($score === null) return 'secondary';
            $pct = (float) $score * 100.0;
            if ($pct >= 90) return 'success';
            if ($pct >= 50) return 'warning';
            return 'danger';
        };

        $scoreLabel = function ($score) {
            if ($score === null) return __('app.common.dash');
            return (string) round(((float) $score) * 100);
        };

        $isMissing = function ($v) {
            return trim((string) ($v ?? '')) === '';
        };
    @endphp

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-ds::card class="lg:col-span-1" title="Resumo" description="Principais alertas da auditoria.">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-(--text-secondary)">Imagens corretas</div>
                    <x-ds::badge variant="success">{{ $imagesOk }}</x-ds::badge>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-(--text-secondary)">Imagens sem ALT</div>
                    <x-ds::badge variant="warning">{{ $imagesWithoutAlt }}</x-ds::badge>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-(--text-secondary)">Imagens não WEBP</div>
                    <x-ds::badge variant="warning">{{ $imagesNotWebp }}</x-ds::badge>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-(--text-secondary)">Páginas sem meta description</div>
                    <x-ds::badge variant="warning">{{ $pagesWithoutMetaDescription }}</x-ds::badge>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-(--text-secondary)">Páginas sem meta keywords</div>
                    <x-ds::badge variant="warning">{{ $pagesWithoutMetaKeywords }}</x-ds::badge>
                </div>
            </div>
        </x-ds::card>

        <x-ds::card class="lg:col-span-2" title="SEÇÃO 1 — Informações gerais" description="Dados gerais do WordPress.">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <div class="text-xs font-medium text-(--text-muted)">URL do site</div>
                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">{{ $general['url'] ?? __('app.common.dash') }}</div>
                </div>

                <div>
                    <div class="text-xs font-medium text-(--text-muted)">Versão do WordPress</div>
                    <div class="mt-1 text-sm text-(--text-secondary)">{{ $general['wp_version'] ?? __('app.common.dash') }}</div>
                </div>

                <div>
                    <div class="text-xs font-medium text-(--text-muted)">Tema ativo</div>
                    <div class="mt-1 text-sm text-(--text-secondary)">{{ $general['active_theme'] ?? __('app.common.dash') }}</div>
                </div>

                <div>
                    <div class="text-xs font-medium text-(--text-muted)">Plugins instalados</div>
                    <div class="mt-1 text-sm text-(--text-secondary)">
                        @if(is_array($general['plugins'] ?? null))
                            {{ count($general['plugins']) }}
                        @else
                            {{ __('app.common.dash') }}
                        @endif
                    </div>
                </div>
            </div>

            @if(!empty($general['notes']) && is_array($general['notes']))
                <div class="mt-4 rounded-lg border border-(--border-subtle) bg-(--surface-hover) p-3">
                    <div class="text-xs font-medium text-(--text-muted)">Observações</div>
                    <div class="mt-2 space-y-1 text-xs text-(--text-secondary)">
                        @foreach($general['notes'] as $note)
                            <div>{{ $note }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-ds::card>
    </div>

    <x-ds::card title="SEÇÃO 2 — Auditoria de imagens" description="Regras: todas imagens devem ser .webp e devem ter alt_text.">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                <div class="text-xs font-medium text-(--text-muted)">Total de imagens</div>
                <div class="mt-1 text-lg font-semibold text-(--text-primary)">{{ $totalImages }}</div>
            </div>
            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                <div class="text-xs font-medium text-(--text-muted)">Imagens corretas</div>
                <div class="mt-1 text-lg font-semibold text-(--status-success)">{{ $imagesOk }}</div>
            </div>
            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                <div class="text-xs font-medium text-(--text-muted)">Imagens com erro</div>
                <div class="mt-1 text-lg font-semibold text-(--status-warning)">{{ $imagesError }}</div>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">URL</th>
                        <th class="px-4 py-3 font-semibold">Ext</th>
                        <th class="px-4 py-3 font-semibold">Largura</th>
                        <th class="px-4 py-3 font-semibold">Altura</th>
                        <th class="px-4 py-3 font-semibold">ALT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($images as $img)
                        @php
                            $ext = strtolower((string) ($img['ext'] ?? ''));
                            $alt = trim((string) ($img['alt_text'] ?? ''));
                            $badWebp = $ext !== 'webp';
                            $badAlt = $alt === '';
                        @endphp
                        <tr class="border-b border-(--border-subtle) {{ ($badWebp || $badAlt) ? 'bg-(--status-warning-light)' : '' }}">
                            <td class="px-4 py-3 text-(--text-secondary)">{{ $img['id'] ?? __('app.common.dash') }}</td>
                            <td class="px-4 py-3">
                                @if(!empty($img['url']))
                                    <a class="underline text-(--status-info)" href="{{ $img['url'] }}" target="_blank" rel="noopener noreferrer">{{ $img['url'] }}</a>
                                @else
                                    <span class="text-(--text-secondary)">{{ __('app.common.dash') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-ds::badge variant="{{ $badWebp ? 'warning' : 'success' }}">{{ $ext ?: __('app.common.dash') }}</x-ds::badge>
                            </td>
                            <td class="px-4 py-3 text-(--text-secondary)">{{ $img['width'] ?? __('app.common.dash') }}</td>
                            <td class="px-4 py-3 text-(--text-secondary)">{{ $img['height'] ?? __('app.common.dash') }}</td>
                            <td class="px-4 py-3">
                                @if($badAlt)
                                    <x-ds::badge variant="warning">Sem ALT</x-ds::badge>
                                @else
                                    <span class="text-(--text-secondary)">{{ $alt }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-(--text-secondary)">
                                Nenhuma imagem encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ds::card>

    <x-ds::card title="SEÇÃO 3 — Páginas do site" description="Lista de páginas e metadados SEO (via Yoast, quando disponível).">
        <div class="mt-2 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Título</th>
                        <th class="px-4 py-3 font-semibold">Slug</th>
                        <th class="px-4 py-3 font-semibold">URL</th>
                        <th class="px-4 py-3 font-semibold">Meta description</th>
                        <th class="px-4 py-3 font-semibold">Meta keywords</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $p)
                        @php
                            $missingDesc = $isMissing($p['meta_description'] ?? null);
                            $missingKw = $isMissing($p['meta_keywords'] ?? null);
                        @endphp
                        <tr class="border-b border-(--border-subtle) {{ ($missingDesc || $missingKw) ? 'bg-(--status-warning-light)' : '' }}">
                            <td class="px-4 py-3 text-(--text-primary)">{{ $p['title'] ?? __('app.common.dash') }}</td>
                            <td class="px-4 py-3 text-(--text-secondary)">{{ $p['slug'] ?? __('app.common.dash') }}</td>
                            <td class="px-4 py-3">
                                @if(!empty($p['url']))
                                    <a class="underline text-(--status-info)" href="{{ $p['url'] }}" target="_blank" rel="noopener noreferrer">{{ $p['url'] }}</a>
                                @else
                                    <span class="text-(--text-secondary)">{{ __('app.common.dash') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($missingDesc)
                                    <x-ds::badge variant="warning">Faltando</x-ds::badge>
                                @else
                                    <span class="text-(--text-secondary)">{{ $p['meta_description'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($missingKw)
                                    <x-ds::badge variant="warning">Faltando</x-ds::badge>
                                @else
                                    <span class="text-(--text-secondary)">{{ $p['meta_keywords'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-ds::badge variant="secondary">{{ $p['status'] ?? __('app.common.dash') }}</x-ds::badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-(--text-secondary)">
                                Nenhuma página encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ds::card>
</div>
