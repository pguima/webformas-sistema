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

    {{-- SEÇÃO 4 — Segurança WordPress --}}
    <x-ds::card title="SEÇÃO 4 — Segurança WordPress" description="Verificações específicas de segurança para sites WordPress: arquivos expostos, enumeração de usuários, debug mode, etc.">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                @if(!empty($wpSecurityChecks))
                    @php
                        $wpPass = collect($wpSecurityChecks)->where('status', 'pass')->count();
                        $wpWarn = collect($wpSecurityChecks)->where('status', 'warn')->count();
                        $wpFail = collect($wpSecurityChecks)->where('status', 'fail')->count();
                        $wpVariant = $wpSecurityScore >= 80 ? 'success' : ($wpSecurityScore >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="flex items-center gap-2">
                        <div class="text-2xl font-bold text-(--text-primary)">{{ $wpSecurityScore }}</div>
                        <x-ds::badge variant="{{ $wpVariant }}">{{ $wpSecurityScore >= 80 ? 'Seguro' : ($wpSecurityScore >= 50 ? 'Vulnerável' : 'Crítico') }}</x-ds::badge>
                    </div>
                    <div class="flex gap-3 text-sm">
                        <span class="flex items-center gap-1.5 text-(--status-success)"><span class="h-2 w-2 rounded-full bg-(--status-success)"></span> {{ $wpPass }}</span>
                        <span class="flex items-center gap-1.5 text-(--status-warning)"><span class="h-2 w-2 rounded-full bg-(--status-warning)"></span> {{ $wpWarn }}</span>
                        <span class="flex items-center gap-1.5 text-(--status-danger)"><span class="h-2 w-2 rounded-full bg-(--status-danger)"></span> {{ $wpFail }}</span>
                    </div>
                @endif
            </div>

            <x-ds::button
                type="button"
                variant="secondary"
                icon="solar:shield-warning-linear"
                wire:click="checkWpSecurity"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="checkWpSecurity"
            >
                <span wire:loading.remove wire:target="checkWpSecurity">{{ empty($wpSecurityChecks) ? 'Verificar segurança WP' : 'Re-verificar' }}</span>
                <span wire:loading wire:target="checkWpSecurity">Verificando...</span>
            </x-ds::button>
        </div>

        @if($wpSecurityError)
            <x-ds::alert variant="danger" icon="solar:danger-circle-linear" class="mb-4">{{ $wpSecurityError }}</x-ds::alert>
        @endif

        @if(!empty($wpSecurityChecks))
            <div class="space-y-2">
                @foreach($wpSecurityChecks as $check)
                    @php
                        $statusIcon   = match($check['status']) { 'pass' => 'solar:check-circle-linear', 'warn' => 'solar:danger-triangle-linear', default => 'solar:close-circle-linear' };
                        $statusColor  = match($check['status']) { 'pass' => 'text-(--status-success)', 'warn' => 'text-(--status-warning)', default => 'text-(--status-danger)' };
                        $severityVariant = match($check['severity']) { 'critical' => 'danger', 'high' => 'warning', 'medium' => 'secondary', default => 'secondary' };
                    @endphp
                    <div x-data="{ open: false }" class="rounded-lg border border-(--border-subtle) bg-(--surface-card)">
                        <div class="flex items-start gap-3 px-4 py-3 cursor-pointer" @click="open = !open">
                            <iconify-icon icon="{{ $statusIcon }}" class="{{ $statusColor }} mt-0.5 shrink-0 text-lg"></iconify-icon>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium text-sm text-(--text-primary)">{{ $check['name'] }}</span>
                                    <x-ds::badge variant="{{ $severityVariant }}">{{ $check['severity'] }}</x-ds::badge>
                                </div>
                                <div class="mt-1 text-xs {{ $check['status'] !== 'pass' ? 'text-(--status-warning)' : 'text-(--text-secondary)' }}">{{ $check['value'] }}</div>
                            </div>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-(--text-muted) shrink-0 transition-transform mt-1" :class="open ? 'rotate-180' : ''"></iconify-icon>
                        </div>
                        @if($check['status'] !== 'pass')
                            <div x-show="open" x-cloak class="border-t border-(--border-subtle) px-4 py-3">
                                <div class="flex gap-2 text-xs text-(--text-secondary)">
                                    <iconify-icon icon="solar:lightbulb-linear" class="shrink-0 text-(--status-info) mt-0.5"></iconify-icon>
                                    {{ $check['tip'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif(!$wpSecurityLoading && !$wpSecurityError)
            <div class="py-6 text-center text-sm text-(--text-muted)">Clique em "Verificar segurança WP" para analisar vulnerabilidades comuns do WordPress.</div>
        @endif
    </x-ds::card>

    {{-- SEÇÃO 5 — Sitemap.xml & Robots.txt --}}
    <x-ds::card title="SEÇÃO 5 — Sitemap.xml & Robots.txt" description="Verifica a presença, formato e qualidade do sitemap e das regras de rastreamento.">
        <div class="flex justify-end mb-4">
            <x-ds::button
                type="button"
                variant="secondary"
                icon="solar:map-linear"
                wire:click="analyzeSitemapRobots"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="analyzeSitemapRobots"
            >
                <span wire:loading.remove wire:target="analyzeSitemapRobots">{{ $sitemapData || $robotsData ? 'Re-analisar' : 'Analisar Sitemap & Robots' }}</span>
                <span wire:loading wire:target="analyzeSitemapRobots">Analisando...</span>
            </x-ds::button>
        </div>

        @if($sitemapError)
            <x-ds::alert variant="danger" icon="solar:danger-circle-linear" class="mb-4">{{ $sitemapError }}</x-ds::alert>
        @endif

        @if($robotsData || $sitemapData)
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                {{-- robots.txt --}}
                @if($robotsData)
                    <div class="rounded-lg border {{ $robotsData['found'] ? 'border-(--border-subtle)' : 'border-(--status-warning)' }} bg-(--surface-card) p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <iconify-icon icon="{{ $robotsData['found'] ? 'solar:check-circle-linear' : 'solar:close-circle-linear' }}" class="{{ $robotsData['found'] ? 'text-(--status-success)' : 'text-(--status-danger)' }} text-lg"></iconify-icon>
                            <span class="font-semibold text-sm text-(--text-primary)">robots.txt</span>
                            <x-ds::badge variant="{{ $robotsData['found'] ? 'success' : 'danger' }}">{{ $robotsData['found'] ? 'Encontrado' : 'Não encontrado' }}</x-ds::badge>
                        </div>
                        @if($robotsData['found'])
                            <div class="space-y-1.5 text-xs text-(--text-secondary)">
                                <div>Regras: <strong>{{ $robotsData['rules_count'] ?? 0 }}</strong></div>
                                @if(!empty($robotsData['sitemap_urls']))
                                    <div>Sitemaps referenciados: <strong>{{ count($robotsData['sitemap_urls']) }}</strong></div>
                                @endif
                                @if($robotsData['disallow_all'] ?? false)
                                    <x-ds::badge variant="danger">Bloqueia todos os crawlers!</x-ds::badge>
                                @endif
                            </div>
                            @if(!empty($robotsData['issues']))
                                <div class="mt-3 space-y-1">
                                    @foreach($robotsData['issues'] as $issue)
                                        <div class="text-xs text-(--status-danger)">⚠ {{ $issue }}</div>
                                    @endforeach
                                </div>
                            @endif
                            <details class="mt-3">
                                <summary class="cursor-pointer text-xs text-(--text-muted) hover:text-(--text-primary)">Ver conteúdo</summary>
                                <pre class="mt-2 text-xs text-(--text-secondary) overflow-x-auto whitespace-pre-wrap">{{ $robotsData['content'] ?? '' }}</pre>
                            </details>
                        @else
                            @foreach($robotsData['issues'] ?? [] as $issue)
                                <div class="text-xs text-(--status-warning)">{{ $issue }}</div>
                            @endforeach
                        @endif
                    </div>
                @endif

                {{-- sitemap.xml --}}
                @if($sitemapData)
                    <div class="rounded-lg border {{ $sitemapData['found'] ? 'border-(--border-subtle)' : 'border-(--status-warning)' }} bg-(--surface-card) p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <iconify-icon icon="{{ $sitemapData['found'] ? 'solar:check-circle-linear' : 'solar:close-circle-linear' }}" class="{{ $sitemapData['found'] ? 'text-(--status-success)' : 'text-(--status-danger)' }} text-lg"></iconify-icon>
                            <span class="font-semibold text-sm text-(--text-primary)">sitemap.xml</span>
                            <x-ds::badge variant="{{ $sitemapData['found'] ? 'success' : 'danger' }}">{{ $sitemapData['found'] ? 'Encontrado' : 'Não encontrado' }}</x-ds::badge>
                        </div>
                        @if($sitemapData['found'])
                            <div class="space-y-1.5 text-xs text-(--text-secondary)">
                                @if($sitemapData['url'] ?? null)
                                    <div><a class="underline text-(--status-info)" href="{{ $sitemapData['url'] }}" target="_blank">{{ $sitemapData['url'] }}</a></div>
                                @endif
                                @if($sitemapData['is_index'] ?? false)
                                    <x-ds::badge variant="secondary">Sitemap Index</x-ds::badge>
                                    <div>Sitemaps filhos: <strong>{{ count($sitemapData['sitemap_files'] ?? []) }}</strong></div>
                                @else
                                    <div>URLs encontradas: <strong>{{ $sitemapData['url_count'] ?? 0 }}</strong></div>
                                @endif
                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    <x-ds::badge variant="{{ $sitemapData['has_lastmod'] ? 'success' : 'warning' }}">{{ $sitemapData['has_lastmod'] ? '✓ lastmod' : '✗ sem lastmod' }}</x-ds::badge>
                                    @if($sitemapData['has_images'] ?? false)
                                        <x-ds::badge variant="success">✓ image sitemap</x-ds::badge>
                                    @endif
                                    @if($sitemapData['has_videos'] ?? false)
                                        <x-ds::badge variant="success">✓ video sitemap</x-ds::badge>
                                    @endif
                                </div>
                            </div>
                            @if(!empty($sitemapData['issues']))
                                <div class="mt-3 space-y-1">
                                    @foreach($sitemapData['issues'] as $issue)
                                        <div class="text-xs text-(--status-warning)">⚠ {{ $issue }}</div>
                                    @endforeach
                                </div>
                            @endif
                            @if(!empty($sitemapData['sample_urls']))
                                <div class="mt-3">
                                    <div class="text-xs font-medium text-(--text-muted) mb-1">Amostra de URLs ({{ count($sitemapData['sample_urls']) }}):</div>
                                    @foreach($sitemapData['sample_urls'] as $url)
                                        <div class="truncate text-xs text-(--text-secondary)">{{ $url }}</div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            @foreach($sitemapData['issues'] ?? [] as $issue)
                                <div class="text-xs text-(--status-warning)">{{ $issue }}</div>
                            @endforeach
                        @endif
                    </div>
                @endif
            </div>
        @elseif(!$sitemapLoading && !$sitemapError)
            <div class="py-6 text-center text-sm text-(--text-muted)">Clique em "Analisar Sitemap & Robots" para verificar esses arquivos essenciais.</div>
        @endif
    </x-ds::card>

    {{-- SEÇÃO 6 — Análise de Conteúdo com IA --}}
    <x-ds::card title="SEÇÃO 6 — Análise de conteúdo com IA" description="Usa GPT-4o-mini para analisar qualidade do conteúdo, tom de voz, palavras-chave e oportunidades de melhoria.">
        <div class="flex items-center justify-between mb-4">
            <div>
                @if($aiAnalysis)
                    @php $aiScore = (int) ($aiAnalysis['score_qualidade'] ?? 0); @endphp
                    <div class="flex items-center gap-2">
                        <div class="text-2xl font-bold text-(--text-primary)">{{ $aiScore }}</div>
                        <x-ds::badge variant="{{ $aiScore >= 70 ? 'success' : ($aiScore >= 40 ? 'warning' : 'danger') }}">
                            {{ $aiScore >= 70 ? 'Bom conteúdo' : ($aiScore >= 40 ? 'Conteúdo mediano' : 'Conteúdo fraco') }}
                        </x-ds::badge>
                    </div>
                @elseif(!config('services.openai.key'))
                    <span class="text-xs text-(--status-warning)">Configure OPENAI_API_KEY no .env para usar esta funcionalidade.</span>
                @endif
            </div>

            <x-ds::button
                type="button"
                variant="secondary"
                icon="solar:stars-minimalistic-linear"
                wire:click="analyzeContentWithAi"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="analyzeContentWithAi"
            >
                <span wire:loading.remove wire:target="analyzeContentWithAi">{{ $aiAnalysis ? 'Re-analisar com IA' : 'Analisar com IA' }}</span>
                <span wire:loading wire:target="analyzeContentWithAi">Analisando com IA...</span>
            </x-ds::button>
        </div>

        @if($aiError)
            <x-ds::alert variant="danger" icon="solar:danger-circle-linear" class="mb-4">{{ $aiError }}</x-ds::alert>
        @endif

        @if($aiAnalysis)
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                {{-- Resumo e diagnóstico geral --}}
                <div class="space-y-3">
                    @if($aiAnalysis['resumo'] ?? null)
                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                            <div class="text-xs font-semibold uppercase tracking-wider text-(--text-muted) mb-1">Resumo</div>
                            <p class="text-sm text-(--text-secondary)">{{ $aiAnalysis['resumo'] }}</p>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-2">
                        @if($aiAnalysis['tom_de_voz'] ?? null)
                            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3">
                                <div class="text-xs text-(--text-muted)">Tom de voz</div>
                                <div class="mt-1 text-sm font-medium text-(--text-primary)">{{ $aiAnalysis['tom_de_voz'] }}</div>
                            </div>
                        @endif
                        @if($aiAnalysis['intenção_de_busca'] ?? null)
                            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3">
                                <div class="text-xs text-(--text-muted)">Intenção de busca</div>
                                <div class="mt-1 text-sm font-medium text-(--text-primary) capitalize">{{ $aiAnalysis['intenção_de_busca'] }}</div>
                            </div>
                        @endif
                        @if($aiAnalysis['legibilidade'] ?? null)
                            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3 col-span-2">
                                <div class="text-xs text-(--text-muted)">Legibilidade</div>
                                <div class="mt-1 text-sm text-(--text-secondary)">{{ $aiAnalysis['legibilidade'] }}</div>
                            </div>
                        @endif
                        @if($aiAnalysis['publico_alvo_estimado'] ?? null)
                            <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3 col-span-2">
                                <div class="text-xs text-(--text-muted)">Público-alvo estimado</div>
                                <div class="mt-1 text-sm text-(--text-secondary)">{{ $aiAnalysis['publico_alvo_estimado'] }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Palavras-chave e recomendações --}}
                <div class="space-y-3">
                    @if(!empty($aiAnalysis['palavras_chave_detectadas']))
                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                            <div class="text-xs font-semibold uppercase tracking-wider text-(--text-muted) mb-2">Palavras-chave detectadas</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($aiAnalysis['palavras_chave_detectadas'] as $kw)
                                    <x-ds::badge variant="secondary">{{ $kw }}</x-ds::badge>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if(!empty($aiAnalysis['palavras_chave_sugeridas']))
                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                            <div class="text-xs font-semibold uppercase tracking-wider text-(--text-muted) mb-2">Palavras-chave sugeridas</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($aiAnalysis['palavras_chave_sugeridas'] as $kw)
                                    <x-ds::badge variant="info">{{ $kw }}</x-ds::badge>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                @if(!empty($aiAnalysis['pontos_fortes']))
                    <div class="rounded-lg border border-(--status-success) bg-(--surface-card) p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-(--status-success) mb-2">Pontos fortes</div>
                        <ul class="space-y-1.5">
                            @foreach($aiAnalysis['pontos_fortes'] as $item)
                                <li class="flex gap-2 text-sm text-(--text-secondary)">
                                    <iconify-icon icon="solar:check-circle-linear" class="shrink-0 text-(--status-success) mt-0.5"></iconify-icon>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(!empty($aiAnalysis['oportunidades_melhoria']))
                    <div class="rounded-lg border border-(--status-warning) bg-(--surface-card) p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-(--status-warning) mb-2">Oportunidades</div>
                        <ul class="space-y-1.5">
                            @foreach($aiAnalysis['oportunidades_melhoria'] as $item)
                                <li class="flex gap-2 text-sm text-(--text-secondary)">
                                    <iconify-icon icon="solar:danger-triangle-linear" class="shrink-0 text-(--status-warning) mt-0.5"></iconify-icon>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(!empty($aiAnalysis['recomendacoes_conteudo']))
                    <div class="rounded-lg border border-(--status-info) bg-(--surface-card) p-4">
                        <div class="text-xs font-semibold uppercase tracking-wider text-(--status-info) mb-2">Recomendações</div>
                        <ul class="space-y-1.5">
                            @foreach($aiAnalysis['recomendacoes_conteudo'] as $item)
                                <li class="flex gap-2 text-sm text-(--text-secondary)">
                                    <iconify-icon icon="solar:lightbulb-linear" class="shrink-0 text-(--status-info) mt-0.5"></iconify-icon>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @elseif(!$aiLoading && !$aiError)
            <div class="py-6 text-center text-sm text-(--text-muted)">Clique em "Analisar com IA" para obter uma análise completa do conteúdo do site.</div>
        @endif
    </x-ds::card>

    {{-- SEÇÃO 7 — GEO (Generative Engine Optimization) --}}
    <x-ds::card title="SEÇÃO 7 — GEO · Generative Engine Optimization" description="Avalia se o site está otimizado para ser citado em respostas de IA (ChatGPT, Perplexity, Google AI Overviews, Claude).">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                @if(!empty($geoChecks))
                    @php
                        $geoPass = collect($geoChecks)->where('status', 'pass')->count();
                        $geoWarn = collect($geoChecks)->where('status', 'warn')->count();
                        $geoFail = collect($geoChecks)->where('status', 'fail')->count();
                        $geoVariant = $geoScore >= 70 ? 'success' : ($geoScore >= 40 ? 'warning' : 'danger');
                    @endphp
                    <div class="flex items-center gap-2">
                        <div class="text-2xl font-bold text-(--text-primary)">{{ $geoScore }}</div>
                        <x-ds::badge variant="{{ $geoVariant }}">{{ $geoScore >= 70 ? 'Bom para IA' : ($geoScore >= 40 ? 'Melhorias necessárias' : 'Pouco citável') }}</x-ds::badge>
                    </div>
                    <div class="flex gap-3 text-sm">
                        <span class="flex items-center gap-1.5 text-(--status-success)"><span class="h-2 w-2 rounded-full bg-(--status-success)"></span> {{ $geoPass }}</span>
                        <span class="flex items-center gap-1.5 text-(--status-warning)"><span class="h-2 w-2 rounded-full bg-(--status-warning)"></span> {{ $geoWarn }}</span>
                        <span class="flex items-center gap-1.5 text-(--status-danger)"><span class="h-2 w-2 rounded-full bg-(--status-danger)"></span> {{ $geoFail }}</span>
                    </div>
                @endif
            </div>

            <x-ds::button
                type="button"
                variant="secondary"
                icon="solar:magic-stick-3-linear"
                wire:click="checkGeo"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="checkGeo"
            >
                <span wire:loading.remove wire:target="checkGeo">{{ empty($geoChecks) ? 'Analisar GEO' : 'Re-analisar' }}</span>
                <span wire:loading wire:target="checkGeo">Analisando...</span>
            </x-ds::button>
        </div>

        @if($geoError)
            <x-ds::alert variant="danger" icon="solar:danger-circle-linear" class="mb-4">{{ $geoError }}</x-ds::alert>
        @endif

        @if(!empty($geoChecks))
            <div class="space-y-2">
                @foreach($geoChecks as $check)
                    @php
                        $statusIcon  = match($check['status']) { 'pass' => 'solar:check-circle-linear', 'warn' => 'solar:danger-triangle-linear', default => 'solar:close-circle-linear' };
                        $statusColor = match($check['status']) { 'pass' => 'text-(--status-success)', 'warn' => 'text-(--status-warning)', default => 'text-(--status-danger)' };
                    @endphp
                    <div x-data="{ open: false }" class="rounded-lg border border-(--border-subtle) bg-(--surface-card)">
                        <div class="flex items-start gap-3 px-4 py-3 cursor-pointer" @click="open = !open">
                            <iconify-icon icon="{{ $statusIcon }}" class="{{ $statusColor }} mt-0.5 shrink-0 text-lg"></iconify-icon>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium text-sm text-(--text-primary)">{{ $check['name'] }}</span>
                                </div>
                                <div class="mt-0.5 text-xs text-(--text-muted)">{{ $check['description'] }}</div>
                                <div class="mt-1 text-xs {{ $check['status'] === 'pass' ? 'text-(--text-secondary)' : 'text-(--status-warning)' }}">{{ $check['value'] }}</div>
                            </div>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-(--text-muted) shrink-0 transition-transform mt-1" :class="open ? 'rotate-180' : ''"></iconify-icon>
                        </div>
                        @if($check['status'] !== 'pass')
                            <div x-show="open" x-cloak class="border-t border-(--border-subtle) px-4 py-3">
                                <div class="flex gap-2 text-xs text-(--text-secondary)">
                                    <iconify-icon icon="solar:lightbulb-linear" class="shrink-0 text-(--status-info) mt-0.5"></iconify-icon>
                                    {{ $check['tip'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif(!$geoLoading && !$geoError)
            <div class="py-6 text-center text-sm text-(--text-muted)">Clique em "Analisar GEO" para verificar como o site se posiciona para sistemas de IA generativa.</div>
        @endif
    </x-ds::card>

    {{-- SEÇÃO 8 — Headers de Segurança --}}
    <x-ds::card title="SEÇÃO 8 — Headers de segurança" description="Verifica headers HTTP de segurança, exposição de informações e vulnerabilidades WordPress.">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                @if(!empty($securityChecks))
                    @php
                        $secPass = collect($securityChecks)->where('status', 'pass')->count();
                        $secWarn = collect($securityChecks)->where('status', 'warn')->count();
                        $secFail = collect($securityChecks)->where('status', 'fail')->count();
                        $secScoreVariant = $securityScore >= 80 ? 'success' : ($securityScore >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="flex items-center gap-2">
                        <div class="text-2xl font-bold text-(--text-primary)">{{ $securityScore }}</div>
                        <x-ds::badge variant="{{ $secScoreVariant }}">{{ $securityScore >= 80 ? 'Seguro' : ($securityScore >= 50 ? 'Melhorias necessárias' : 'Crítico') }}</x-ds::badge>
                    </div>
                    <div class="flex gap-3 text-sm">
                        <span class="flex items-center gap-1.5 text-(--status-success)"><span class="h-2 w-2 rounded-full bg-(--status-success)"></span> {{ $secPass }}</span>
                        <span class="flex items-center gap-1.5 text-(--status-warning)"><span class="h-2 w-2 rounded-full bg-(--status-warning)"></span> {{ $secWarn }}</span>
                        <span class="flex items-center gap-1.5 text-(--status-danger)"><span class="h-2 w-2 rounded-full bg-(--status-danger)"></span> {{ $secFail }}</span>
                    </div>
                @endif
            </div>

            <x-ds::button
                type="button"
                variant="secondary"
                icon="solar:shield-check-linear"
                wire:click="checkSecurity"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="checkSecurity"
            >
                <span wire:loading.remove wire:target="checkSecurity">{{ empty($securityChecks) ? 'Verificar segurança' : 'Re-verificar' }}</span>
                <span wire:loading wire:target="checkSecurity">Verificando...</span>
            </x-ds::button>
        </div>

        @if($securityError)
            <x-ds::alert variant="danger" icon="solar:danger-circle-linear" class="mb-4">{{ $securityError }}</x-ds::alert>
        @endif

        @if(!empty($securityChecks))
            <div class="space-y-2">
                @foreach($securityChecks as $check)
                    @php
                        $statusIcon   = match($check['status']) { 'pass' => 'solar:check-circle-linear', 'warn' => 'solar:danger-triangle-linear', default => 'solar:close-circle-linear' };
                        $statusColor  = match($check['status']) { 'pass' => 'text-(--status-success)', 'warn' => 'text-(--status-warning)', default => 'text-(--status-danger)' };
                        $severityVariant = match($check['severity']) { 'critical' => 'danger', 'high' => 'warning', 'medium' => 'secondary', default => 'secondary' };
                    @endphp
                    <div x-data="{ open: false }" class="rounded-lg border border-(--border-subtle) bg-(--surface-card)">
                        <div class="flex items-start gap-3 px-4 py-3 cursor-pointer" @click="open = !open">
                            <iconify-icon icon="{{ $statusIcon }}" class="{{ $statusColor }} mt-0.5 shrink-0 text-lg"></iconify-icon>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium text-sm text-(--text-primary)">{{ $check['name'] }}</span>
                                    <x-ds::badge variant="{{ $severityVariant }}">{{ $check['severity'] }}</x-ds::badge>
                                </div>
                                <div class="mt-0.5 text-xs text-(--text-muted)">{{ $check['description'] }}</div>
                                <div class="mt-1 font-mono text-xs {{ $check['status'] !== 'pass' ? 'text-(--status-warning)' : 'text-(--text-secondary)' }}">{{ $check['value'] }}</div>
                            </div>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-(--text-muted) shrink-0 transition-transform mt-1" :class="open ? 'rotate-180' : ''"></iconify-icon>
                        </div>
                        @if($check['status'] !== 'pass')
                            <div x-show="open" x-cloak class="border-t border-(--border-subtle) px-4 py-3">
                                <div class="flex gap-2 text-xs text-(--text-secondary)">
                                    <iconify-icon icon="solar:lightbulb-linear" class="shrink-0 text-(--status-info) mt-0.5"></iconify-icon>
                                    {{ $check['tip'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif(!$securityLoading && !$securityError)
            <div class="py-6 text-center text-sm text-(--text-muted)">Clique em "Verificar segurança" para analisar os headers HTTP.</div>
        @endif
    </x-ds::card>

    {{-- SEÇÃO 9 — Schema Markup & Open Graph --}}
    <x-ds::card title="SEÇÃO 9 — Schema Markup & Open Graph" description="Detecta e valida JSON-LD (Schema.org), Open Graph e Twitter Card na homepage.">
        <div class="flex items-center justify-between mb-4">
            <div class="flex flex-wrap gap-3 text-sm">
                @if(!empty($schemaItems))
                    @php
                        $schemaValid   = collect($schemaItems)->where('valid', true)->count();
                        $schemaInvalid = collect($schemaItems)->where('valid', false)->count();
                    @endphp
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-(--status-success)"></span> Válidos: {{ $schemaValid }}</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-(--status-warning)"></span> Com campos faltando: {{ $schemaInvalid }}</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-(--status-info)"></span> Open Graph: {{ count($ogTags) }} tags</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-(--status-info)"></span> Twitter Card: {{ count($twitterTags) }} tags</span>
                @endif
            </div>

            <x-ds::button
                type="button"
                variant="secondary"
                icon="solar:code-square-linear"
                wire:click="detectSchema"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="detectSchema"
            >
                <span wire:loading.remove wire:target="detectSchema">{{ empty($schemaItems) && empty($ogTags) ? 'Analisar Schema' : 'Re-analisar' }}</span>
                <span wire:loading wire:target="detectSchema">Analisando...</span>
            </x-ds::button>
        </div>

        @if($schemaError)
            <x-ds::alert variant="danger" icon="solar:danger-circle-linear" class="mb-4">{{ $schemaError }}</x-ds::alert>
        @endif

        @if(!empty($schemaItems))
            <div class="space-y-3 mb-6">
                <div class="text-xs font-semibold uppercase tracking-wider text-(--text-muted)">JSON-LD Schemas encontrados ({{ count($schemaItems) }})</div>
                @foreach($schemaItems as $schema)
                    <div x-data="{ expanded: false }" class="rounded-lg border {{ $schema['valid'] ? 'border-(--border-subtle)' : 'border-(--status-warning)' }} bg-(--surface-card)">
                        <div class="flex items-center justify-between px-4 py-3 cursor-pointer" @click="expanded = !expanded">
                            <div class="flex items-center gap-3">
                                <x-ds::badge variant="{{ $schema['valid'] ? 'success' : 'warning' }}">{{ $schema['type'] }}</x-ds::badge>
                                @if($schema['name'])
                                    <span class="text-sm text-(--text-secondary)">{{ Str::limit($schema['name'], 60) }}</span>
                                @endif
                                @if(!$schema['valid'])
                                    <span class="text-xs text-(--status-warning)">Campos faltando: {{ implode(', ', $schema['missing_fields']) }}</span>
                                @endif
                            </div>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="text-(--text-muted) transition-transform" :class="expanded ? 'rotate-180' : ''"></iconify-icon>
                        </div>
                        <div x-show="expanded" x-cloak class="border-t border-(--border-subtle) px-4 py-3">
                            <pre class="text-xs text-(--text-secondary) overflow-x-auto whitespace-pre-wrap break-all">{{ $schema['raw'] }}</pre>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif(!empty($ogTags) || !empty($twitterTags))
            <div class="mb-4 rounded-lg border border-(--status-warning) bg-(--surface-card) px-4 py-3">
                <x-ds::badge variant="warning">Nenhum JSON-LD encontrado</x-ds::badge>
                <span class="ml-2 text-sm text-(--text-secondary)">Considere adicionar Schema.org markup para melhorar a visibilidade nos buscadores.</span>
            </div>
        @endif

        {{-- Open Graph --}}
        @if(!empty($ogTags))
            <div class="mb-4">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">Open Graph Tags</div>
                <div class="grid grid-cols-1 gap-1 sm:grid-cols-2">
                    @foreach($ogTags as $key => $value)
                        <div class="flex gap-2 rounded-lg border border-(--border-subtle) bg-(--surface-card) px-3 py-2">
                            <span class="shrink-0 font-mono text-xs font-semibold text-(--text-secondary)">og:{{ $key }}</span>
                            <span class="truncate text-xs text-(--text-muted)" title="{{ $value }}">{{ Str::limit($value, 80) }}</span>
                        </div>
                    @endforeach
                </div>
                @php
                    $ogRequired = ['title', 'description', 'image', 'url', 'type'];
                    $ogMissing = array_diff($ogRequired, array_keys($ogTags));
                @endphp
                @if(!empty($ogMissing))
                    <div class="mt-2 text-xs text-(--status-warning)">Tags ausentes: og:{{ implode(', og:', $ogMissing) }}</div>
                @endif
            </div>
        @endif

        {{-- Twitter Card --}}
        @if(!empty($twitterTags))
            <div class="mb-4">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">Twitter Card Tags</div>
                <div class="grid grid-cols-1 gap-1 sm:grid-cols-2">
                    @foreach($twitterTags as $key => $value)
                        <div class="flex gap-2 rounded-lg border border-(--border-subtle) bg-(--surface-card) px-3 py-2">
                            <span class="shrink-0 font-mono text-xs font-semibold text-(--text-secondary)">twitter:{{ $key }}</span>
                            <span class="truncate text-xs text-(--text-muted)" title="{{ $value }}">{{ Str::limit($value, 80) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(empty($schemaItems) && empty($ogTags) && empty($twitterTags) && !$schemaLoading && !$schemaError)
            <div class="py-6 text-center text-sm text-(--text-muted)">Clique em "Analisar Schema" para detectar JSON-LD, Open Graph e Twitter Card.</div>
        @endif
    </x-ds::card>

    {{-- SEÇÃO 10 — Crawl de Links --}}
    <x-ds::card title="SEÇÃO 10 — Crawl de links" description="Verifica o status HTTP de todos os links encontrados nas páginas auditadas (máx. 150 links).">
        <div class="flex items-center justify-between mb-4">
            @php
                $linksOk       = collect($links)->where('type', 'ok')->count();
                $linksBroken   = collect($links)->where('type', 'broken')->count();
                $linksRedirect = collect($links)->where('type', 'redirect')->count();
                $linksError    = collect($links)->where('type', 'error')->count();
            @endphp

            <div class="flex flex-wrap gap-3 text-sm">
                @if(!empty($links))
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-(--status-success)"></span> OK: {{ $linksOk }}</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-(--status-danger)"></span> Quebrados: {{ $linksBroken }}</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-(--status-warning)"></span> Redirect: {{ $linksRedirect }}</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-(--border-subtle)"></span> Erro: {{ $linksError }}</span>
                @endif
            </div>

            <x-ds::button
                type="button"
                variant="secondary"
                icon="solar:link-minimalistic-2-linear"
                wire:click="crawlLinks"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="crawlLinks"
            >
                <span wire:loading.remove wire:target="crawlLinks">{{ empty($links) ? 'Verificar links' : 'Re-verificar' }}</span>
                <span wire:loading wire:target="crawlLinks">Verificando...</span>
            </x-ds::button>
        </div>

        @if($linksError)
            <x-ds::alert variant="danger" icon="solar:danger-circle-linear" class="mb-4">{{ $linksError }}</x-ds::alert>
        @endif

        @if($linksLoading)
            <div class="py-8 text-center"><x-ds::spinner label="Verificando links..." /></div>
        @elseif(!empty($links))
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                        <tr>
                            <th class="px-4 py-3">URL</th>
                            <th class="px-4 py-3 text-center">Status HTTP</th>
                            <th class="px-4 py-3 text-center">Tipo</th>
                            <th class="px-4 py-3 text-center">Interno</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($links as $link)
                            @php
                                $linkVariant = match($link['type']) {
                                    'ok'       => 'success',
                                    'broken'   => 'danger',
                                    'redirect' => 'warning',
                                    default    => 'secondary',
                                };
                                $rowHighlight = $link['type'] === 'broken' ? 'bg-(--status-danger-light)' : '';
                            @endphp
                            <tr class="border-b border-(--border-subtle) {{ $rowHighlight }}">
                                <td class="max-w-sm px-4 py-2.5">
                                    <a class="block truncate text-xs underline text-(--status-info)" href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $link['url'] }}">{{ $link['url'] }}</a>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <x-ds::badge variant="{{ $linkVariant }}">{{ $link['status'] ?? 'N/A' }}</x-ds::badge>
                                </td>
                                <td class="px-4 py-2.5 text-center text-xs text-(--text-secondary) capitalize">{{ $link['type'] }}</td>
                                <td class="px-4 py-2.5 text-center">
                                    <x-ds::badge variant="{{ $link['internal'] ? 'secondary' : 'secondary' }}">{{ $link['internal'] ? 'Interno' : 'Externo' }}</x-ds::badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-6 text-center text-sm text-(--text-muted)">Clique em "Verificar links" para iniciar o crawl.</div>
        @endif
    </x-ds::card>

    {{-- SEÇÃO 11 — Plugins (somente se disponível via API) --}}
    @if(is_array($general['plugins'] ?? null) && count($general['plugins']) > 0)
        <x-ds::card title="SEÇÃO 11 — Plugins instalados" description="Lista de plugins obtida via WordPress REST API.">
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
