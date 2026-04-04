<div class="space-y-6">

    {{-- Header --}}
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
            <div class="mt-1 text-xs text-(--text-muted)">{{ $web?->name ?: __('app.common.dash') }}</div>
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
                <span wire:loading.remove wire:target="analyzePageSpeed">Analisar PageSpeed</span>
                <span wire:loading wire:target="analyzePageSpeed">Analisando...</span>
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
        // Helpers de score
        $scoreVariantInt = function ($score): string {
            if ($score === null || $score === '') return 'secondary';
            $pct = (int) $score;
            if ($pct >= 90) return 'success';
            if ($pct >= 50) return 'warning';
            return 'danger';
        };

        $scoreVariant = function ($score): string {
            if ($score === null) return 'secondary';
            $pct = (float) $score * 100.0;
            if ($pct >= 90) return 'success';
            if ($pct >= 50) return 'warning';
            return 'danger';
        };

        $scoreLabel = function ($score): string {
            if ($score === null) return __('app.common.dash');
            return (string) round(((float) $score) * 100);
        };

        $scoreLabelInt = function ($score): string {
            if ($score === null || $score === '') return __('app.common.dash');
            return (string) (int) $score;
        };

        // Core Web Vitals — thresholds Google
        $cwvVariant = function (string $metric, ?float $value): string {
            if ($value === null) return 'secondary';
            return match ($metric) {
                'fcp'         => $value <= 1800  ? 'success' : ($value <= 3000  ? 'warning' : 'danger'),
                'lcp'         => $value <= 2500  ? 'success' : ($value <= 4000  ? 'warning' : 'danger'),
                'tbt'         => $value <= 200   ? 'success' : ($value <= 600   ? 'warning' : 'danger'),
                'ttfb'        => $value <= 800   ? 'success' : ($value <= 1800  ? 'warning' : 'danger'),
                'speed_index' => $value <= 3400  ? 'success' : ($value <= 5800  ? 'warning' : 'danger'),
                'inp'         => $value <= 200   ? 'success' : ($value <= 500   ? 'warning' : 'danger'),
                'cls'         => $value <= 0.1   ? 'success' : ($value <= 0.25  ? 'warning' : 'danger'),
                default       => 'secondary',
            };
        };

        $sec  = fn($ms) => $ms === null ? __('app.common.dash') : round((float) $ms / 1000, 2) . 's';
        $ms   = fn($v)  => $v  === null ? __('app.common.dash') : (string) round((float) $v) . ' ms';
        $cls  = fn($v)  => $v  === null ? __('app.common.dash') : number_format((float) $v, 3);

        // Cálculo do score geral (desktop se disponível, senão mobile, senão dados salvos)
        $overallScoreVal = null;
        if ($pageSpeed) {
            $ds = $pageSpeed['desktop']['scores'] ?? $pageSpeed['mobile']['scores'] ?? null;
            if ($ds) {
                $vals = array_filter(array_map(fn($v) => is_numeric($v) ? (int) round((float) $v * 100) : null, $ds), fn($v) => $v !== null);
                $overallScoreVal = empty($vals) ? null : (int) round(array_sum($vals) / count($vals));
            }
        } elseif ($web) {
            $stored = array_filter([$web->performance, $web->seo, $web->accessibility, $web->best_practices], fn($v) => $v !== null);
            $overallScoreVal = empty($stored) ? null : (int) round(array_sum($stored) / count($stored));
        }
        $overallVariant = $overallScoreVal === null ? 'secondary' : ($overallScoreVal >= 90 ? 'success' : ($overallScoreVal >= 50 ? 'warning' : 'danger'));
        $overallLabel   = match($overallVariant) {
            'success'   => 'Excelente',
            'warning'   => 'Precisa de atenção',
            'danger'    => 'Crítico',
            default     => 'Sem dados',
        };

        // Delta Mobile vs Desktop
        $delta = function (?float $mob, ?float $desk): ?int {
            if ($mob === null || $desk === null) return null;
            return (int) round(($desk - $mob) * 100);
        };
        $deltaLabel = fn(?int $d) => $d === null ? '—' : ($d > 0 ? '+' . $d : (string) $d);
        $deltaVariant = fn(?int $d) => $d === null ? 'secondary' : ($d >= 0 ? 'success' : 'danger');

        // Savings legíveis
        $savingsLabel = function (?int $ms): string {
            if ($ms === null) return '';
            if ($ms >= 1000) return round($ms / 1000, 1) . ' s';
            return $ms . ' ms';
        };
    @endphp

    {{-- Status Geral --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-5">
            <div class="text-xs font-medium text-(--text-muted)">Status geral</div>
            <div class="mt-2 flex items-center gap-3">
                @if($overallScoreVal !== null)
                    <div class="text-3xl font-bold text-(--text-primary)">{{ $overallScoreVal }}</div>
                @endif
                <x-ds::badge variant="{{ $overallVariant }}" class="text-sm">{{ $overallLabel }}</x-ds::badge>
            </div>
            <div class="mt-1 text-xs text-(--text-muted)">Média das 4 categorias (Desktop)</div>
        </div>

        <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-5">
            <div class="text-xs font-medium text-(--text-muted)">Última análise</div>
            <div class="mt-2 text-base font-semibold text-(--text-primary)">{{ $pageSpeedLastRunAt ?: __('app.common.dash') }}</div>
            <div class="mt-1 text-xs text-(--text-muted)">
                @if(config('services.pagespeed.key'))
                    API Key configurada
                @else
                    Sem API Key (quota limitada)
                @endif
            </div>
        </div>

        <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-5">
            <div class="text-xs font-medium text-(--text-muted)">Histórico de análises</div>
            <div class="mt-2 text-3xl font-bold text-(--text-primary)">{{ count($history) }}</div>
            <div class="mt-1 text-xs text-(--text-muted)">registros salvos (últimos 10)</div>
        </div>
    </div>

    {{-- Gráfico de evolução histórica --}}
    @if(count($chartHistory) >= 2)
        <x-ds::card title="Evolução histórica" description="Performance e SEO ao longo das últimas análises.">
            @php
                $chartLabels = array_map(fn($r) => \Carbon\Carbon::parse($r['analyzed_at'])->format('d/m H:i'), $chartHistory);
                $chartSeries = [
                    ['name' => 'Perf Desktop',  'data' => array_column($chartHistory, 'performance_desktop')],
                    ['name' => 'Perf Mobile',   'data' => array_column($chartHistory, 'performance_mobile')],
                    ['name' => 'SEO Desktop',   'data' => array_column($chartHistory, 'seo_desktop')],
                    ['name' => 'SEO Mobile',    'data' => array_column($chartHistory, 'seo_mobile')],
                    ['name' => 'A11y Desktop',  'data' => array_column($chartHistory, 'accessibility_desktop')],
                    ['name' => 'BP Desktop',    'data' => array_column($chartHistory, 'best_practices_desktop')],
                ];
            @endphp

            <div
                x-data="{
                    chart: null,
                    series: {{ Js::from($chartSeries) }},
                    labels: {{ Js::from($chartLabels) }},
                    isDark: document.documentElement.classList.contains('dark'),
                    colors: ['#3b82f6','#93c5fd','#10b981','#6ee7b7','#f59e0b','#8b5cf6'],
                    init() {
                        this.renderChart();
                        this.$watch('series', () => {
                            this.chart?.destroy();
                            this.renderChart();
                        });
                        window.addEventListener('livewire:morph', () => {
                            this.isDark = document.documentElement.classList.contains('dark');
                            this.chart?.destroy();
                            this.renderChart();
                        });
                    },
                    renderChart() {
                        const options = {
                            chart: {
                                type: 'line',
                                height: 280,
                                toolbar: { show: false },
                                zoom: { enabled: false },
                                background: 'transparent',
                                animations: { enabled: true, speed: 400 },
                            },
                            theme: { mode: this.isDark ? 'dark' : 'light' },
                            series: this.series,
                            xaxis: {
                                categories: this.labels,
                                labels: { style: { fontSize: '11px' } },
                                tickPlacement: 'on',
                            },
                            yaxis: {
                                min: 0,
                                max: 100,
                                tickAmount: 5,
                                labels: { style: { fontSize: '11px' }, formatter: (v) => v + '' },
                            },
                            colors: this.colors,
                            stroke: { curve: 'smooth', width: 2 },
                            markers: { size: this.series[0].data.length <= 10 ? 4 : 0 },
                            legend: { position: 'top', fontSize: '12px', horizontalAlign: 'left' },
                            grid: {
                                borderColor: this.isDark ? '#374151' : '#e5e7eb',
                                strokeDashArray: 4,
                            },
                            tooltip: {
                                shared: true,
                                intersect: false,
                                y: { formatter: (v) => (v ?? '—') + (v !== null ? '' : '') },
                            },
                            annotations: {
                                yaxis: [
                                    { y: 90, borderColor: '#10b981', strokeDashArray: 4, label: { text: 'Bom (90)', style: { fontSize: '10px', color: '#10b981' } } },
                                    { y: 50, borderColor: '#f59e0b', strokeDashArray: 4, label: { text: 'Atenção (50)', style: { fontSize: '10px', color: '#f59e0b' } } },
                                ],
                            },
                        };
                        this.chart = new ApexCharts(this.$refs.chartEl, options);
                        this.chart.render();
                    }
                }"
            >
                <div x-ref="chartEl" class="w-full"></div>
            </div>
        </x-ds::card>
    @elseif(count($chartHistory) === 1)
        <x-ds::card>
            <div class="py-4 text-center text-sm text-(--text-muted)">Execute mais uma análise para ver o gráfico de evolução.</div>
        </x-ds::card>
    @endif

    {{-- Comparativo Mobile vs Desktop (somente quando há resultado fresco) --}}
    @if($pageSpeed)
        <x-ds::card title="Comparativo Mobile vs Desktop" description="Diferença de pontuação entre os dois dispositivos.">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                        <tr>
                            <th class="px-4 py-3">Categoria</th>
                            <th class="px-4 py-3 text-center">Mobile</th>
                            <th class="px-4 py-3 text-center">Desktop</th>
                            <th class="px-4 py-3 text-center">Δ (Desktop − Mobile)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $compareRows = [
                                ['key' => 'performance',   'label' => 'Performance'],
                                ['key' => 'seo',           'label' => 'SEO'],
                                ['key' => 'accessibility', 'label' => 'Accessibility'],
                                ['key' => 'best_practices','label' => 'Best Practices'],
                            ];
                        @endphp
                        @foreach($compareRows as $row)
                            @php
                                $mob  = $pageSpeed['mobile']['scores'][$row['key']] ?? null;
                                $desk = $pageSpeed['desktop']['scores'][$row['key']] ?? null;
                                $d    = $delta($mob, $desk);
                            @endphp
                            <tr class="border-b border-(--border-subtle)">
                                <td class="px-4 py-3 font-medium text-(--text-primary)">{{ $row['label'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <x-ds::badge variant="{{ $scoreVariant($mob) }}">{{ $scoreLabel($mob) }}</x-ds::badge>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-ds::badge variant="{{ $scoreVariant($desk) }}">{{ $scoreLabel($desk) }}</x-ds::badge>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-ds::badge variant="{{ $deltaVariant($d) }}">{{ $deltaLabel($d) }}</x-ds::badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ds::card>
    @endif

    {{-- Scores salvos (quando não há resultado fresco) --}}
    @if(!$pageSpeed && $web)
        <x-ds::card title="Últimos scores salvos" description="Resultados Desktop da última análise executada. Clique em Analisar PageSpeed para dados atualizados.">
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                @foreach(['performance' => 'Performance', 'seo' => 'SEO', 'accessibility' => 'Accessibility', 'best_practices' => 'Best Practices'] as $field => $catLabel)
                    @php
                        $val = $web->$field;
                        $savedColor = $val === null ? 'text-(--text-muted)' : ($val >= 90 ? 'text-(--status-success)' : ($val >= 50 ? 'text-(--status-warning)' : 'text-(--status-danger)'));
                        $savedBar   = $val === null ? 'bg-(--border-subtle)' : ($val >= 90 ? 'bg-(--status-success)' : ($val >= 50 ? 'bg-(--status-warning)' : 'bg-(--status-danger)'));
                    @endphp
                    <div class="overflow-hidden rounded-lg border border-(--border-subtle) bg-(--surface-card)">
                        <div class="h-1 {{ $savedBar }}"></div>
                        <div class="p-4 text-center">
                            <div class="text-3xl font-bold leading-none {{ $savedColor }}">{{ $val ?? '—' }}</div>
                            <div class="mt-1.5 text-xs text-(--text-muted)">{{ $catLabel }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ds::card>
    @endif

    {{-- Análise detalhada por estratégia --}}
    @if($pageSpeed)
        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
            @foreach(['mobile' => 'Mobile', 'desktop' => 'Desktop'] as $strategyKey => $strategyLabel)
                @php
                    $sd    = $pageSpeed[$strategyKey] ?? null;
                    $serr  = $pageSpeedErrors[$strategyKey] ?? null;
                    $opps  = $sd['opportunities'] ?? [];
                @endphp

                <x-ds::card :title="$strategyLabel">
                    @if($serr)
                        <x-ds::alert variant="danger" icon="solar:danger-circle-linear">{{ $serr }}</x-ds::alert>
                    @endif

                    @if($sd)
                        @php
                            $scoreColor = function ($score): string {
                                if ($score === null) return 'text-(--text-muted)';
                                $p = (float) $score * 100;
                                return $p >= 90 ? 'text-(--status-success)' : ($p >= 50 ? 'text-(--status-warning)' : 'text-(--status-danger)');
                            };
                            $scoreBar = function ($score): string {
                                if ($score === null) return 'bg-(--border-subtle)';
                                $p = (float) $score * 100;
                                return $p >= 90 ? 'bg-(--status-success)' : ($p >= 50 ? 'bg-(--status-warning)' : 'bg-(--status-danger)');
                            };
                            $cwvColor = function (string $metric, ?float $value) use ($cwvVariant): string {
                                return match($cwvVariant($metric, $value)) {
                                    'success' => 'text-(--status-success)',
                                    'warning' => 'text-(--status-warning)',
                                    'danger'  => 'text-(--status-danger)',
                                    default   => 'text-(--text-muted)',
                                };
                            };
                            $cwvBar = function (string $metric, ?float $value) use ($cwvVariant): string {
                                return match($cwvVariant($metric, $value)) {
                                    'success' => 'bg-(--status-success)',
                                    'warning' => 'bg-(--status-warning)',
                                    'danger'  => 'bg-(--status-danger)',
                                    default   => 'bg-(--border-subtle)',
                                };
                            };
                        @endphp

                        {{-- Scores: número grande com barra de cor superior --}}
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['performance' => 'Performance', 'seo' => 'SEO', 'accessibility' => 'Accessibility', 'best_practices' => 'Best Practices'] as $scoreKey => $catLabel)
                                @php
                                    $sv  = $sd['scores'][$scoreKey] ?? null;
                                    $pct = $sv !== null ? (int) round((float) $sv * 100) : null;
                                @endphp
                                <div class="overflow-hidden rounded-lg border border-(--border-subtle) bg-(--surface-card)">
                                    <div class="h-1 {{ $scoreBar($sv) }}"></div>
                                    <div class="p-3 text-center">
                                        <div class="text-2xl font-bold leading-none {{ $scoreColor($sv) }}">{{ $pct ?? '—' }}</div>
                                        <div class="mt-1 text-xs text-(--text-muted)">{{ $catLabel }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Core Web Vitals: vertical, barra de cor, nome completo como tooltip --}}
                        <div class="mt-5">
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">Core Web Vitals</div>
                            @php
                                $cwvItems = [
                                    ['label' => 'FCP',         'full' => 'First Contentful Paint',   'metric' => 'fcp',         'raw' => $sd['metrics']['fcp_ms'],         'display' => $sd['display']['fcp'],         'fmt' => 'sec'],
                                    ['label' => 'LCP',         'full' => 'Largest Contentful Paint',  'metric' => 'lcp',         'raw' => $sd['metrics']['lcp_ms'],         'display' => $sd['display']['lcp'],         'fmt' => 'sec'],
                                    ['label' => 'TBT',         'full' => 'Total Blocking Time',       'metric' => 'tbt',         'raw' => $sd['metrics']['tbt_ms'],         'display' => $sd['display']['tbt'],         'fmt' => 'ms'],
                                    ['label' => 'CLS',         'full' => 'Cumulative Layout Shift',   'metric' => 'cls',         'raw' => $sd['metrics']['cls'],            'display' => $sd['display']['cls'],         'fmt' => 'cls'],
                                    ['label' => 'TTFB',        'full' => 'Time to First Byte',        'metric' => 'ttfb',        'raw' => $sd['metrics']['ttfb_ms'],        'display' => $sd['display']['ttfb'],        'fmt' => 'ms'],
                                    ['label' => 'Speed Index', 'full' => 'Speed Index',               'metric' => 'speed_index', 'raw' => $sd['metrics']['speed_index_ms'], 'display' => $sd['display']['speed_index'], 'fmt' => 'sec'],
                                    ['label' => 'INP',         'full' => 'Interaction to Next Paint', 'metric' => 'inp',         'raw' => $sd['metrics']['inp_ms'],         'display' => $sd['display']['inp'],         'fmt' => 'ms'],
                                ];
                            @endphp
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($cwvItems as $cwv)
                                    @php
                                        $rawVal  = $cwv['raw'];
                                        $dispVal = $cwv['display'] ?? match($cwv['fmt']) {
                                            'sec' => $sec($rawVal),
                                            'ms'  => $ms($rawVal),
                                            'cls' => $cls($rawVal),
                                            default => '—',
                                        };
                                    @endphp
                                    <div class="overflow-hidden rounded-lg border border-(--border-subtle) bg-(--surface-card)" title="{{ $cwv['full'] }}">
                                        <div class="h-0.5 {{ $cwvBar($cwv['metric'], $rawVal) }}"></div>
                                        <div class="p-2.5">
                                            <div class="text-xs text-(--text-muted)">{{ $cwv['label'] }}</div>
                                            <div class="mt-1 text-sm font-semibold {{ $cwvColor($cwv['metric'], $rawVal) }}">{{ $dispVal }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Oportunidades --}}
                        <div class="mt-5">
                            <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">
                                Oportunidades de melhoria
                                @if(!empty($opps))
                                    <span class="ml-1 font-normal normal-case text-(--text-muted)">({{ count($opps) }})</span>
                                @endif
                            </div>
                            @if(!empty($opps))
                                <div class="space-y-1.5">
                                    @foreach(array_slice($opps, 0, 5) as $opp)
                                        @php
                                            $saving = $opp['savings_ms'] > 0 ? $savingsLabel($opp['savings_ms']) : ($opp['display_value'] ?? null);
                                        @endphp
                                        <div class="flex items-center gap-2.5 rounded-lg border border-(--border-subtle) bg-(--surface-card) px-3 py-2">
                                            <div class="h-1.5 w-1.5 shrink-0 rounded-full bg-(--status-warning)"></div>
                                            <div class="flex-1 text-xs text-(--text-secondary)">{{ $opp['title'] }}</div>
                                            @if($saving)
                                                <div class="shrink-0 text-xs font-semibold text-(--status-warning)">{{ $saving }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if(count($opps) > 5)
                                        <div class="pl-1 text-xs text-(--text-muted)">+ {{ count($opps) - 5 }} outras</div>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-xs text-(--status-success)">
                                    <div class="h-1.5 w-1.5 rounded-full bg-(--status-success)"></div>
                                    Nenhuma oportunidade crítica identificada.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="py-4 text-sm text-(--text-secondary)">{{ __('app.common.dash') }}</div>
                    @endif
                </x-ds::card>
            @endforeach
        </div>
    @endif

    {{-- Histórico de análises --}}
    @if(!empty($history))
        <x-ds::card title="Histórico de análises" description="Últimas 10 análises executadas para este site.">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                        <tr>
                            <th class="px-4 py-3">Data</th>
                            <th class="px-4 py-3 text-center">Perf M</th>
                            <th class="px-4 py-3 text-center">Perf D</th>
                            <th class="px-4 py-3 text-center">SEO M</th>
                            <th class="px-4 py-3 text-center">SEO D</th>
                            <th class="px-4 py-3 text-center">A11y M</th>
                            <th class="px-4 py-3 text-center">A11y D</th>
                            <th class="px-4 py-3 text-center">BP M</th>
                            <th class="px-4 py-3 text-center">BP D</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $record)
                            @php
                                $date = \Carbon\Carbon::parse($record['analyzed_at'])->format('d/m/Y H:i');
                            @endphp
                            <tr class="border-b border-(--border-subtle)">
                                <td class="px-4 py-3 text-xs text-(--text-secondary)">{{ $date }}</td>
                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariantInt($record['performance_mobile']) }}">{{ $record['performance_mobile'] ?? '—' }}</x-ds::badge></td>
                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariantInt($record['performance_desktop']) }}">{{ $record['performance_desktop'] ?? '—' }}</x-ds::badge></td>
                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariantInt($record['seo_mobile']) }}">{{ $record['seo_mobile'] ?? '—' }}</x-ds::badge></td>
                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariantInt($record['seo_desktop']) }}">{{ $record['seo_desktop'] ?? '—' }}</x-ds::badge></td>
                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariantInt($record['accessibility_mobile']) }}">{{ $record['accessibility_mobile'] ?? '—' }}</x-ds::badge></td>
                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariantInt($record['accessibility_desktop']) }}">{{ $record['accessibility_desktop'] ?? '—' }}</x-ds::badge></td>
                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariantInt($record['best_practices_mobile']) }}">{{ $record['best_practices_mobile'] ?? '—' }}</x-ds::badge></td>
                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariantInt($record['best_practices_desktop']) }}">{{ $record['best_practices_desktop'] ?? '—' }}</x-ds::badge></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ds::card>
    @endif

</div>
