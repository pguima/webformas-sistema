<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-xs text-(--text-secondary)">URL analisada</div>

            <div class="mt-1 text-base font-semibold text-(--text-primary)">
                @if($web?->url)
                    <a class="underline decoration-from-font text-(--status-info)" href="{{ str_starts_with($web->url, 'http') ? $web->url : 'https://' . $web->url }}" target="_blank" rel="noopener noreferrer">{{ $web->url }}</a>
                @else
                    {{ __('app.common.dash') }}
                @endif
            </div>

            <div class="mt-1 text-xs text-(--text-muted)">
                {{ $web?->name ?: __('app.common.dash') }}
            </div>
        </div>

        <div class="flex items-center gap-2">
            <x-ds::button
                type="button"
                variant="secondary"
                icon="solar:refresh-linear"
                wire:click="analyzePageSpeed"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="analyzePageSpeed"
            >
                <span wire:loading.remove wire:target="analyzePageSpeed">Atualizar PageSpeed</span>
                <span wire:loading wire:target="analyzePageSpeed">Atualizando...</span>
            </x-ds::button>
        </div>
    </div>

    @if($loading)
        <x-ds::card>
            <div class="py-8">
                <x-ds::spinner label="Carregando..." />
            </div>
        </x-ds::card>
    @endif

    @if($errorMessage)
        <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
            {{ $errorMessage }}
        </x-ds::alert>
    @endif

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

        $sec = function ($ms) {
            if ($ms === null) return __('app.common.dash');
            return round(((float) $ms) / 1000, 2) . 's';
        };

        $msLabel = function ($ms) {
            if ($ms === null) return __('app.common.dash');
            return (string) round((float) $ms) . 'ms';
        };
    @endphp

    <x-ds::card title="Resumo" description="Indicadores principais do Lighthouse (Mobile e Desktop).">
        <div class="text-xs text-(--text-secondary)">
            Última análise:
            <span class="font-medium text-(--text-primary)">{{ $pageSpeedLastRunAt ?: __('app.common.dash') }}</span>
        </div>

        @if(!$pageSpeed)
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                    <div class="text-xs font-medium text-(--text-muted)">Performance</div>
                    <div class="mt-1">
                        <x-ds::badge :variant="$scoreVariantInt($web?->performance)">{{ $scoreLabelInt($web?->performance) }}</x-ds::badge>
                    </div>
                </div>
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                    <div class="text-xs font-medium text-(--text-muted)">SEO</div>
                    <div class="mt-1">
                        <x-ds::badge :variant="$scoreVariantInt($web?->seo)">{{ $scoreLabelInt($web?->seo) }}</x-ds::badge>
                    </div>
                </div>
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                    <div class="text-xs font-medium text-(--text-muted)">Accessibility</div>
                    <div class="mt-1">
                        <x-ds::badge :variant="$scoreVariantInt($web?->accessibility)">{{ $scoreLabelInt($web?->accessibility) }}</x-ds::badge>
                    </div>
                </div>
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                    <div class="text-xs font-medium text-(--text-muted)">Best practices</div>
                    <div class="mt-1">
                        <x-ds::badge :variant="$scoreVariantInt($web?->best_practices)">{{ $scoreLabelInt($web?->best_practices) }}</x-ds::badge>
                    </div>
                </div>
            </div>

            <div class="mt-3 text-sm text-(--text-secondary)">
                Clique em <span class="font-medium">Atualizar PageSpeed</span> para ver o detalhamento de <span class="font-medium">Mobile</span> e <span class="font-medium">Desktop</span>.
            </div>
        @else
            @php
                $strategies = [
                    'mobile' => 'Mobile',
                    'desktop' => 'Desktop',
                ];
            @endphp

            <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
                @foreach($strategies as $strategyKey => $strategyLabel)
                    @php
                        $strategyData = $pageSpeed[$strategyKey] ?? null;
                        $strategyError = $pageSpeedErrors[$strategyKey] ?? null;
                    @endphp

                    <x-ds::card :title="$strategyLabel" :description="'Resultados para ' . $strategyLabel . '.'">
                        @if($strategyError)
                            <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                                {{ $strategyError }}
                            </x-ds::alert>
                        @endif

                        @if($strategyData)
                            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">Performance</div>
                                    <div class="mt-1">
                                        <x-ds::badge variant="{{ $scoreVariant($strategyData['scores']['performance'] ?? null) }}">{{ $scoreLabel($strategyData['scores']['performance'] ?? null) }}</x-ds::badge>
                                    </div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">SEO</div>
                                    <div class="mt-1">
                                        <x-ds::badge variant="{{ $scoreVariant($strategyData['scores']['seo'] ?? null) }}">{{ $scoreLabel($strategyData['scores']['seo'] ?? null) }}</x-ds::badge>
                                    </div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">Accessibility</div>
                                    <div class="mt-1">
                                        <x-ds::badge variant="{{ $scoreVariant($strategyData['scores']['accessibility'] ?? null) }}">{{ $scoreLabel($strategyData['scores']['accessibility'] ?? null) }}</x-ds::badge>
                                    </div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">Best practices</div>
                                    <div class="mt-1">
                                        <x-ds::badge variant="{{ $scoreVariant($strategyData['scores']['best_practices'] ?? null) }}">{{ $scoreLabel($strategyData['scores']['best_practices'] ?? null) }}</x-ds::badge>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">FCP</div>
                                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">{{ $strategyData['display']['fcp'] ?? $sec($strategyData['metrics']['fcp_ms'] ?? null) }}</div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">LCP</div>
                                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">{{ $strategyData['display']['lcp'] ?? $sec($strategyData['metrics']['lcp_ms'] ?? null) }}</div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">TBT</div>
                                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">{{ $strategyData['display']['tbt'] ?? $msLabel($strategyData['metrics']['tbt_ms'] ?? null) }}</div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">CLS</div>
                                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">{{ $strategyData['display']['cls'] ?? ($strategyData['metrics']['cls'] ?? __('app.common.dash')) }}</div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">TTFB</div>
                                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">{{ $strategyData['display']['ttfb'] ?? $sec($strategyData['metrics']['ttfb_ms'] ?? null) }}</div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="text-xs font-medium text-(--text-muted)">Speed Index</div>
                                    <div class="mt-1 text-sm font-semibold text-(--text-primary)">{{ $strategyData['display']['speed_index'] ?? $sec($strategyData['metrics']['speed_index_ms'] ?? null) }}</div>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 text-sm text-(--text-secondary)">
                                {{ __('app.common.dash') }}
                            </div>
                        @endif
                    </x-ds::card>
                @endforeach
            </div>
        @endif

        <div class="mt-3 text-xs text-(--text-muted)">
            @if(config('services.pagespeed.key'))
                Usando API Key configurada.
            @else
                Sem API Key configurada (pode funcionar com quota limitada).
            @endif
        </div>
    </x-ds::card>
</div>
