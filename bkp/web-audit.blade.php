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
        $isMissing = fn($v) => trim((string) ($v ?? '')) === '';

        $formatBytes = function ($bytes) {
            if ($bytes === null) return null;
            if ($bytes < 1024) return $bytes . ' B';
            if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
            return round($bytes / 1048576, 2) . ' MB';
        };

        $bytesVariant = function ($bytes) {
            if ($bytes === null) return 'secondary';
            if ($bytes > 500000) return 'danger';
            if ($bytes > 100000) return 'warning';
            return 'success';
        };

        $descLengthVariant = function ($desc) {
            if ($desc === null || trim($desc) === '') return null;
            $len = mb_strlen(trim($desc));
            if ($len > 160) return 'danger';
            if ($len < 50) return 'warning';
            return 'success';
        };

        $descLengthLabel = function ($desc) {
            if ($desc === null || trim($desc) === '') return null;
            return mb_strlen(trim($desc)) . ' chars';
        };
    @endphp

    {{-- RESUMO --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-ds::card class="lg:col-span-1" title="Resumo" description="Principais indicadores da auditoria.">
            <div class="space-y-4">

                {{-- Grupo: Imagens --}}
                <div>
                    <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">Imagens</div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Total</div>
                            <x-ds::badge variant="secondary">{{ $totalImages }}</x-ds::badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Corretas (WebP + ALT)</div>
                            <x-ds::badge variant="{{ $imagesOk === $totalImages && $totalImages > 0 ? 'success' : ($imagesOk > 0 ? 'success' : 'secondary') }}">{{ $imagesOk }}</x-ds::badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Sem ALT</div>
                            <x-ds::badge variant="{{ $imagesWithoutAlt > 0 ? 'warning' : 'success' }}">{{ $imagesWithoutAlt }}</x-ds::badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Não WEBP</div>
                            <x-ds::badge variant="{{ $imagesNotWebp > 0 ? 'warning' : 'success' }}">{{ $imagesNotWebp }}</x-ds::badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Sem dimensões (W/H)</div>
                            <x-ds::badge variant="{{ $imagesWithoutDimensions > 0 ? 'warning' : 'success' }}">{{ $imagesWithoutDimensions }}</x-ds::badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Grandes (&gt;500 KB)</div>
                            <x-ds::badge variant="{{ $imagesLarge > 0 ? 'danger' : 'success' }}">{{ $imagesLarge }}</x-ds::badge>
                        </div>
                    </div>
                </div>

                <hr class="border-(--border-subtle)">

                {{-- Grupo: Páginas SEO --}}
                <div>
                    <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">
                        Páginas SEO
                        @if($pagesHtmlFetched > 0)
                            <span class="ml-1 font-normal normal-case text-(--text-muted)">(HTML: {{ $pagesHtmlFetched }} pág.)</span>
                        @endif
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Sem meta description</div>
                            <x-ds::badge variant="{{ $pagesWithoutMetaDescription > 0 ? 'warning' : 'success' }}">{{ $pagesWithoutMetaDescription }}</x-ds::badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Sem meta keywords</div>
                            <x-ds::badge variant="{{ $pagesWithoutMetaKeywords > 0 ? 'warning' : 'success' }}">{{ $pagesWithoutMetaKeywords }}</x-ds::badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Description muito longa (&gt;160)</div>
                            <x-ds::badge variant="{{ $pagesDescriptionTooLong > 0 ? 'danger' : 'success' }}">{{ $pagesDescriptionTooLong }}</x-ds::badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Description muito curta (&lt;50)</div>
                            <x-ds::badge variant="{{ $pagesDescriptionTooShort > 0 ? 'warning' : 'success' }}">{{ $pagesDescriptionTooShort }}</x-ds::badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">Descriptions duplicadas</div>
                            <x-ds::badge variant="{{ $pagesDuplicateDescription > 0 ? 'warning' : 'success' }}">{{ $pagesDuplicateDescription }}</x-ds::badge>
                        </div>
                        @if($pagesHtmlFetched > 0)
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-(--text-secondary)">Sem H1</div>
                                <x-ds::badge variant="{{ $pagesWithoutH1 > 0 ? 'warning' : 'success' }}">{{ $pagesWithoutH1 }}</x-ds::badge>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-(--text-secondary)">Sem title tag</div>
                                <x-ds::badge variant="{{ $pagesWithoutTitleTag > 0 ? 'warning' : 'success' }}">{{ $pagesWithoutTitleTag }}</x-ds::badge>
                            </div>
                        @endif
                    </div>
                </div>

                <hr class="border-(--border-subtle)">

                {{-- Grupo: WordPress --}}
                <div>
                    <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">WordPress</div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-(--text-secondary)">SSL / HTTPS</div>
                            <x-ds::badge variant="{{ $sslEnabled ? 'success' : 'danger' }}">{{ $sslEnabled ? 'HTTPS' : 'HTTP' }}</x-ds::badge>
                        </div>
                        @if(!empty($general['wp_version']))
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-(--text-secondary)">Versão WP</div>
                                <x-ds::badge variant="secondary">{{ $general['wp_version'] }}</x-ds::badge>
                            </div>
                        @endif
                        @if(is_array($general['plugins'] ?? null))
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-(--text-secondary)">Plugins inativos</div>
                                <x-ds::badge variant="{{ $inactivePlugins > 0 ? 'warning' : 'success' }}">{{ $inactivePlugins }}</x-ds::badge>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </x-ds::card>

        {{-- SEÇÃO 1 — Informações gerais --}}
        <x-ds::card class="lg:col-span-2" title="SEÇÃO 1 — Informações gerais" description="Dados gerais do WordPress.">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <div class="text-xs font-medium text-(--text-muted)">URL do site</div>
                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">{{ $general['url'] ?? __('app.common.dash') }}</div>
                </div>

                <div>
                    <div class="text-xs font-medium text-(--text-muted)">SSL / HTTPS</div>
                    <div class="mt-1">
                        @if($sslEnabled)
                            <x-ds::badge variant="success">HTTPS ativo</x-ds::badge>
                        @else
                            <x-ds::badge variant="danger">Sem HTTPS — risco de segurança</x-ds::badge>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium text-(--text-muted)">Versão do WordPress</div>
                    <div class="mt-1 text-sm text-(--text-secondary)">
                        @if(!empty($general['wp_version']))
                            <span class="font-medium text-(--text-primary)">{{ $general['wp_version'] }}</span>
                            <span class="ml-2 text-xs text-(--text-muted)">(detectado via meta generator)</span>
                        @else
                            {{ __('app.common.dash') }}
                        @endif
                    </div>
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
                            @if($inactivePlugins > 0)
                                <x-ds::badge class="ml-2" variant="warning">{{ $inactivePlugins }} inativos</x-ds::badge>
                            @endif
                        @else
                            {{ __('app.common.dash') }}
                        @endif
                    </div>
                </div>

                <div>
                    <div class="text-xs font-medium text-(--text-muted)">Nome do site</div>
                    <div class="mt-1 text-sm text-(--text-secondary)">{{ $general['wp']['name'] ?? __('app.common.dash') }}</div>
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

    {{-- SEÇÃO 2 — Auditoria de imagens --}}
    <x-ds::card title="SEÇÃO 2 — Auditoria de imagens" description="Regras: todas imagens devem ser .webp, ter alt_text, ter dimensões definidas e tamanho razoável.">
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6">
            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                <div class="text-xs font-medium text-(--text-muted)">Total</div>
                <div class="mt-1 text-lg font-semibold text-(--text-primary)">{{ $totalImages }}</div>
            </div>
            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                <div class="text-xs font-medium text-(--text-muted)">Corretas</div>
                <div class="mt-1 text-lg font-semibold text-(--status-success)">{{ $imagesOk }}</div>
            </div>
            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                <div class="text-xs font-medium text-(--text-muted)">Com erro</div>
                <div class="mt-1 text-lg font-semibold text-(--status-warning)">{{ $imagesError }}</div>
            </div>
            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                <div class="text-xs font-medium text-(--text-muted)">Não WEBP</div>
                <div class="mt-1 text-lg font-semibold text-(--status-warning)">{{ $imagesNotWebp }}</div>
            </div>
            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                <div class="text-xs font-medium text-(--text-muted)">Sem dimensões</div>
                <div class="mt-1 text-lg font-semibold {{ $imagesWithoutDimensions > 0 ? 'text-(--status-warning)' : 'text-(--text-secondary)' }}">{{ $imagesWithoutDimensions }}</div>
            </div>
            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                <div class="text-xs font-medium text-(--text-muted)">Grandes (&gt;500KB)</div>
                <div class="mt-1 text-lg font-semibold {{ $imagesLarge > 0 ? 'text-(--status-danger)' : 'text-(--text-secondary)' }}">{{ $imagesLarge }}</div>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">URL</th>
                        <th class="px-4 py-3 font-semibold">Ext</th>
                        <th class="px-4 py-3 font-semibold">Tamanho</th>
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
                            $width = $img['width'] ?? null;
                            $height = $img['height'] ?? null;
                            $filesize = $img['filesize'] ?? null;
                            $badWebp = $ext !== 'webp';
                            $badAlt = $alt === '';
                            $badDims = $width === null || $height === null;
                            $hasWarning = $badWebp || $badAlt || $badDims || ($filesize !== null && $filesize > 500000);
                        @endphp
                        <tr class="border-b border-(--border-subtle) {{ $hasWarning ? 'bg-(--status-warning-light)' : '' }}">
                            <td class="px-4 py-3 text-(--text-secondary)">{{ $img['id'] ?? __('app.common.dash') }}</td>
                            <td class="max-w-xs px-4 py-3">
                                @if(!empty($img['url']))
                                    @php $imgPages = $imagePageMap[$img['url']] ?? []; @endphp
                                    <div class="relative inline-block max-w-full" x-data="{ open: false }">
                                        <a
                                            class="block truncate underline text-(--status-info)"
                                            href="{{ $img['url'] }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            title="{{ $img['url'] }}"
                                            x-on:mouseenter="open = true"
                                            x-on:mouseleave="open = false"
                                        >{{ $img['url'] }}</a>

                                        @if(!empty($imgPages))
                                            <div
                                                x-show="open"
                                                x-cloak
                                                x-on:mouseenter="open = true"
                                                x-on:mouseleave="open = false"
                                                class="absolute left-0 top-full z-50 mt-1 w-72 rounded-lg border border-(--border-default) bg-(--surface-card) p-2 shadow-lg"
                                            >
                                                <div class="mb-1.5 text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">Encontrada em {{ count($imgPages) }} {{ count($imgPages) === 1 ? 'página' : 'páginas' }}:</div>
                                                <div class="space-y-1">
                                                    @foreach($imgPages as $pg)
                                                        <div class="truncate text-xs">
                                                            <a href="{{ $pg['url'] }}" target="_blank" rel="noopener noreferrer" class="underline text-(--status-info)" title="{{ $pg['url'] }}">{{ $pg['title'] ?: $pg['url'] }}</a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-(--text-secondary)">{{ __('app.common.dash') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-ds::badge variant="{{ $badWebp ? 'warning' : 'success' }}">{{ $ext ?: __('app.common.dash') }}</x-ds::badge>
                            </td>
                            <td class="px-4 py-3">
                                @if($filesize !== null)
                                    <x-ds::badge variant="{{ $bytesVariant($filesize) }}">{{ $formatBytes($filesize) }}</x-ds::badge>
                                @else
                                    <span class="text-(--text-secondary)">{{ __('app.common.dash') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($width !== null)
                                    <span class="text-(--text-secondary)">{{ $width }}</span>
                                @else
                                    <x-ds::badge variant="warning">?</x-ds::badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($height !== null)
                                    <span class="text-(--text-secondary)">{{ $height }}</span>
                                @else
                                    <x-ds::badge variant="warning">?</x-ds::badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($badAlt)
                                    <x-ds::badge variant="warning">Sem ALT</x-ds::badge>
                                @else
                                    <span class="text-(--text-secondary)" title="{{ $alt }}">{{ Str::limit($alt, 40) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-(--text-secondary)">
                                Nenhuma imagem encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ds::card>

    {{-- SEÇÃO 3 — Páginas do site --}}
    <x-ds::card title="SEÇÃO 3 — Páginas do site" description="SEO on-page: meta description, keywords, H1 e title tag. H1/title disponíveis para páginas com HTML analisado.">

        @if($pagesHtmlFetched > 0)
            <div class="mb-4 rounded-lg border border-(--border-subtle) bg-(--surface-hover) px-4 py-2 text-xs text-(--text-muted)">
                HTML analisado em <strong>{{ $pagesHtmlFetched }}</strong> de <strong>{{ count($pages) }}</strong> páginas.
                H1 e title tag são exibidos apenas para páginas com HTML capturado.
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Título da página</th>
                        <th class="px-4 py-3 font-semibold">URL</th>
                        <th class="px-4 py-3 font-semibold">Meta description</th>
                        <th class="px-4 py-3 font-semibold">H1</th>
                        <th class="px-4 py-3 font-semibold">Title tag</th>
                        <th class="px-4 py-3 font-semibold">Meta keywords</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $p)
                        @php
                            $missingDesc = $isMissing($p['meta_description'] ?? null);
                            $missingKw = $isMissing($p['meta_keywords'] ?? null);
                            $missingH1 = ($p['html_fetched'] ?? false) && $isMissing($p['h1'] ?? null);
                            $missingTitle = ($p['html_fetched'] ?? false) && $isMissing($p['title_tag'] ?? null);
                            $hasWarning = $missingDesc || $missingKw || $missingH1 || $missingTitle;
                            $descVariant = $descLengthVariant($p['meta_description'] ?? null);
                            $descLength = $descLengthLabel($p['meta_description'] ?? null);
                        @endphp
                        <tr class="border-b border-(--border-subtle) {{ $hasWarning ? 'bg-(--status-warning-light)' : '' }}">
                            <td class="max-w-xs px-4 py-3 font-medium text-(--text-primary)">
                                <span title="{{ $p['title'] ?? '' }}">{{ Str::limit($p['title'] ?? __('app.common.dash'), 45) }}</span>
                            </td>
                            <td class="max-w-xs px-4 py-3">
                                @if(!empty($p['url']))
                                    <a class="block truncate underline text-(--status-info)" href="{{ $p['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $p['url'] }}">{{ $p['url'] }}</a>
                                @else
                                    <span class="text-(--text-secondary)">{{ __('app.common.dash') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($missingDesc)
                                    <x-ds::badge variant="warning">Faltando</x-ds::badge>
                                @else
                                    <div class="space-y-1">
                                        <div class="max-w-xs text-xs text-(--text-secondary)" title="{{ $p['meta_description'] }}">{{ Str::limit($p['meta_description'], 60) }}</div>
                                        @if($descVariant && $descLength)
                                            <x-ds::badge variant="{{ $descVariant }}">{{ $descLength }}</x-ds::badge>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if(!($p['html_fetched'] ?? false))
                                    <span class="text-xs text-(--text-muted)">—</span>
                                @elseif($missingH1)
                                    <x-ds::badge variant="warning">Sem H1</x-ds::badge>
                                @else
                                    <span class="max-w-xs block truncate text-xs text-(--text-secondary)" title="{{ $p['h1'] ?? '' }}">{{ Str::limit($p['h1'] ?? '', 40) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if(!($p['html_fetched'] ?? false))
                                    <span class="text-xs text-(--text-muted)">—</span>
                                @elseif($missingTitle)
                                    <x-ds::badge variant="danger">Sem title</x-ds::badge>
                                @else
                                    <span class="max-w-xs block truncate text-xs text-(--text-secondary)" title="{{ $p['title_tag'] ?? '' }}">{{ Str::limit($p['title_tag'] ?? '', 40) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($missingKw)
                                    <x-ds::badge variant="warning">Faltando</x-ds::badge>
                                @else
                                    <span class="text-xs text-(--text-secondary)" title="{{ $p['meta_keywords'] }}">{{ Str::limit($p['meta_keywords'] ?? '', 30) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <x-ds::badge variant="secondary">{{ $p['status'] ?? __('app.common.dash') }}</x-ds::badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-(--text-secondary)">
                                Nenhuma página encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ds::card>

    {{-- SEÇÃO 4 — Plugins (somente se disponível via API) --}}
    @if(is_array($general['plugins'] ?? null) && count($general['plugins']) > 0)
        <x-ds::card title="SEÇÃO 4 — Plugins instalados" description="Lista de plugins obtida via WordPress REST API.">
            @php
                $activePlugins = collect($general['plugins'])->where('status', 'active');
                $inactivePluginsList = collect($general['plugins'])->where('status', '!=', 'active');
            @endphp

            <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                    <div class="text-xs font-medium text-(--text-muted)">Total de plugins</div>
                    <div class="mt-1 text-lg font-semibold text-(--text-primary)">{{ count($general['plugins']) }}</div>
                </div>
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                    <div class="text-xs font-medium text-(--text-muted)">Ativos</div>
                    <div class="mt-1 text-lg font-semibold text-(--status-success)">{{ $activePlugins->count() }}</div>
                </div>
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                    <div class="text-xs font-medium text-(--text-muted)">Inativos</div>
                    <div class="mt-1 text-lg font-semibold {{ $inactivePluginsList->count() > 0 ? 'text-(--status-warning)' : 'text-(--text-secondary)' }}">{{ $inactivePluginsList->count() }}</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Plugin</th>
                            <th class="px-4 py-3 font-semibold">Versão</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($general['plugins'] as $plugin)
                            @php
                                $isInactive = ($plugin['status'] ?? '') !== 'active';
                            @endphp
                            <tr class="border-b border-(--border-subtle) {{ $isInactive ? 'bg-(--status-warning-light)' : '' }}">
                                <td class="px-4 py-3 font-medium text-(--text-primary)">{{ $plugin['name'] ?? $plugin['plugin'] ?? __('app.common.dash') }}</td>
                                <td class="px-4 py-3 text-(--text-secondary)">{{ $plugin['version'] ?? __('app.common.dash') }}</td>
                                <td class="px-4 py-3">
                                    <x-ds::badge variant="{{ $isInactive ? 'warning' : 'success' }}">{{ $plugin['status'] ?? __('app.common.dash') }}</x-ds::badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ds::card>
    @endif

</div>
