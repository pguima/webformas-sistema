<div
    class="space-y-6"
    x-on:analysis-continue.window="$wire.runNextModule()"
>
    @php
        /* ── Score helpers ── */
        $scoreVariant = function ($score): string {
            if ($score === null) return 'secondary';
            $pct = (float) $score * 100.0;
            return $pct >= 90 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
        };
        $scoreLabel = function ($score): string {
            if ($score === null) return '—';
            return (string) round(((float) $score) * 100);
        };
        $scoreVariantInt = function ($score): string {
            if ($score === null || $score === '') return 'secondary';
            $pct = (int) $score;
            return $pct >= 90 ? 'success' : ($pct >= 50 ? 'warning' : 'danger');
        };
        $scoreLabelInt = function ($score): string {
            if ($score === null || $score === '') return '—';
            return (string) (int) $score;
        };
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
        /* ── CWV helpers ── */
        $cwvVariant = function (string $metric, ?float $value): string {
            if ($value === null) return 'secondary';
            return match ($metric) {
                'fcp'         => $value <= 1800 ? 'success' : ($value <= 3000 ? 'warning' : 'danger'),
                'lcp'         => $value <= 2500 ? 'success' : ($value <= 4000 ? 'warning' : 'danger'),
                'tbt'         => $value <= 200  ? 'success' : ($value <= 600  ? 'warning' : 'danger'),
                'ttfb'        => $value <= 800  ? 'success' : ($value <= 1800 ? 'warning' : 'danger'),
                'speed_index' => $value <= 3400 ? 'success' : ($value <= 5800 ? 'warning' : 'danger'),
                'inp'         => $value <= 200  ? 'success' : ($value <= 500  ? 'warning' : 'danger'),
                'cls'         => $value <= 0.1  ? 'success' : ($value <= 0.25 ? 'warning' : 'danger'),
                default       => 'secondary',
            };
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
        $sec = fn($ms) => $ms === null ? '—' : round((float) $ms / 1000, 2) . 's';
        $ms  = fn($v)  => $v  === null ? '—' : (string) round((float) $v) . ' ms';
        $cls = fn($v)  => $v  === null ? '—' : number_format((float) $v, 3);
        $delta = function (?float $mob, ?float $desk): ?int {
            if ($mob === null || $desk === null) return null;
            return (int) round(($desk - $mob) * 100);
        };
        $deltaLabel   = fn(?int $d) => $d === null ? '—' : ($d > 0 ? '+' . $d : (string) $d);
        $deltaVariant = fn(?int $d) => $d === null ? 'secondary' : ($d >= 0 ? 'success' : 'danger');
        /* ── Misc ── */
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
        $defs = $this->moduleDefs();
    @endphp

    {{-- ── Header ── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="text-xs text-(--text-muted)">Análise do site</div>
            <div class="mt-1 text-base font-semibold text-(--text-primary)">{{ $web?->name ?: '—' }}</div>
            @if($web?->url)
                <div class="mt-0.5 text-xs">
                    <a class="underline decoration-from-font text-(--status-info)" href="{{ str_starts_with($web->url, 'http') ? $web->url : 'https://' . $web->url }}" target="_blank" rel="noopener noreferrer">{{ $web->url }}</a>
                </div>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($platformDetected)
                @if($isWordPress)
                    <x-ds::badge variant="success">WordPress</x-ds::badge>
                @else
                    <x-ds::badge variant="secondary">HTML / Outro</x-ds::badge>
                @endif
            @else
                <x-ds::badge variant="secondary">Detectando plataforma...</x-ds::badge>
            @endif
            @if($pageSpeedLastRunAt)
                <span class="text-xs text-(--text-muted)">PageSpeed: {{ $pageSpeedLastRunAt }}</span>
            @endif
        </div>
    </div>

    @if($errorMessage)
        <x-ds::alert variant="danger" icon="solar:danger-circle-linear">{{ $errorMessage }}</x-ds::alert>
    @endif

    {{-- ── Module Selector ── --}}
    <x-ds::card title="Módulos de análise" description="Escolha quais análises executar. Módulos WordPress ficam disponíveis apenas em sites WP detectados.">
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5">
            @foreach($defs as $key => $def)
                @php
                    $wpOnly   = $def['wp_only'] ?? false;
                    $disabled = $wpOnly && !$isWordPress;
                    $checked  = $selectedModules[$key] ?? false;
                    $status   = $moduleStatus[$key] ?? null;
                    $cardCls  = $disabled
                        ? 'opacity-40 cursor-not-allowed border-(--border-subtle)'
                        : ($checked ? 'border-(--brand-primary) bg-(--brand-subtle) cursor-pointer' : 'border-(--border-subtle) cursor-pointer hover:border-(--border-default)');
                @endphp
                <label class="flex items-start gap-2 rounded-lg border p-3 transition-colors {{ $cardCls }}">
                    <input
                        type="checkbox"
                        wire:model.live="selectedModules.{{ $key }}"
                        class="mt-0.5 shrink-0 accent-(--brand-primary)"
                        {{ $disabled ? 'disabled' : '' }}
                    >
                    <div class="min-w-0">
                        <div class="text-xs font-medium text-(--text-primary) leading-tight">{{ $def['label'] }}</div>
                        @if($wpOnly)
                            <div class="mt-0.5 text-[10px] text-(--text-muted)">WordPress</div>
                        @endif
                        @if($status === 'running')
                            <div class="mt-1"><x-ds::spinner size="sm" /></div>
                        @elseif($status === 'done')
                            <div class="mt-1 text-[10px] text-(--status-success) font-medium">✓ Concluído</div>
                        @elseif($status === 'error')
                            <div class="mt-1 text-[10px] text-(--status-danger) font-medium">✗ Erro</div>
                        @endif
                    </div>
                </label>
            @endforeach
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-ds::button
                type="button"
                icon="solar:play-circle-linear"
                wire:click="startAnalysis"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-wait"
                wire:target="startAnalysis,runNextModule,runModule"
            >
                <span wire:loading.remove wire:target="startAnalysis,runNextModule,runModule">Iniciar análise</span>
                <span wire:loading wire:target="startAnalysis,runNextModule,runModule">Analisando...</span>
            </x-ds::button>
            <x-ds::button type="button" variant="secondary" wire:click="selectAll">Todos</x-ds::button>
            <x-ds::button type="button" variant="ghost" wire:click="selectNone">Nenhum</x-ds::button>

            @if(!empty($moduleQueue))
                <span class="flex items-center gap-1 rounded-full bg-(--surface-hover) px-3 py-1 text-xs text-(--text-muted)">
                    <x-ds::spinner size="sm" />
                    {{ count($moduleQueue) }} na fila
                </span>
                <x-ds::button
                    type="button"
                    variant="danger"
                    icon="solar:stop-circle-linear"
                    wire:click="cancelAnalysis"
                >Interromper</x-ds::button>
            @endif
        </div>
    </x-ds::card>

    {{-- ════════════════════════════════════════════════════════════════════════
         RESULTADOS — um card por módulo, colapsável
    ════════════════════════════════════════════════════════════════════════ --}}

    {{-- ── PageSpeed ── --}}
    @if(isset($moduleStatus['pagespeed']))
        @php
            $psStatus = $moduleStatus['pagespeed'];
            $psOpen   = $moduleExpanded['pagespeed'] ?? true;
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
            $overallLabel   = match($overallVariant) { 'success' => 'Excelente', 'warning' => 'Atenção', 'danger' => 'Crítico', default => 'Sem dados' };
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($psOpen) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:chart-2-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">PageSpeed</span>
                        @if($overallScoreVal !== null)
                            <x-ds::badge variant="{{ $overallVariant }}">{{ $overallScoreVal }} — {{ $overallLabel }}</x-ds::badge>
                        @endif
                        @if($psStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($psStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>

                <div x-show="open" x-cloak class="mt-5 space-y-5">
                    @if($psStatus === 'running')
                        <x-ds::spinner label="Analisando PageSpeed..." />
                    @else
                        {{-- Status cards --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4">
                                <div class="text-xs font-medium text-(--text-muted)">Status geral</div>
                                <div class="mt-2 flex items-center gap-3">
                                    @if($overallScoreVal !== null)
                                        <div class="text-3xl font-bold text-(--text-primary)">{{ $overallScoreVal }}</div>
                                    @endif
                                    <x-ds::badge variant="{{ $overallVariant }}">{{ $overallLabel }}</x-ds::badge>
                                </div>
                                <div class="mt-1 text-xs text-(--text-muted)">Média das 4 categorias</div>
                            </div>
                            <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4">
                                <div class="text-xs font-medium text-(--text-muted)">Última análise</div>
                                <div class="mt-2 text-base font-semibold text-(--text-primary)">{{ $pageSpeedLastRunAt ?: '—' }}</div>
                                <div class="mt-1 text-xs text-(--text-muted)">
                                    {{ config('services.pagespeed.key') ? 'API Key configurada' : 'Sem API Key (quota limitada)' }}
                                </div>
                            </div>
                            <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4">
                                <div class="text-xs font-medium text-(--text-muted)">Histórico de análises</div>
                                <div class="mt-2 text-3xl font-bold text-(--text-primary)">{{ count($history) }}</div>
                                <div class="mt-1 text-xs text-(--text-muted)">registros (últimos 10)</div>
                            </div>
                        </div>

                        {{-- Chart --}}
                        @if(count($chartHistory) >= 2)
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
                                        window.addEventListener('livewire:morph', () => {
                                            this.isDark = document.documentElement.classList.contains('dark');
                                            this.chart?.destroy(); this.renderChart();
                                        });
                                    },
                                    renderChart() {
                                        const options = {
                                            chart: { type: 'line', height: 260, toolbar: { show: false }, zoom: { enabled: false }, background: 'transparent', animations: { enabled: true, speed: 400 } },
                                            theme: { mode: this.isDark ? 'dark' : 'light' },
                                            series: this.series,
                                            xaxis: { categories: this.labels, labels: { style: { fontSize: '11px' } } },
                                            yaxis: { min: 0, max: 100, tickAmount: 5, labels: { style: { fontSize: '11px' }, formatter: (v) => v + '' } },
                                            colors: this.colors,
                                            stroke: { curve: 'smooth', width: 2 },
                                            markers: { size: this.series[0].data.length <= 10 ? 4 : 0 },
                                            legend: { position: 'top', fontSize: '12px', horizontalAlign: 'left' },
                                            grid: { borderColor: this.isDark ? '#374151' : '#e5e7eb', strokeDashArray: 4 },
                                            tooltip: { shared: true, intersect: false },
                                            annotations: { yaxis: [
                                                { y: 90, borderColor: '#10b981', strokeDashArray: 4, label: { text: 'Bom (90)', style: { fontSize: '10px', color: '#10b981' } } },
                                                { y: 50, borderColor: '#f59e0b', strokeDashArray: 4, label: { text: 'Atenção (50)', style: { fontSize: '10px', color: '#f59e0b' } } },
                                            ]},
                                        };
                                        this.chart = new ApexCharts(this.$refs.chartEl, options);
                                        this.chart.render();
                                    }
                                }"
                            >
                                <div x-ref="chartEl" class="w-full"></div>
                            </div>
                        @endif

                        {{-- Scores salvos quando não há resultado fresco --}}
                        @if(!$pageSpeed && $web)
                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                @foreach(['performance' => 'Performance', 'seo' => 'SEO', 'accessibility' => 'A11y', 'best_practices' => 'Best Pract.'] as $field => $catLabel)
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
                        @endif

                        {{-- Comparativo Mobile vs Desktop --}}
                        @if($pageSpeed)
                            <div class="overflow-x-auto rounded-lg border border-(--border-subtle)">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                                        <tr>
                                            <th class="px-4 py-3">Categoria</th>
                                            <th class="px-4 py-3 text-center">Mobile</th>
                                            <th class="px-4 py-3 text-center">Desktop</th>
                                            <th class="px-4 py-3 text-center">Δ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['performance' => 'Performance', 'seo' => 'SEO', 'accessibility' => 'Accessibility', 'best_practices' => 'Best Practices'] as $scoreKey => $catLabel)
                                            @php
                                                $mob  = $pageSpeed['mobile']['scores'][$scoreKey] ?? null;
                                                $desk = $pageSpeed['desktop']['scores'][$scoreKey] ?? null;
                                                $d    = $delta($mob, $desk);
                                            @endphp
                                            <tr class="border-t border-(--border-subtle)">
                                                <td class="px-4 py-3 font-medium text-(--text-primary)">{{ $catLabel }}</td>
                                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariant($mob) }}">{{ $scoreLabel($mob) }}</x-ds::badge></td>
                                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $scoreVariant($desk) }}">{{ $scoreLabel($desk) }}</x-ds::badge></td>
                                                <td class="px-4 py-3 text-center"><x-ds::badge variant="{{ $deltaVariant($d) }}">{{ $deltaLabel($d) }}</x-ds::badge></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Per strategy: CWV + opportunities --}}
                            <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                                @foreach(['mobile' => 'Mobile', 'desktop' => 'Desktop'] as $strategyKey => $strategyLabel)
                                    @php
                                        $sd   = $pageSpeed[$strategyKey] ?? null;
                                        $serr = $pageSpeedErrors[$strategyKey] ?? null;
                                        $opps = $sd['opportunities'] ?? [];
                                    @endphp
                                    @if($sd || $serr)
                                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4 space-y-4">
                                            <div class="font-medium text-(--text-primary)">{{ $strategyLabel }}</div>
                                            @if($serr)
                                                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">{{ $serr }}</x-ds::alert>
                                            @endif
                                            @if($sd)
                                                {{-- 4 scores --}}
                                                <div class="grid grid-cols-4 gap-1.5">
                                                    @foreach(['performance' => 'Perf', 'seo' => 'SEO', 'accessibility' => 'A11y', 'best_practices' => 'BP'] as $sk => $cl)
                                                        @php
                                                            $sv  = $sd['scores'][$sk] ?? null;
                                                            $pct = $sv !== null ? (int) round((float) $sv * 100) : null;
                                                        @endphp
                                                        <div class="overflow-hidden rounded-lg border border-(--border-subtle)">
                                                            <div class="h-1 {{ $scoreBar($sv) }}"></div>
                                                            <div class="p-2 text-center">
                                                                <div class="text-xl font-bold leading-none {{ $scoreColor($sv) }}">{{ $pct ?? '—' }}</div>
                                                                <div class="mt-0.5 text-[10px] text-(--text-muted)">{{ $cl }}</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                {{-- CWV --}}
                                                @php
                                                    $cwvItems = [
                                                        ['label' => 'FCP',   'metric' => 'fcp',         'raw' => $sd['metrics']['fcp_ms'] ?? null,         'display' => $sd['display']['fcp'] ?? null,         'fmt' => 'sec'],
                                                        ['label' => 'LCP',   'metric' => 'lcp',         'raw' => $sd['metrics']['lcp_ms'] ?? null,         'display' => $sd['display']['lcp'] ?? null,         'fmt' => 'sec'],
                                                        ['label' => 'TBT',   'metric' => 'tbt',         'raw' => $sd['metrics']['tbt_ms'] ?? null,         'display' => $sd['display']['tbt'] ?? null,         'fmt' => 'ms'],
                                                        ['label' => 'CLS',   'metric' => 'cls',         'raw' => $sd['metrics']['cls'] ?? null,            'display' => $sd['display']['cls'] ?? null,         'fmt' => 'cls'],
                                                        ['label' => 'TTFB',  'metric' => 'ttfb',        'raw' => $sd['metrics']['ttfb_ms'] ?? null,        'display' => $sd['display']['ttfb'] ?? null,        'fmt' => 'ms'],
                                                        ['label' => 'SI',    'metric' => 'speed_index', 'raw' => $sd['metrics']['speed_index_ms'] ?? null, 'display' => $sd['display']['speed_index'] ?? null, 'fmt' => 'sec'],
                                                        ['label' => 'INP',   'metric' => 'inp',         'raw' => $sd['metrics']['inp_ms'] ?? null,         'display' => $sd['display']['inp'] ?? null,         'fmt' => 'ms'],
                                                    ];
                                                @endphp
                                                <div class="grid grid-cols-4 gap-1.5">
                                                    @foreach($cwvItems as $cwv)
                                                        @php
                                                            $rawVal  = $cwv['raw'];
                                                            $dispVal = $cwv['display'] ?? match($cwv['fmt']) { 'sec' => $sec($rawVal), 'ms' => $ms($rawVal), 'cls' => $cls($rawVal), default => '—' };
                                                        @endphp
                                                        <div class="overflow-hidden rounded-lg border border-(--border-subtle)" title="{{ $cwv['label'] }}">
                                                            <div class="h-0.5 {{ $cwvBar($cwv['metric'], $rawVal) }}"></div>
                                                            <div class="p-2">
                                                                <div class="text-[10px] text-(--text-muted)">{{ $cwv['label'] }}</div>
                                                                <div class="mt-0.5 text-xs font-semibold {{ $cwvColor($cwv['metric'], $rawVal) }}">{{ $dispVal }}</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                {{-- Opportunities --}}
                                                @if(!empty($opps))
                                                    <div class="space-y-1">
                                                        <div class="text-xs font-semibold text-(--text-muted) uppercase tracking-wider">Oportunidades ({{ count($opps) }})</div>
                                                        @foreach(array_slice($opps, 0, 8) as $opp)
                                                            @php $savings = $opp['savings_ms'] ?? null; @endphp
                                                            <div class="flex items-start justify-between gap-2 rounded-md bg-(--surface-hover) px-3 py-2 text-xs">
                                                                <span class="text-(--text-secondary)">{{ $opp['title'] ?? '—' }}</span>
                                                                @if($savings)
                                                                    <span class="shrink-0 font-mono text-(--status-warning)">
                                                                        {{ $savings >= 1000 ? round($savings / 1000, 1) . ' s' : $savings . ' ms' }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

    {{-- ── Headers de Segurança ── --}}
    @if(isset($moduleStatus['security_headers']))
        @php
            $shStatus = $moduleStatus['security_headers'];
            $shOpen   = $moduleExpanded['security_headers'] ?? true;
            $shVariant = $securityScore >= 80 ? 'success' : ($securityScore >= 50 ? 'warning' : 'danger');
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($shOpen) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:shield-check-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">Headers de Segurança</span>
                        @if($shStatus !== 'running' && !empty($securityChecks))
                            <x-ds::badge variant="{{ $shVariant }}">Score {{ $securityScore }}/100</x-ds::badge>
                        @endif
                        @if($shStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($shStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-5">
                    @if($shStatus === 'running')
                        <x-ds::spinner label="Verificando headers..." />
                    @elseif(!empty($securityChecks))
                        <div class="space-y-2">
                            @foreach($securityChecks as $check)
                                @php
                                    $passed  = $check['passed'] ?? false;
                                    $icon    = $passed ? 'solar:check-circle-linear' : 'solar:close-circle-linear';
                                    $cls     = $passed ? 'text-(--status-success)' : 'text-(--status-danger)';
                                @endphp
                                <div class="flex items-start gap-3 rounded-lg border border-(--border-subtle) bg-(--surface-card) px-4 py-3">
                                    <iconify-icon icon="{{ $icon }}" class="mt-0.5 shrink-0 text-base {{ $cls }}"></iconify-icon>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-(--text-primary)">{{ $check['header'] ?? '—' }}</div>
                                        @if(!empty($check['value']))
                                            <div class="mt-0.5 truncate font-mono text-xs text-(--text-muted)">{{ $check['value'] }}</div>
                                        @endif
                                        @if(!empty($check['description']))
                                            <div class="mt-0.5 text-xs text-(--text-secondary)">{{ $check['description'] }}</div>
                                        @endif
                                    </div>
                                    @if(!empty($check['severity']))
                                        @php $sevVariant = match(strtolower($check['severity'])) { 'critical' => 'danger', 'high' => 'warning', default => 'secondary' }; @endphp
                                        <x-ds::badge variant="{{ $sevVariant }}">{{ $check['severity'] }}</x-ds::badge>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($shStatus === 'done')
                        <div class="py-4 text-center text-sm text-(--text-muted)">Nenhum dado disponível.</div>
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

    {{-- ── Schema / Open Graph ── --}}
    @if(isset($moduleStatus['schema']))
        @php
            $scStatus = $moduleStatus['schema'];
            $scOpen   = $moduleExpanded['schema'] ?? true;
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($scOpen) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:code-square-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">Schema Markup & Open Graph</span>
                        @if($scStatus !== 'running')
                            @php $schemaCount = count($schemaItems); @endphp
                            <x-ds::badge variant="{{ $schemaCount > 0 ? 'success' : 'secondary' }}">{{ $schemaCount }} schemas</x-ds::badge>
                            @php $ogCount = count($ogTags) + count($twitterTags); @endphp
                            @if($ogCount > 0)
                                <x-ds::badge variant="secondary">{{ $ogCount }} OG/Twitter</x-ds::badge>
                            @endif
                        @endif
                        @if($scStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($scStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-5 space-y-5">
                    @if($scStatus === 'running')
                        <x-ds::spinner label="Detectando schemas..." />
                    @else
                        {{-- Schema.org --}}
                        @if(!empty($schemaItems))
                            <div>
                                <div class="mb-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">JSON-LD / Schema.org</div>
                                <div class="overflow-x-auto rounded-lg border border-(--border-subtle)">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-(--surface-hover) text-xs text-(--text-secondary)">
                                            <tr>
                                                <th class="px-4 py-2">Tipo</th>
                                                <th class="px-4 py-2">Válido</th>
                                                <th class="px-4 py-2">Problemas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schemaItems as $item)
                                                <tr class="border-t border-(--border-subtle)">
                                                    <td class="px-4 py-2 font-mono text-xs font-medium text-(--text-primary)">{{ $item['type'] ?? '—' }}</td>
                                                    <td class="px-4 py-2">
                                                        @php $valid = $item['valid'] ?? false; @endphp
                                                        <x-ds::badge variant="{{ $valid ? 'success' : 'warning' }}">{{ $valid ? 'Válido' : 'Avisos' }}</x-ds::badge>
                                                    </td>
                                                    <td class="px-4 py-2 text-xs text-(--text-muted)">
                                                        @if(!empty($item['issues']))
                                                            {{ implode(', ', (array) $item['issues']) }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <x-ds::alert variant="warning" icon="solar:danger-triangle-linear">Nenhum Schema.org (JSON-LD) encontrado.</x-ds::alert>
                        @endif

                        {{-- Open Graph --}}
                        @php
                            $ogRequired = ['title', 'description', 'image', 'url', 'type'];
                            $ogMissing  = array_filter($ogRequired, fn($k) => empty($ogTags[$k]));
                        @endphp
                        <div>
                            <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">
                                Open Graph
                                @if(empty($ogTags))
                                    <x-ds::badge variant="danger">Ausente</x-ds::badge>
                                @elseif(empty($ogMissing))
                                    <x-ds::badge variant="success">Completo</x-ds::badge>
                                @else
                                    <x-ds::badge variant="warning">{{ count($ogMissing) }} tag(s) faltando</x-ds::badge>
                                @endif
                            </div>

                            @if(!empty($ogTags))
                                {{-- Preview card --}}
                                <div class="mb-3 overflow-hidden rounded-lg border border-(--border-subtle)">
                                    @if(!empty($ogTags['image']))
                                        <img src="{{ $ogTags['image'] }}" alt="OG Image"
                                             class="h-36 w-full object-cover"
                                             onerror="this.style.display='none'">
                                    @else
                                        <div class="flex h-24 items-center justify-center bg-(--surface-hover) text-xs text-(--text-muted)">Sem og:image</div>
                                    @endif
                                    <div class="p-3">
                                        @if(!empty($ogTags['site_name']))
                                            <div class="text-[10px] uppercase text-(--text-muted)">{{ $ogTags['site_name'] }}</div>
                                        @endif
                                        <div class="text-sm font-semibold text-(--text-primary)">{{ $ogTags['title'] ?? '— sem og:title' }}</div>
                                        @if(!empty($ogTags['description']))
                                            <div class="mt-0.5 line-clamp-2 text-xs text-(--text-secondary)">{{ $ogTags['description'] }}</div>
                                        @endif
                                        @if(!empty($ogTags['url']))
                                            <div class="mt-1 truncate text-[10px] text-(--text-muted)">{{ $ogTags['url'] }}</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Checklist obrigatórias --}}
                                <div class="mb-3 grid grid-cols-2 gap-1 sm:grid-cols-3">
                                    @foreach($ogRequired as $req)
                                        @php $present = !empty($ogTags[$req]); @endphp
                                        <div class="flex items-center gap-1.5 text-xs">
                                            <iconify-icon icon="{{ $present ? 'solar:check-circle-linear' : 'solar:close-circle-linear' }}"
                                                          class="{{ $present ? 'text-(--status-success)' : 'text-(--status-danger)' }}"></iconify-icon>
                                            <span class="text-(--text-secondary)">og:{{ $req }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Todas as tags --}}
                                <div class="space-y-1">
                                    @foreach($ogTags as $tag => $value)
                                        <div class="flex gap-2 rounded-md bg-(--surface-hover) px-3 py-1.5 text-xs">
                                            <span class="w-32 shrink-0 font-mono text-(--text-muted)">og:{{ $tag }}</span>
                                            @if(in_array($tag, ['image', 'image:secure_url']) && str_starts_with($value, 'http'))
                                                <a href="{{ $value }}" target="_blank" rel="noopener"
                                                   class="truncate text-blue-600 hover:underline" title="{{ $value }}">{{ $value }}</a>
                                            @else
                                                <span class="truncate text-(--text-secondary)" title="{{ $value }}">{{ $value }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-(--text-muted)">Nenhuma tag Open Graph encontrada. Instale um plugin de SEO (Yoast, RankMath) ou adicione manualmente.</p>
                            @endif
                        </div>

                        {{-- Twitter / X Cards --}}
                        @php
                            $twRequired   = ['card', 'title', 'description', 'image'];
                            $twMissing    = array_filter($twRequired, fn($k) => empty($twitterTags[$k]));
                            $twCardType   = $twitterTags['card'] ?? null;
                            $twTitle      = $twitterTags['title'] ?? $ogTags['title'] ?? null;
                            $twDesc       = $twitterTags['description'] ?? $ogTags['description'] ?? null;
                            $twImage      = $twitterTags['image'] ?? $ogTags['image'] ?? null;
                            $twSite       = $twitterTags['site'] ?? $ogTags['site_name'] ?? null;
                            $twCreator    = $twitterTags['creator'] ?? null;
                            $twLargeCard  = $twCardType === 'summary_large_image' || $twCardType === 'app';
                        @endphp
                        <div>
                            <div class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-(--text-muted)">
                                Twitter / X Cards
                                @if(empty($twitterTags))
                                    <x-ds::badge variant="warning">Ausente</x-ds::badge>
                                @elseif(empty($twMissing))
                                    <x-ds::badge variant="success">Completo</x-ds::badge>
                                @else
                                    <x-ds::badge variant="warning">{{ count($twMissing) }} tag(s) faltando</x-ds::badge>
                                @endif
                            </div>

                            @if(!empty($twitterTags))
                                {{-- Preview card estilo X/Twitter --}}
                                <div class="mb-4 overflow-hidden rounded-2xl border border-(--border-subtle) bg-(--surface-hover)">
                                    @if($twLargeCard && $twImage)
                                        {{-- summary_large_image: imagem no topo --}}
                                        <img src="{{ $twImage }}" alt="Twitter card image"
                                             class="h-40 w-full object-cover"
                                             onerror="this.style.display='none'">
                                        <div class="p-3">
                                            @if($twSite)
                                                <div class="text-[10px] text-(--text-muted)">{{ $twSite }}</div>
                                            @endif
                                            <div class="text-sm font-bold leading-snug text-(--text-primary)">{{ $twTitle ?? '— sem título' }}</div>
                                            @if($twDesc)
                                                <div class="mt-0.5 line-clamp-2 text-xs text-(--text-secondary)">{{ $twDesc }}</div>
                                            @endif
                                        </div>
                                    @else
                                        {{-- summary: imagem à esquerda --}}
                                        <div class="flex gap-0">
                                            @if($twImage)
                                                <img src="{{ $twImage }}" alt="Twitter card image"
                                                     class="h-28 w-28 shrink-0 object-cover"
                                                     onerror="this.style.display='none'">
                                            @else
                                                <div class="flex h-28 w-28 shrink-0 items-center justify-center bg-(--surface-card)">
                                                    <iconify-icon icon="solar:gallery-minimalistic-linear" class="text-2xl text-(--text-muted)"></iconify-icon>
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1 p-3">
                                                @if($twSite)
                                                    <div class="text-[10px] text-(--text-muted)">{{ $twSite }}</div>
                                                @endif
                                                <div class="text-sm font-bold leading-snug text-(--text-primary)">{{ $twTitle ?? '— sem título' }}</div>
                                                @if($twDesc)
                                                    <div class="mt-0.5 line-clamp-2 text-xs text-(--text-secondary)">{{ $twDesc }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Tipo + creator --}}
                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                    @if($twCardType)
                                        <x-ds::badge variant="secondary">{{ $twCardType }}</x-ds::badge>
                                    @endif
                                    @if($twCreator)
                                        <span class="text-xs text-(--text-muted)">Criador: <span class="text-blue-500">{{ $twCreator }}</span></span>
                                    @endif
                                    @if($twSite)
                                        <span class="text-xs text-(--text-muted)">Site: <span class="text-(--text-secondary)">{{ $twSite }}</span></span>
                                    @endif
                                </div>

                                {{-- Checklist --}}
                                <div class="mb-3 grid grid-cols-2 gap-1">
                                    @foreach($twRequired as $req)
                                        @php $present = !empty($twitterTags[$req]); @endphp
                                        <div class="flex items-center gap-1.5 text-xs">
                                            <iconify-icon icon="{{ $present ? 'solar:check-circle-linear' : 'solar:close-circle-linear' }}"
                                                          class="{{ $present ? 'text-(--status-success)' : 'text-(--status-danger)' }}"></iconify-icon>
                                            <span class="text-(--text-secondary)">twitter:{{ $req }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Todas as tags --}}
                                <div class="space-y-1">
                                    @foreach($twitterTags as $tag => $value)
                                        <div class="flex gap-2 rounded-md bg-(--surface-hover) px-3 py-1.5 text-xs">
                                            <span class="w-36 shrink-0 font-mono text-(--text-muted)">twitter:{{ $tag }}</span>
                                            @if($tag === 'image' && str_starts_with($value, 'http'))
                                                <a href="{{ $value }}" target="_blank" rel="noopener"
                                                   class="truncate text-blue-600 hover:underline" title="{{ $value }}">{{ $value }}</a>
                                            @else
                                                <span class="truncate text-(--text-secondary)" title="{{ $value }}">{{ $value }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-(--text-muted)">Nenhuma Twitter Card encontrada. Adicione as meta tags <code class="font-mono">twitter:card</code>, <code class="font-mono">twitter:title</code>, <code class="font-mono">twitter:description</code> e <code class="font-mono">twitter:image</code>.</p>
                            @endif
                        </div>

                        @if(empty($schemaItems) && empty($ogTags) && empty($twitterTags) && $scStatus === 'done')
                            <div class="py-4 text-center text-sm text-(--text-muted)">Nenhum dado encontrado.</div>
                        @endif
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

    {{-- ── GEO / IA ── --}}
    @if(isset($moduleStatus['geo']))
        @php
            $geoStatus  = $moduleStatus['geo'];
            $geoOpenVal = $moduleExpanded['geo'] ?? true;
            $geoVariant = $geoScore >= 80 ? 'success' : ($geoScore >= 50 ? 'warning' : 'danger');
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($geoOpenVal) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:magic-stick-3-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">GEO · Generative Engine Optimization</span>
                        @if($geoStatus !== 'running' && !empty($geoChecks))
                            <x-ds::badge variant="{{ $geoVariant }}">Score {{ $geoScore }}/100</x-ds::badge>
                        @endif
                        @if($geoStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($geoStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-5">
                    @if($geoStatus === 'running')
                        <x-ds::spinner label="Analisando sinais GEO..." />
                    @elseif(!empty($geoChecks))
                        <div class="space-y-2">
                            @foreach($geoChecks as $check)
                                @php
                                    $chStatus = $check['status'] ?? 'fail';
                                    $chIcon   = match($chStatus) {
                                        'pass'  => 'solar:check-circle-linear',
                                        'warn'  => 'solar:danger-triangle-linear',
                                        default => 'solar:close-circle-linear',
                                    };
                                    $chColor  = match($chStatus) {
                                        'pass'  => 'text-(--status-success)',
                                        'warn'  => 'text-(--status-warning)',
                                        default => 'text-(--status-danger)',
                                    };
                                @endphp
                                <div class="flex items-start gap-3 rounded-lg border border-(--border-subtle) bg-(--surface-card) px-4 py-3">
                                    <iconify-icon icon="{{ $chIcon }}" class="mt-0.5 shrink-0 text-base {{ $chColor }}"></iconify-icon>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-(--text-primary)">{{ $check['name'] ?? '—' }}</span>
                                        </div>
                                        @if(!empty($check['description']))
                                            <div class="mt-0.5 text-xs text-(--text-muted)">{{ $check['description'] }}</div>
                                        @endif
                                        @if(!empty($check['value']))
                                            <div class="mt-1 text-xs text-(--text-secondary)">{{ $check['value'] }}</div>
                                        @endif
                                        @if($chStatus !== 'pass' && !empty($check['tip']))
                                            <div class="mt-1 text-xs text-(--text-muted)">💡 {{ $check['tip'] }}</div>
                                        @endif
                                    </div>
                                    @if(!empty($check['weight']))
                                        <span class="shrink-0 text-xs text-(--text-muted)">{{ $check['weight'] }}pts</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($geoStatus === 'done')
                        <div class="py-4 text-center text-sm text-(--text-muted)">Nenhum dado disponível.</div>
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

    {{-- ── Sitemap & Robots ── --}}
    @if(isset($moduleStatus['sitemap']))
        @php
            $smStatus = $moduleStatus['sitemap'];
            $smOpen   = $moduleExpanded['sitemap'] ?? true;
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($smOpen) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:map-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">Sitemap & Robots.txt</span>
                        @if($smStatus !== 'running')
                            @php
                                $smOk = is_array($sitemapData) && ($sitemapData['found'] ?? false);
                                $rbOk = is_array($robotsData) && ($robotsData['found'] ?? false);
                            @endphp
                            <x-ds::badge variant="{{ $smOk ? 'success' : 'warning' }}">Sitemap {{ $smOk ? 'OK' : 'não encontrado' }}</x-ds::badge>
                            <x-ds::badge variant="{{ $rbOk ? 'success' : 'warning' }}">Robots {{ $rbOk ? 'OK' : 'não encontrado' }}</x-ds::badge>
                        @endif
                        @if($smStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($smStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @if($smStatus === 'running')
                        <x-ds::spinner label="Verificando sitemap e robots..." class="col-span-2" />
                    @else
                        {{-- Sitemap --}}
                        <div class="space-y-3">
                            <div class="text-xs font-semibold uppercase tracking-wider text-(--text-muted)">Sitemap.xml</div>
                            @if($sitemapData)
                                @if($sitemapData['found'] ?? false)
                                    <div class="space-y-2">
                                        <div class="flex gap-2 text-xs">
                                            <span class="w-28 shrink-0 text-(--text-muted)">URL</span>
                                            <a href="{{ $sitemapData['url'] }}" target="_blank" rel="noopener"
                                               class="break-all text-blue-600 hover:underline">{{ $sitemapData['url'] }}</a>
                                        </div>
                                        <div class="flex gap-2 text-xs">
                                            <span class="w-28 shrink-0 text-(--text-muted)">Tipo</span>
                                            <span class="text-(--text-secondary)">
                                                {{ ($sitemapData['is_index'] ?? false) ? 'Sitemap Index' : 'Sitemap simples' }}
                                            </span>
                                        </div>
                                        <div class="flex gap-2 text-xs">
                                            <span class="w-28 shrink-0 text-(--text-muted)">URLs indexadas</span>
                                            <span class="text-(--text-secondary)">{{ $sitemapData['url_count'] ?? 0 }}</span>
                                        </div>
                                        <div class="flex gap-2 text-xs">
                                            <span class="w-28 shrink-0 text-(--text-muted)">Lastmod</span>
                                            <x-ds::badge variant="{{ ($sitemapData['has_lastmod'] ?? false) ? 'success' : 'warning' }}">
                                                {{ ($sitemapData['has_lastmod'] ?? false) ? 'Presente' : 'Ausente' }}
                                            </x-ds::badge>
                                        </div>
                                        <div class="flex gap-2 text-xs">
                                            <span class="w-28 shrink-0 text-(--text-muted)">Image sitemap</span>
                                            <x-ds::badge variant="{{ ($sitemapData['has_images'] ?? false) ? 'success' : 'secondary' }}">
                                                {{ ($sitemapData['has_images'] ?? false) ? 'Sim' : 'Não' }}
                                            </x-ds::badge>
                                        </div>
                                        @if(!empty($sitemapData['sample_urls']))
                                            <div>
                                                <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Amostra de URLs</div>
                                                <div class="space-y-0.5">
                                                    @foreach(array_slice($sitemapData['sample_urls'], 0, 5) as $su)
                                                        <div class="truncate text-[11px] text-(--text-muted)" title="{{ $su }}">{{ $su }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <x-ds::badge variant="danger">Não encontrado</x-ds::badge>
                                @endif
                                @if(!empty($sitemapData['issues']))
                                    <div class="space-y-1">
                                        @foreach($sitemapData['issues'] as $issue)
                                            <x-ds::alert variant="warning">{{ $issue }}</x-ds::alert>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="text-sm text-(--text-muted)">—</div>
                            @endif
                        </div>

                        {{-- Robots --}}
                        <div class="space-y-3">
                            <div class="text-xs font-semibold uppercase tracking-wider text-(--text-muted)">Robots.txt</div>
                            @if($robotsData)
                                @if($robotsData['found'] ?? false)
                                    <div class="space-y-2">
                                        <div class="flex gap-2 text-xs">
                                            <span class="w-28 shrink-0 text-(--text-muted)">Regras totais</span>
                                            <span class="text-(--text-secondary)">{{ $robotsData['rules_count'] ?? 0 }}</span>
                                        </div>
                                        <div class="flex gap-2 text-xs">
                                            <span class="w-28 shrink-0 text-(--text-muted)">Bloqueia tudo</span>
                                            @php $disAll = $robotsData['disallow_all'] ?? false; @endphp
                                            <x-ds::badge variant="{{ $disAll ? 'danger' : 'success' }}">
                                                {{ $disAll ? 'Sim — Disallow: /' : 'Não' }}
                                            </x-ds::badge>
                                        </div>
                                        @if(!empty($robotsData['sitemap_urls']))
                                            <div class="flex gap-2 text-xs">
                                                <span class="w-28 shrink-0 text-(--text-muted)">Sitemap declarado</span>
                                                <div class="space-y-0.5">
                                                    @foreach($robotsData['sitemap_urls'] as $su)
                                                        <a href="{{ $su }}" target="_blank" rel="noopener"
                                                           class="block truncate text-blue-600 hover:underline" title="{{ $su }}">{{ $su }}</a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex gap-2 text-xs">
                                                <span class="w-28 shrink-0 text-(--text-muted)">Sitemap declarado</span>
                                                <x-ds::badge variant="warning">Não declarado</x-ds::badge>
                                            </div>
                                        @endif
                                        @if(!empty($robotsData['content']))
                                            <div>
                                                <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Conteúdo</div>
                                                <pre class="max-h-40 overflow-y-auto rounded bg-(--surface-hover) p-2 text-[10px] leading-relaxed text-(--text-secondary)">{{ $robotsData['content'] }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <x-ds::badge variant="danger">Não encontrado</x-ds::badge>
                                @endif
                                @if(!empty($robotsData['issues']))
                                    <div class="space-y-1">
                                        @foreach($robotsData['issues'] as $issue)
                                            <x-ds::alert variant="warning">{{ $issue }}</x-ds::alert>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="text-sm text-(--text-muted)">—</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

    {{-- ── Análise com IA ── --}}
    @if(isset($moduleStatus['ai_content']))
        @php
            $aiStatus  = $moduleStatus['ai_content'];
            $aiOpenVal = $moduleExpanded['ai_content'] ?? true;
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($aiOpenVal) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:stars-minimalistic-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">Análise de Conteúdo com IA</span>
                        @if($aiStatus !== 'running' && $aiAnalysis)
                            @php $aiSeoScore = $aiAnalysis['score_qualidade'] ?? null; @endphp
                            @if($aiSeoScore !== null)
                                <x-ds::badge variant="{{ $aiSeoScore >= 80 ? 'success' : ($aiSeoScore >= 50 ? 'warning' : 'danger') }}">Score {{ $aiSeoScore }}/100</x-ds::badge>
                            @endif
                        @endif
                        @if($aiStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($aiStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-5">
                    @if($aiStatus === 'running')
                        <x-ds::spinner label="Analisando conteúdo com IA..." />
                    @elseif($aiAnalysis)
                        @php
                            $aiScore = $aiAnalysis['score_qualidade'] ?? null;
                        @endphp
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                            {{-- Coluna 1: resumo + scores --}}
                            <div class="space-y-3">
                                @if($aiScore !== null)
                                    <div class="flex flex-wrap gap-2">
                                        <x-ds::badge variant="{{ $aiScore >= 80 ? 'success' : ($aiScore >= 50 ? 'warning' : 'danger') }}">
                                            Score {{ $aiScore }}/100
                                        </x-ds::badge>
                                    </div>
                                @endif
                                @if(!empty($aiAnalysis['resumo']))
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Resumo</div>
                                        <div class="text-xs text-(--text-secondary)">{{ $aiAnalysis['resumo'] }}</div>
                                    </div>
                                @endif
                                @if(!empty($aiAnalysis['tom_de_voz']))
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Tom de voz</div>
                                        <div class="text-xs text-(--text-secondary)">{{ $aiAnalysis['tom_de_voz'] }}</div>
                                    </div>
                                @endif
                                @if(!empty($aiAnalysis['publico_alvo_estimado']))
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Público-alvo estimado</div>
                                        <div class="text-xs text-(--text-secondary)">{{ $aiAnalysis['publico_alvo_estimado'] }}</div>
                                    </div>
                                @endif
                                @if(!empty($aiAnalysis['legibilidade']))
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Legibilidade</div>
                                        <div class="text-xs text-(--text-secondary)">{{ $aiAnalysis['legibilidade'] }}</div>
                                    </div>
                                @endif
                                @if(!empty($aiAnalysis['intencao_de_busca']))
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Intenção de busca</div>
                                        <div class="text-xs text-(--text-secondary)">{{ $aiAnalysis['intencao_de_busca'] }}</div>
                                    </div>
                                @endif
                                @if(!empty($aiAnalysis['palavras_chave_detectadas']))
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Palavras-chave detectadas</div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach((array) $aiAnalysis['palavras_chave_detectadas'] as $kw)
                                                <x-ds::badge variant="secondary">{{ $kw }}</x-ds::badge>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($aiAnalysis['palavras_chave_sugeridas']))
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Palavras-chave sugeridas</div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach((array) $aiAnalysis['palavras_chave_sugeridas'] as $kw)
                                                <x-ds::badge variant="info">{{ $kw }}</x-ds::badge>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Coluna 2: pontos fortes + oportunidades --}}
                            <div class="space-y-3">
                                @if(!empty($aiAnalysis['pontos_fortes']))
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Pontos fortes</div>
                                        <ul class="space-y-1">
                                            @foreach((array) $aiAnalysis['pontos_fortes'] as $s)
                                                <li class="flex items-start gap-1.5 text-xs text-(--text-secondary)">
                                                    <iconify-icon icon="solar:check-circle-linear" class="mt-0.5 shrink-0 text-(--status-success)"></iconify-icon>
                                                    {{ $s }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if(!empty($aiAnalysis['oportunidades_melhoria']))
                                    <div>
                                        <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Oportunidades de melhoria</div>
                                        <ul class="space-y-1">
                                            @foreach((array) $aiAnalysis['oportunidades_melhoria'] as $w)
                                                <li class="flex items-start gap-1.5 text-xs text-(--text-secondary)">
                                                    <iconify-icon icon="solar:danger-triangle-linear" class="mt-0.5 shrink-0 text-(--status-warning)"></iconify-icon>
                                                    {{ $w }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            {{-- Coluna 3: recomendações --}}
                            <div>
                                @if(!empty($aiAnalysis['recomendacoes_conteudo']))
                                    <div class="mb-1 text-[10px] uppercase tracking-wider text-(--text-muted)">Recomendações de conteúdo</div>
                                    <ul class="space-y-1">
                                        @foreach((array) $aiAnalysis['recomendacoes_conteudo'] as $rec)
                                            <li class="flex items-start gap-1.5 text-xs text-(--text-secondary)">
                                                <iconify-icon icon="solar:arrow-right-linear" class="mt-0.5 shrink-0 text-(--brand-primary)"></iconify-icon>
                                                {{ $rec }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @elseif($aiStatus === 'done')
                        <div class="py-4 text-center text-sm text-(--text-muted)">Nenhum dado disponível. Verifique a OPENAI_API_KEY.</div>
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

    {{-- ── Links quebrados ── --}}
    @if(isset($moduleStatus['links']))
        @php
            $lkStatus  = $moduleStatus['links'];
            $lkOpenVal = $moduleExpanded['links'] ?? true;
            $broken    = array_filter($links, fn($l) => ($l['status'] ?? 200) >= 400 || ($l['status'] ?? 200) === 0);
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($lkOpenVal) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:link-minimalistic-2-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">Links</span>
                        @if($lkStatus !== 'running' && !empty($links))
                            <x-ds::badge variant="secondary">{{ count($links) }} total</x-ds::badge>
                            @if(count($broken) > 0)
                                <x-ds::badge variant="danger">{{ count($broken) }} quebrados</x-ds::badge>
                            @else
                                <x-ds::badge variant="success">Todos OK</x-ds::badge>
                            @endif
                        @endif
                        @if($lkStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($lkStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-5">
                    @if($lkStatus === 'running')
                        <x-ds::spinner label="Verificando links..." />
                    @elseif(!empty($links))
                        <div class="overflow-x-auto rounded-lg border border-(--border-subtle)">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-(--surface-hover) text-xs text-(--text-secondary)">
                                    <tr>
                                        <th class="px-4 py-2">URL</th>
                                        <th class="px-4 py-2">Status</th>
                                        <th class="px-4 py-2">Tipo</th>
                                        <th class="px-4 py-2">Texto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($links, 0, 100) as $link)
                                        @php
                                            $lkCode    = $link['status'] ?? 0;
                                            $lkVariant = $lkCode >= 400 || $lkCode === 0 ? 'danger' : ($lkCode >= 300 ? 'warning' : 'success');
                                            $lkLabel   = $lkCode === 0 ? 'Erro' : (string) $lkCode;
                                        @endphp
                                        <tr class="border-t border-(--border-subtle) hover:bg-(--surface-hover)">
                                            <td class="px-4 py-2 max-w-[240px]">
                                                <span class="block truncate text-xs text-(--text-primary)" title="{{ $link['url'] ?? '' }}">{{ $link['url'] ?? '—' }}</span>
                                            </td>
                                            <td class="px-4 py-2">
                                                <x-ds::badge variant="{{ $lkVariant }}">{{ $lkLabel }}</x-ds::badge>
                                            </td>
                                            <td class="px-4 py-2 text-xs text-(--text-muted)">{{ $link['type'] ?? '—' }}</td>
                                            <td class="px-4 py-2 max-w-[160px]">
                                                <span class="block truncate text-xs text-(--text-secondary)">{{ $link['anchor'] ?? '—' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(count($links) > 100)
                                        <tr><td colspan="4" class="px-4 py-2 text-xs text-(--text-muted) text-center">... e mais {{ count($links) - 100 }} links</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @elseif($lkStatus === 'done')
                        <div class="py-4 text-center text-sm text-(--text-muted)">Nenhum link encontrado.</div>
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

    {{-- ── WP Imagens ── --}}
    @if(isset($moduleStatus['wp_images']))
        @php
            $wiStatus  = $moduleStatus['wp_images'];
            $wiOpenVal = $moduleExpanded['wp_images'] ?? true;
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($wiOpenVal) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:gallery-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">Imagens WordPress</span>
                        @if($wiStatus !== 'running' && $totalImages > 0)
                            <x-ds::badge variant="secondary">{{ $totalImages }} imagens</x-ds::badge>
                            @if($imagesError > 0) <x-ds::badge variant="danger">{{ $imagesError }} com erro</x-ds::badge> @endif
                            @if($imagesNotWebp > 0) <x-ds::badge variant="warning">{{ $imagesNotWebp }} não WEBP</x-ds::badge> @endif
                        @endif
                        @if($wiStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($wiStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-5 space-y-4">
                    @if($wiStatus === 'running')
                        <x-ds::spinner label="Auditando imagens..." />
                    @else
                        {{-- Counter cards --}}
                        <div class="grid grid-cols-3 gap-2 sm:grid-cols-6">
                            @foreach([
                                ['Total',      $totalImages,           'text-(--text-primary)'],
                                ['Corretas',   $imagesOk,              'text-(--status-success)'],
                                ['Com erro',   $imagesError,           'text-(--status-warning)'],
                                ['Não WEBP',   $imagesNotWebp,         'text-(--status-warning)'],
                                ['Sem alt',    $imagesWithoutAlt,      'text-(--status-warning)'],
                                ['>500KB',     $imagesLarge,           'text-(--status-danger)'],
                            ] as [$label, $count, $clr])
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3 text-center">
                                    <div class="text-xs text-(--text-muted)">{{ $label }}</div>
                                    <div class="mt-1 text-xl font-bold {{ $clr }}">{{ $count }}</div>
                                </div>
                            @endforeach
                        </div>

                        @if(!empty($images))
                            {{-- Modal páginas da imagem (escopo único Alpine) --}}
                            <div x-data="{ open: false, pages: [], imgUrl: '' }"
                                 x-on:open-img-pages.window="open = true; pages = $event.detail.pages; imgUrl = $event.detail.imgUrl"
                                 x-on:keydown.escape.window="open = false">

                                {{-- Modal --}}
                                <div x-show="open" x-cloak class="fixed inset-0 z-50"
                                     role="dialog" aria-modal="true">
                                    {{-- Backdrop --}}
                                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         @click="open = false"></div>
                                    {{-- Panel --}}
                                    <div class="absolute inset-0 overflow-y-auto p-4">
                                        <div class="flex min-h-full items-center justify-center">
                                            <div class="relative w-full max-w-2xl overflow-hidden rounded-xl border border-(--border-default) bg-(--surface-card) shadow-xl"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave="transition ease-in duration-150"
                                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                                                 @click.stop>
                                                {{-- Header --}}
                                                <div class="flex items-center justify-between gap-3 border-b border-(--border-subtle) px-6 py-4">
                                                    <div class="min-w-0">
                                                        <div class="text-base font-semibold text-(--text-primary)">Páginas que usam esta imagem</div>
                                                        <div class="mt-1 truncate text-sm text-(--text-secondary)" x-text="imgUrl"></div>
                                                    </div>
                                                    <button type="button" @click="open = false"
                                                            class="inline-flex h-8 w-8 items-center justify-center rounded-md text-(--text-secondary) transition-colors hover:bg-(--surface-hover) hover:text-(--text-primary)">
                                                        <iconify-icon icon="iconamoon:sign-times-light" class="text-xl"></iconify-icon>
                                                    </button>
                                                </div>
                                                {{-- Body --}}
                                                <div class="max-h-[60vh] overflow-y-auto px-6 py-5">
                                                    <div class="divide-y divide-(--border-subtle)">
                                                        <template x-for="pg in pages" :key="pg.url">
                                                            <div class="flex items-center gap-3 py-3">
                                                                <iconify-icon icon="solar:link-linear" class="shrink-0 text-(--text-muted)"></iconify-icon>
                                                                <a :href="pg.url" target="_blank" rel="noopener"
                                                                   class="min-w-0 flex-1 truncate text-sm text-blue-600 hover:underline"
                                                                   x-text="pg.title || pg.url"
                                                                   :title="pg.url"></a>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                                {{-- Footer --}}
                                                <div class="flex items-center justify-end gap-3 border-t border-(--border-subtle) bg-(--surface-page) px-6 py-4">
                                                    <x-ds::button variant="secondary" @click="open = false">Fechar</x-ds::button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="overflow-x-auto rounded-lg border border-(--border-subtle)">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-(--surface-hover) text-xs text-(--text-secondary)">
                                            <tr>
                                                <th class="px-3 py-2">ID</th>
                                                <th class="px-3 py-2">URL</th>
                                                <th class="px-3 py-2">Ext</th>
                                                <th class="px-3 py-2">Dimensões</th>
                                                <th class="px-3 py-2">Peso</th>
                                                <th class="px-3 py-2">ALT</th>
                                                <th class="px-3 py-2">Páginas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($images as $img)
                                                @php
                                                    $ext    = strtolower((string) ($img['ext'] ?? ''));
                                                    $alt    = trim((string) ($img['alt_text'] ?? ''));
                                                    $bytes  = $img['filesize'] ?? null;
                                                    $imgW   = $img['width'] ?? null;
                                                    $imgH   = $img['height'] ?? null;
                                                    $imgUrl = (string) ($img['url'] ?? '');
                                                    $imgPages = $imagePageMap[$imgUrl] ?? [];
                                                    $rowCls = (!$alt || $ext !== 'webp' || ($bytes !== null && $bytes > 500000)) ? 'bg-orange-50/30' : '';
                                                @endphp
                                                <tr class="border-t border-(--border-subtle) {{ $rowCls }}">
                                                    <td class="px-3 py-2 text-xs text-(--text-muted)">{{ $img['id'] ?? '—' }}</td>
                                                    <td class="px-3 py-2 max-w-[200px]">
                                                        <a href="{{ $imgUrl }}" target="_blank" rel="noopener"
                                                           class="block truncate text-xs text-blue-600 hover:underline" title="{{ $imgUrl }}">
                                                            {{ $imgUrl ?: '—' }}
                                                        </a>
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <x-ds::badge variant="{{ $ext === 'webp' ? 'success' : 'warning' }}">{{ strtoupper($ext) ?: '?' }}</x-ds::badge>
                                                    </td>
                                                    <td class="px-3 py-2 text-xs text-(--text-secondary) whitespace-nowrap">
                                                        @if($imgW && $imgH)
                                                            {{ $imgW }}×{{ $imgH }}
                                                        @else
                                                            <span class="text-(--text-muted)">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        @if($bytes !== null)
                                                            <x-ds::badge variant="{{ $bytesVariant($bytes) }}">{{ $formatBytes($bytes) }}</x-ds::badge>
                                                        @else
                                                            <span class="text-xs text-(--text-muted)">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        @if($alt)
                                                            <span class="block max-w-[160px] truncate text-xs text-(--text-secondary)" title="{{ $alt }}">{{ $alt }}</span>
                                                        @else
                                                            <x-ds::badge variant="danger">Sem alt</x-ds::badge>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        @if(!empty($imgPages))
                                                            <button type="button"
                                                                    @click="$dispatch('open-img-pages', { pages: @js($imgPages), imgUrl: @js($imgUrl) })"
                                                                    class="inline-flex items-center gap-1 rounded border border-(--border-subtle) bg-(--surface-hover) px-2 py-1 text-[11px] font-medium text-(--text-secondary) hover:text-(--text-primary)">
                                                                <iconify-icon icon="solar:document-text-linear" class="text-xs"></iconify-icon>
                                                                {{ count($imgPages) }}
                                                            </button>
                                                        @else
                                                            <span class="text-xs text-(--text-muted)">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif($wiStatus === 'done')
                            <div class="py-4 text-center text-sm text-(--text-muted)">Nenhuma imagem encontrada.</div>
                        @endif
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

    {{-- ── WP Páginas SEO ── --}}
    @if(isset($moduleStatus['wp_pages']))
        @php
            $wpStatus  = $moduleStatus['wp_pages'];
            $wpOpenVal = $moduleExpanded['wp_pages'] ?? true;
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($wpOpenVal) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:document-text-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">Páginas SEO</span>
                        @if($wpStatus !== 'running' && !empty($pages))
                            <x-ds::badge variant="secondary">{{ count($pages) }} páginas</x-ds::badge>
                            @if($pagesWithoutMetaDescription > 0) <x-ds::badge variant="warning">{{ $pagesWithoutMetaDescription }} sem meta</x-ds::badge> @endif
                            @if($pagesWithoutH1 > 0) <x-ds::badge variant="danger">{{ $pagesWithoutH1 }} sem H1</x-ds::badge> @endif
                        @endif
                        @if($wpStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($wpStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-5 space-y-4">
                    @if($wpStatus === 'running')
                        <x-ds::spinner label="Auditando páginas..." />
                    @else
                        {{-- Summary counters --}}
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                            @foreach([
                                ['Sem meta desc.',  $pagesWithoutMetaDescription,  'warning'],
                                ['Sem H1',          $pagesWithoutH1,               'danger'],
                                ['Sem title tag',   $pagesWithoutTitleTag,         'danger'],
                                ['Desc. dup.',      $pagesDuplicateDescription,    'warning'],
                            ] as [$lbl, $cnt, $v])
                                @php $bv = $cnt > 0 ? $v : 'secondary'; @endphp
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3 text-center">
                                    <div class="text-xs text-(--text-muted)">{{ $lbl }}</div>
                                    <div class="mt-1 text-xl font-bold {{ $cnt > 0 ? 'text-(--status-' . $v . ')' : 'text-(--text-secondary)' }}">{{ $cnt }}</div>
                                </div>
                            @endforeach
                        </div>

                        @if(!empty($pages))
                            <div class="overflow-x-auto rounded-lg border border-(--border-subtle)">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-(--surface-hover) text-xs text-(--text-secondary)">
                                        <tr>
                                            <th class="px-3 py-2">Página</th>
                                            <th class="px-3 py-2">Title</th>
                                            <th class="px-3 py-2">Meta Desc.</th>
                                            <th class="px-3 py-2">H1</th>
                                            <th class="px-3 py-2">Canonical</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pages as $page)
                                            @php $htmlFetched = (bool) ($page['html_fetched'] ?? false); @endphp
                                            <tr class="border-t border-(--border-subtle) hover:bg-(--surface-hover)">
                                                <td class="px-3 py-2 max-w-[160px]">
                                                    @if(!empty($page['url']))
                                                        <a href="{{ $page['url'] }}" target="_blank" rel="noopener"
                                                           class="block truncate text-xs font-medium text-blue-600 hover:underline" title="{{ $page['url'] }}">{{ $page['title'] ?? $page['url'] }}</a>
                                                    @else
                                                        <span class="block truncate text-xs font-medium text-(--text-primary)">{{ $page['title'] ?? '—' }}</span>
                                                    @endif
                                                    <span class="block truncate text-[10px] text-(--text-muted)">{{ $page['url'] ?? '' }}</span>
                                                </td>
                                                <td class="px-3 py-2">
                                                    @php $t = $page['title_tag'] ?? null; @endphp
                                                    @if($t)
                                                        <span class="block max-w-[140px] truncate text-xs text-(--text-secondary)" title="{{ $t }}">{{ $t }}</span>
                                                    @else
                                                        <x-ds::badge variant="danger">Sem title</x-ds::badge>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2">
                                                    @php $md = $page['meta_description'] ?? null; $mdLen = $md ? mb_strlen(trim($md)) : 0; @endphp
                                                    @if($md)
                                                        @php $mdV = $mdLen > 160 ? 'danger' : ($mdLen < 50 ? 'warning' : 'success'); @endphp
                                                        <x-ds::badge variant="{{ $mdV }}">{{ $mdLen }}c</x-ds::badge>
                                                    @else
                                                        <x-ds::badge variant="danger">Sem meta</x-ds::badge>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2">
                                                    @if(!$htmlFetched)
                                                        <span class="text-xs text-(--text-muted)">—</span>
                                                    @else
                                                        @php $h1 = $page['h1'] ?? null; @endphp
                                                        @if($h1)
                                                            <span class="block max-w-[120px] truncate text-xs text-(--text-secondary)" title="{{ $h1 }}">{{ $h1 }}</span>
                                                        @else
                                                            <x-ds::badge variant="danger">Sem H1</x-ds::badge>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2">
                                                    @php $can = $page['canonical'] ?? null; @endphp
                                                    @if($can)
                                                        <x-ds::badge variant="success">OK</x-ds::badge>
                                                    @else
                                                        <x-ds::badge variant="warning">Sem canonical</x-ds::badge>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($wpStatus === 'done')
                            <div class="py-4 text-center text-sm text-(--text-muted)">Nenhuma página encontrada.</div>
                        @endif
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

    {{-- ── WP Segurança ── --}}
    @if(isset($moduleStatus['wp_security']))
        @php
            $wsStatus  = $moduleStatus['wp_security'];
            $wsOpenVal = $moduleExpanded['wp_security'] ?? true;
            $wsVariant = $wpSecurityScore >= 80 ? 'success' : ($wpSecurityScore >= 50 ? 'warning' : 'danger');
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($wsOpenVal) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2">
                        <iconify-icon icon="solar:shield-warning-linear" class="text-lg text-(--text-muted)"></iconify-icon>
                        <span class="font-medium text-(--text-primary)">Segurança WordPress</span>
                        @if($wsStatus !== 'running' && !empty($wpSecurityChecks))
                            <x-ds::badge variant="{{ $wsVariant }}">Score {{ $wpSecurityScore }}/100</x-ds::badge>
                        @endif
                        @if($wsStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($wsStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-5 space-y-4">
                    @if($wsStatus === 'running')
                        <x-ds::spinner label="Verificando segurança WordPress..." />
                    @else
                        {{-- General WP info --}}
                        @if($general)
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3">
                                    <div class="text-xs text-(--text-muted)">Versão WP</div>
                                    <div class="mt-1 text-sm font-medium text-(--text-primary)">{{ $general['wp_version'] ?? '—' }}</div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3">
                                    <div class="text-xs text-(--text-muted)">SSL</div>
                                    <div class="mt-1">
                                        <x-ds::badge variant="{{ $sslEnabled ? 'success' : 'danger' }}">{{ $sslEnabled ? 'Ativo' : 'Sem SSL' }}</x-ds::badge>
                                    </div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3">
                                    <div class="text-xs text-(--text-muted)">Plugins inativos</div>
                                    <div class="mt-1">
                                        <x-ds::badge variant="{{ $inactivePlugins > 0 ? 'warning' : 'success' }}">{{ $inactivePlugins }}</x-ds::badge>
                                    </div>
                                </div>
                                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3">
                                    <div class="text-xs text-(--text-muted)">Tema ativo</div>
                                    <div class="mt-1 truncate text-xs text-(--text-secondary)">{{ $general['active_theme'] ?? '—' }}</div>
                                </div>
                            </div>
                        @endif

                        {{-- Security checks --}}
                        @if(!empty($wpSecurityChecks))
                            <div class="space-y-2">
                                @foreach($wpSecurityChecks as $check)
                                    @php
                                        $chStatus = $check['status'] ?? 'pass';
                                        $chIcon   = match($chStatus) {
                                            'pass' => 'solar:check-circle-linear',
                                            'warn' => 'solar:danger-triangle-linear',
                                            default => 'solar:close-circle-linear',
                                        };
                                        $chColor  = match($chStatus) {
                                            'pass' => 'text-(--status-success)',
                                            'warn' => 'text-(--status-warning)',
                                            default => 'text-(--status-danger)',
                                        };
                                        $sevVariant = match($check['severity'] ?? 'low') {
                                            'critical' => 'danger',
                                            'high'     => 'danger',
                                            'medium'   => 'warning',
                                            default    => 'secondary',
                                        };
                                    @endphp
                                    <div class="flex items-start gap-3 rounded-lg border border-(--border-subtle) bg-(--surface-card) px-4 py-3">
                                        <iconify-icon icon="{{ $chIcon }}" class="mt-0.5 shrink-0 text-base {{ $chColor }}"></iconify-icon>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium text-(--text-primary)">{{ $check['name'] ?? '—' }}</span>
                                                <x-ds::badge variant="{{ $sevVariant }}" size="xs">{{ $check['severity'] ?? '' }}</x-ds::badge>
                                            </div>
                                            @if(!empty($check['value']))
                                                <div class="mt-0.5 text-xs text-(--text-secondary)">{{ $check['value'] }}</div>
                                            @endif
                                            @if($chStatus !== 'pass' && !empty($check['tip']))
                                                <div class="mt-1 text-xs text-(--text-muted)">💡 {{ $check['tip'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($wsStatus === 'done')
                            <div class="py-4 text-center text-sm text-(--text-muted)">Nenhum dado disponível.</div>
                        @endif
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

</div>
