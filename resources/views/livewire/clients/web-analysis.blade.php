<div
    class="space-y-5"
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
        $scoreCssVar = function ($score): string {
            if ($score === null) return 'var(--border-subtle)';
            $p = (float) $score * 100;
            return $p >= 90 ? 'var(--status-success)' : ($p >= 50 ? 'var(--status-warning)' : 'var(--status-danger)');
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
        $cwvCssVar = function (string $metric, ?float $value) use ($cwvVariant): string {
            return match($cwvVariant($metric, $value)) {
                'success' => 'var(--status-success)',
                'warning' => 'var(--status-warning)',
                'danger'  => 'var(--status-danger)',
                default   => 'var(--border-subtle)',
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
        /* ── Score ring SVG helper ── */
        $scoreRing = function (int|null $score, string $variant): string {
            $pct  = $score ?? 0;
            $color = match($variant) {
                'success' => 'var(--status-success)',
                'warning' => 'var(--status-warning)',
                'danger'  => 'var(--status-danger)',
                default   => 'var(--border-subtle)',
            };
            return '<svg class="h-full w-full -rotate-90" viewBox="0 0 36 36">'
                 . '<circle cx="18" cy="18" r="15.9155" fill="none" stroke="var(--border-subtle)" stroke-width="2.5"/>'
                 . '<circle cx="18" cy="18" r="15.9155" fill="none" stroke="' . $color . '" stroke-width="2.5"'
                 . ' stroke-dasharray="' . $pct . ' 100" stroke-linecap="round"/>'
                 . '</svg>';
        };
        $defs = $this->moduleDefs();
    @endphp

    {{-- ── Header ── --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                <iconify-icon icon="solar:chart-2-linear" class="text-xl text-(--color-primary)"></iconify-icon>
            </div>
            <div>
                <div class="text-[10px] uppercase tracking-wider text-(--text-muted)">Análise do site</div>
                <div class="text-lg font-semibold text-(--text-primary)">{{ $web?->name ?: '—' }}</div>
                @if($web?->url)
                    <div class="mt-0.5 text-xs">
                        <a class="text-(--color-primary) hover:underline" href="{{ str_starts_with($web->url, 'http') ? $web->url : 'https://' . $web->url }}" target="_blank" rel="noopener noreferrer">{{ $web->url }}</a>
                    </div>
                @endif
            </div>
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
                <label class="flex items-start gap-2.5 rounded-xl border p-3.5 transition-colors {{ $cardCls }}">
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
                <span class="flex items-center gap-1.5 rounded-full bg-(--surface-hover) px-3 py-1 text-xs text-(--text-muted)">
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
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:chart-2-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">PageSpeed</span>
                        @if($overallScoreVal !== null)
                            <x-ds::badge variant="{{ $overallVariant }}">{{ $overallScoreVal }} — {{ $overallLabel }}</x-ds::badge>
                        @endif
                        @if($psStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($psStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>

                <div x-show="open" x-cloak class="mt-6 space-y-6">
                    @if($psStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Analisando PageSpeed...</span>
                        </div>
                    @else
                        {{-- Summary row --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            {{-- Score ring --}}
                            <div class="flex items-center gap-4 rounded-2xl border border-(--border-subtle) bg-(--surface-hover) p-5">
                                <div class="relative h-20 w-20 shrink-0">
                                    {!! $scoreRing($overallScoreVal, $overallVariant) !!}
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-2xl font-bold text-(--text-primary)">{{ $overallScoreVal ?? '—' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-(--text-muted)">Score geral</div>
                                    <div class="mt-1 text-lg font-bold text-(--text-primary)">{{ $overallLabel }}</div>
                                    <div class="mt-0.5 text-xs text-(--text-muted)">Média das 4 categorias</div>
                                </div>
                            </div>
                            <div class="flex flex-col justify-center rounded-2xl border border-(--border-subtle) bg-(--surface-hover) p-5">
                                <div class="text-xs text-(--text-muted)">Última análise</div>
                                <div class="mt-1 text-lg font-bold text-(--text-primary)">{{ $pageSpeedLastRunAt ?: '—' }}</div>
                                <div class="mt-1 text-xs text-(--text-muted)">
                                    {{ config('services.pagespeed.key') ? 'API Key configurada ✓' : 'Sem API Key (quota limitada)' }}
                                </div>
                            </div>
                            <div class="flex flex-col justify-center rounded-2xl border border-(--border-subtle) bg-(--surface-hover) p-5">
                                <div class="text-xs text-(--text-muted)">Histórico</div>
                                <div class="mt-1 text-3xl font-bold text-(--text-primary)">{{ count($history) }}</div>
                                <div class="mt-1 text-xs text-(--text-muted)">análises registradas</div>
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
                            <div class="rounded-2xl border border-(--border-subtle) bg-(--surface-hover) p-4"
                                x-data="{
                                    chart: null,
                                    series: {{ Js::from($chartSeries) }},
                                    labels: {{ Js::from($chartLabels) }},
                                    isDark: document.documentElement.classList.contains('dark'),
                                    colors: ['#7c3aed','#a78bfa','#10b981','#6ee7b7','#f59e0b','#3b82f6'],
                                    init() {
                                        this.renderChart();
                                        window.addEventListener('livewire:morph', () => {
                                            this.isDark = document.documentElement.classList.contains('dark');
                                            this.chart?.destroy(); this.renderChart();
                                        });
                                    },
                                    renderChart() {
                                        const options = {
                                            chart: { type: 'line', height: 240, toolbar: { show: false }, zoom: { enabled: false }, background: 'transparent', animations: { enabled: true, speed: 400 } },
                                            theme: { mode: this.isDark ? 'dark' : 'light' },
                                            series: this.series,
                                            xaxis: { categories: this.labels, labels: { style: { fontSize: '11px' } } },
                                            yaxis: { min: 0, max: 100, tickAmount: 5, labels: { style: { fontSize: '11px' }, formatter: (v) => v + '' } },
                                            colors: this.colors,
                                            stroke: { curve: 'smooth', width: 2 },
                                            markers: { size: this.series[0].data.length <= 10 ? 4 : 0 },
                                            legend: { position: 'top', fontSize: '12px', horizontalAlign: 'left' },
                                            grid: { borderColor: this.isDark ? '#2A2440' : '#e4e0f0', strokeDashArray: 4 },
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
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                @foreach(['performance' => 'Performance', 'seo' => 'SEO', 'accessibility' => 'Acessib.', 'best_practices' => 'Boas práticas'] as $field => $catLabel)
                                    @php
                                        $val = $web->$field;
                                        $v   = $val === null ? 'secondary' : ($val >= 90 ? 'success' : ($val >= 50 ? 'warning' : 'danger'));
                                        $pct = $val ?? 0;
                                    @endphp
                                    <div class="flex flex-col items-center gap-3 rounded-2xl border border-(--border-subtle) bg-(--surface-hover) p-5">
                                        <div class="relative h-16 w-16">
                                            {!! $scoreRing($pct, $v) !!}
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="text-xl font-bold text-(--text-primary)">{{ $val ?? '—' }}</span>
                                            </div>
                                        </div>
                                        <div class="text-xs font-medium text-(--text-muted)">{{ $catLabel }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Mobile + Desktop side by side --}}
                        @if($pageSpeed)
                            <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
                                @foreach(['mobile' => ['Mobile', 'solar:smartphone-linear'], 'desktop' => ['Desktop', 'solar:monitor-linear']] as $strategyKey => [$strategyLabel, $strategyIcon])
                                    @php
                                        $sd   = $pageSpeed[$strategyKey] ?? null;
                                        $serr = $pageSpeedErrors[$strategyKey] ?? null;
                                        $opps = $sd['opportunities'] ?? [];
                                        $mainScore = $sd ? (int) round((float) ($sd['scores']['performance'] ?? 0) * 100) : null;
                                        $mainVariant = $mainScore !== null ? ($mainScore >= 90 ? 'success' : ($mainScore >= 50 ? 'warning' : 'danger')) : 'secondary';
                                    @endphp
                                    @if($sd || $serr)
                                        <div class="rounded-2xl border border-(--border-subtle) bg-(--surface-card) overflow-hidden">
                                            {{-- Panel header --}}
                                            <div class="flex items-center gap-3 border-b border-(--border-subtle) bg-(--surface-hover) px-5 py-3">
                                                <iconify-icon icon="{{ $strategyIcon }}" class="text-base text-(--color-primary)"></iconify-icon>
                                                <span class="font-semibold text-(--text-primary)">{{ $strategyLabel }}</span>
                                            </div>
                                            <div class="space-y-5 p-5">
                                                @if($serr)
                                                    <x-ds::alert variant="danger" icon="solar:danger-circle-linear">{{ $serr }}</x-ds::alert>
                                                @endif
                                                @if($sd)
                                                    {{-- 4 scores + main ring --}}
                                                    <div class="flex items-center gap-4">
                                                        <div class="relative h-20 w-20 shrink-0">
                                                            {!! $scoreRing($mainScore, $mainVariant) !!}
                                                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                                                <span class="text-xl font-bold text-(--text-primary)">{{ $mainScore ?? '—' }}</span>
                                                                <span class="text-[9px] text-(--text-muted)">Perf</span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-1 grid grid-cols-3 gap-2">
                                                            @foreach(['seo' => 'SEO', 'accessibility' => 'A11y', 'best_practices' => 'BP'] as $sk => $cl)
                                                                @php
                                                                    $sv  = $sd['scores'][$sk] ?? null;
                                                                    $pct2 = $sv !== null ? (int) round((float) $sv * 100) : null;
                                                                    $v2  = $sv !== null ? ($pct2 >= 90 ? 'success' : ($pct2 >= 50 ? 'warning' : 'danger')) : 'secondary';
                                                                @endphp
                                                                <div class="flex flex-col items-center gap-1.5 rounded-xl border border-(--border-subtle) p-2.5">
                                                                    <div class="text-lg font-bold" style="color: {{ $pct2 !== null ? ($pct2 >= 90 ? 'var(--status-success)' : ($pct2 >= 50 ? 'var(--status-warning)' : 'var(--status-danger)')) : 'var(--text-muted)' }}">{{ $pct2 ?? '—' }}</div>
                                                                    <div class="text-[9px] text-(--text-muted)">{{ $cl }}</div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- CWV as horizontal bars --}}
                                                    @php
                                                        $cwvItems = [
                                                            ['label' => 'FCP',  'metric' => 'fcp',         'raw' => $sd['metrics']['fcp_ms'] ?? null,         'display' => $sd['display']['fcp'] ?? null,         'fmt' => 'sec'],
                                                            ['label' => 'LCP',  'metric' => 'lcp',         'raw' => $sd['metrics']['lcp_ms'] ?? null,         'display' => $sd['display']['lcp'] ?? null,         'fmt' => 'sec'],
                                                            ['label' => 'TBT',  'metric' => 'tbt',         'raw' => $sd['metrics']['tbt_ms'] ?? null,         'display' => $sd['display']['tbt'] ?? null,         'fmt' => 'ms'],
                                                            ['label' => 'CLS',  'metric' => 'cls',         'raw' => $sd['metrics']['cls'] ?? null,            'display' => $sd['display']['cls'] ?? null,         'fmt' => 'cls'],
                                                            ['label' => 'TTFB', 'metric' => 'ttfb',        'raw' => $sd['metrics']['ttfb_ms'] ?? null,        'display' => $sd['display']['ttfb'] ?? null,        'fmt' => 'ms'],
                                                            ['label' => 'SI',   'metric' => 'speed_index', 'raw' => $sd['metrics']['speed_index_ms'] ?? null, 'display' => $sd['display']['speed_index'] ?? null, 'fmt' => 'sec'],
                                                            ['label' => 'INP',  'metric' => 'inp',         'raw' => $sd['metrics']['inp_ms'] ?? null,         'display' => $sd['display']['inp'] ?? null,         'fmt' => 'ms'],
                                                        ];
                                                        $cwvThresholds = ['fcp' => 3000, 'lcp' => 4000, 'tbt' => 600, 'cls' => 0.25, 'ttfb' => 1800, 'speed_index' => 5800, 'inp' => 500];
                                                    @endphp
                                                    <div class="space-y-2.5">
                                                        <div class="text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">Core Web Vitals</div>
                                                        @foreach($cwvItems as $cwv)
                                                            @php
                                                                $rawVal  = $cwv['raw'];
                                                                $dispVal = $cwv['display'] ?? match($cwv['fmt']) { 'sec' => $sec($rawVal), 'ms' => $ms($rawVal), 'cls' => $cls($rawVal), default => '—' };
                                                                $threshold = $cwvThresholds[$cwv['metric']] ?? 1;
                                                                $barPct = $rawVal !== null && $threshold > 0 ? min(100, round(($rawVal / $threshold) * 100)) : 0;
                                                                $cssColor = $cwvCssVar($cwv['metric'], $rawVal);
                                                            @endphp
                                                            <div class="flex items-center gap-3">
                                                                <span class="w-9 shrink-0 text-[10px] font-semibold text-(--text-muted)">{{ $cwv['label'] }}</span>
                                                                <div class="flex-1 overflow-hidden rounded-full h-1.5 bg-(--border-subtle)">
                                                                    <div class="h-full rounded-full transition-all" style="width: {{ $barPct }}%; background-color: {{ $cssColor }}"></div>
                                                                </div>
                                                                <span class="w-14 text-right text-xs font-mono font-semibold {{ $cwvColor($cwv['metric'], $rawVal) }}">{{ $dispVal }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    {{-- Opportunities --}}
                                                    @if(!empty($opps))
                                                        <div class="space-y-1.5">
                                                            <div class="text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">Oportunidades ({{ count($opps) }})</div>
                                                            @foreach(array_slice($opps, 0, 6) as $opp)
                                                                @php $savings = $opp['savings_ms'] ?? null; @endphp
                                                                <div class="flex items-center justify-between gap-2 rounded-lg border-l-2 border-l-(--status-warning) bg-(--surface-hover) px-3 py-2 text-xs">
                                                                    <span class="text-(--text-secondary) leading-snug">{{ $opp['title'] ?? '—' }}</span>
                                                                    @if($savings)
                                                                        <span class="shrink-0 rounded bg-(--status-warning)/10 px-1.5 py-0.5 font-mono text-[10px] font-semibold text-(--status-warning)">
                                                                            {{ $savings >= 1000 ? round($savings / 1000, 1) . 's' : $savings . 'ms' }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
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
            $shStatus  = $moduleStatus['security_headers'];
            $shOpen    = $moduleExpanded['security_headers'] ?? true;
            $shVariant = $securityScore >= 80 ? 'success' : ($securityScore >= 50 ? 'warning' : 'danger');
            $shPassed  = array_filter($securityChecks, fn($c) => $c['passed'] ?? false);
            $shFailed  = array_filter($securityChecks, fn($c) => !($c['passed'] ?? false));
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($shOpen) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:shield-check-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">Headers de Segurança</span>
                        @if($shStatus !== 'running' && !empty($securityChecks))
                            <x-ds::badge variant="{{ $shVariant }}">{{ $securityScore }}/100</x-ds::badge>
                            @if(count($shFailed) > 0)
                                <x-ds::badge variant="danger">{{ count($shFailed) }} problema(s)</x-ds::badge>
                            @else
                                <x-ds::badge variant="success">Tudo OK</x-ds::badge>
                            @endif
                        @endif
                        @if($shStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($shStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-6 space-y-5">
                    @if($shStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Verificando headers...</span>
                        </div>
                    @elseif(!empty($securityChecks))
                        {{-- Score bar --}}
                        <div class="rounded-2xl border border-(--border-subtle) bg-(--surface-hover) p-5">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-semibold text-(--text-primary)">Pontuação de segurança</div>
                                    <div class="text-xs text-(--text-muted)">{{ count($shPassed) }} de {{ count($securityChecks) }} headers configurados</div>
                                </div>
                                <div class="text-3xl font-bold" style="color: var(--status-{{ $shVariant }})">{{ $securityScore }}<span class="text-base font-normal text-(--text-muted)">/100</span></div>
                            </div>
                            <div class="h-2.5 w-full overflow-hidden rounded-full bg-(--border-subtle)">
                                <div class="h-full rounded-full transition-all" style="width: {{ $securityScore }}%; background-color: var(--status-{{ $shVariant }})"></div>
                            </div>
                        </div>

                        {{-- Problems first --}}
                        @if(!empty($shFailed))
                            <div>
                                <div class="mb-3 flex items-center gap-2">
                                    <div class="h-3.5 w-0.5 rounded-full bg-(--status-danger)"></div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-(--status-danger)">Problemas encontrados ({{ count($shFailed) }})</span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($shFailed as $check)
                                        @php $sevVariant = match(strtolower($check['severity'] ?? '')) { 'critical' => 'danger', 'high' => 'warning', default => 'secondary' }; @endphp
                                        <div class="flex items-start gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4" style="border-left: 3px solid var(--status-error)">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg" style="background-color: color-mix(in srgb, var(--status-error) 12%, transparent)">
                                                <iconify-icon icon="solar:close-circle-bold" class="text-base text-(--status-danger)"></iconify-icon>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="font-semibold text-(--text-primary)">{{ $check['header'] ?? '—' }}</span>
                                                    <x-ds::badge variant="{{ $sevVariant }}">{{ $check['severity'] ?? 'info' }}</x-ds::badge>
                                                </div>
                                                @if(!empty($check['description']))
                                                    <div class="mt-1 text-xs text-(--text-secondary)">{{ $check['description'] }}</div>
                                                @endif
                                                @if(!empty($check['value']))
                                                    <code class="mt-1 block truncate text-xs text-(--text-muted)">{{ $check['value'] }}</code>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Passed --}}
                        @if(!empty($shPassed))
                            <div>
                                <div class="mb-3 flex items-center gap-2">
                                    <div class="h-3.5 w-0.5 rounded-full bg-(--status-success)"></div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-(--status-success)">Configurados corretamente ({{ count($shPassed) }})</span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($shPassed as $check)
                                        <div class="flex items-start gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4" style="border-left: 3px solid var(--status-success)">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg" style="background-color: color-mix(in srgb, var(--status-success) 12%, transparent)">
                                                <iconify-icon icon="solar:check-circle-bold" class="text-base text-(--status-success)"></iconify-icon>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="font-semibold text-(--text-primary)">{{ $check['header'] ?? '—' }}</div>
                                                @if(!empty($check['value']))
                                                    <code class="mt-0.5 block truncate text-xs text-(--text-muted)">{{ $check['value'] }}</code>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @elseif($shStatus === 'done')
                        <div class="flex flex-col items-center gap-2 py-10 text-center">
                            <iconify-icon icon="solar:inbox-line-linear" class="text-4xl text-(--text-muted)"></iconify-icon>
                            <div class="text-sm text-(--text-muted)">Nenhum dado disponível.</div>
                        </div>
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
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:code-square-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">Schema Markup & Open Graph</span>
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
                <div x-show="open" x-cloak class="mt-6 space-y-6">
                    @if($scStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Detectando schemas...</span>
                        </div>
                    @else
                        {{-- Schema.org --}}
                        <div>
                            <div class="mb-3 flex items-center gap-2">
                                <div class="h-3.5 w-0.5 rounded-full bg-(--color-primary)"></div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-(--text-muted)">JSON-LD / Schema.org</span>
                            </div>
                            @if(!empty($schemaItems))
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    @foreach($schemaItems as $item)
                                        @php $valid = $item['valid'] ?? false; @endphp
                                        <div class="flex items-start gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4" style="border-left: 3px solid {{ $valid ? 'var(--status-success)' : 'var(--status-warning)' }}">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg font-bold text-xs" style="background-color: color-mix(in srgb, {{ $valid ? 'var(--status-success)' : 'var(--status-warning)' }} 12%, transparent); color: {{ $valid ? 'var(--status-success)' : 'var(--status-warning)' }}">
                                                <iconify-icon icon="{{ $valid ? 'solar:check-circle-bold' : 'solar:danger-triangle-bold' }}" class="text-base"></iconify-icon>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <code class="font-mono text-sm font-semibold text-(--text-primary)">{{ $item['type'] ?? '—' }}</code>
                                                <div class="mt-1 flex items-center gap-2">
                                                    <x-ds::badge variant="{{ $valid ? 'success' : 'warning' }}">{{ $valid ? 'Válido' : 'Com avisos' }}</x-ds::badge>
                                                </div>
                                                @if(!empty($item['issues']))
                                                    <div class="mt-1 text-xs text-(--text-muted)">{{ implode(' · ', (array) $item['issues']) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex items-center gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-hover) p-4">
                                    <iconify-icon icon="solar:danger-triangle-linear" class="text-xl text-(--status-warning)"></iconify-icon>
                                    <div>
                                        <div class="text-sm font-medium text-(--text-primary)">Nenhum Schema.org encontrado</div>
                                        <div class="text-xs text-(--text-muted)">Adicione JSON-LD ao seu site para melhorar a visibilidade em buscadores.</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- OG + Twitter side by side --}}
                        @php
                            $ogRequired = ['title', 'description', 'image', 'url', 'type'];
                            $ogMissing  = array_filter($ogRequired, fn($k) => empty($ogTags[$k]));
                            $twRequired = ['card', 'title', 'description', 'image'];
                            $twMissing  = array_filter($twRequired, fn($k) => empty($twitterTags[$k]));
                            $twCardType = $twitterTags['card'] ?? null;
                            $twTitle    = $twitterTags['title'] ?? $ogTags['title'] ?? null;
                            $twDesc     = $twitterTags['description'] ?? $ogTags['description'] ?? null;
                            $twImage    = $twitterTags['image'] ?? $ogTags['image'] ?? null;
                            $twSite     = $twitterTags['site'] ?? $ogTags['site_name'] ?? null;
                            $twCreator  = $twitterTags['creator'] ?? null;
                            $twLargeCard = $twCardType === 'summary_large_image' || $twCardType === 'app';
                        @endphp
                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                            {{-- Open Graph --}}
                            <div>
                                <div class="mb-3 flex items-center gap-2">
                                    <div class="h-3.5 w-0.5 rounded-full bg-(--color-primary)"></div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-(--text-muted)">Open Graph</span>
                                    @if(empty($ogTags))
                                        <x-ds::badge variant="danger">Ausente</x-ds::badge>
                                    @elseif(empty($ogMissing))
                                        <x-ds::badge variant="success">Completo</x-ds::badge>
                                    @else
                                        <x-ds::badge variant="warning">{{ count($ogMissing) }} faltando</x-ds::badge>
                                    @endif
                                </div>
                                @if(!empty($ogTags))
                                    <div class="overflow-hidden rounded-xl border border-(--border-subtle) bg-(--surface-hover)">
                                        @if(!empty($ogTags['image']))
                                            <img src="{{ $ogTags['image'] }}" alt="OG Image" class="h-40 w-full object-cover" onerror="this.style.display='none'">
                                        @else
                                            <div class="flex h-28 items-center justify-center bg-(--surface-hover)">
                                                <iconify-icon icon="solar:gallery-minimalistic-linear" class="text-3xl text-(--text-muted)"></iconify-icon>
                                            </div>
                                        @endif
                                        <div class="p-4">
                                            @if(!empty($ogTags['site_name']))
                                                <div class="text-[10px] uppercase tracking-wider text-(--text-muted)">{{ $ogTags['site_name'] }}</div>
                                            @endif
                                            <div class="mt-0.5 text-sm font-semibold text-(--text-primary)">{{ $ogTags['title'] ?? '— sem og:title' }}</div>
                                            @if(!empty($ogTags['description']))
                                                <div class="mt-1 line-clamp-2 text-xs text-(--text-secondary)">{{ $ogTags['description'] }}</div>
                                            @endif
                                            @if(!empty($ogTags['url']))
                                                <div class="mt-2 truncate text-[10px] text-(--text-muted)">{{ $ogTags['url'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-3 grid grid-cols-3 gap-1.5">
                                        @foreach($ogRequired as $req)
                                            @php $present = !empty($ogTags[$req]); @endphp
                                            <div class="flex items-center gap-1.5 rounded-lg border border-(--border-subtle) px-2.5 py-1.5 text-xs {{ $present ? 'bg-(--surface-card)' : 'bg-(--surface-hover)' }}">
                                                <iconify-icon icon="{{ $present ? 'solar:check-circle-bold' : 'solar:close-circle-bold' }}"
                                                              class="shrink-0 text-sm {{ $present ? 'text-(--status-success)' : 'text-(--status-danger)' }}"></iconify-icon>
                                                <span class="truncate text-(--text-secondary)">og:{{ $req }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 space-y-1">
                                        @foreach($ogTags as $tag => $value)
                                            <div class="flex gap-2 rounded-lg bg-(--surface-hover) px-3 py-2 text-xs">
                                                <code class="w-36 shrink-0 text-(--text-muted)">og:{{ $tag }}</code>
                                                @if(in_array($tag, ['image', 'image:secure_url']) && str_starts_with($value, 'http'))
                                                    <a href="{{ $value }}" target="_blank" rel="noopener" class="truncate text-(--color-primary) hover:underline" title="{{ $value }}">{{ $value }}</a>
                                                @else
                                                    <span class="truncate text-(--text-secondary)" title="{{ $value }}">{{ $value }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-hover) p-4">
                                        <iconify-icon icon="solar:danger-triangle-linear" class="text-xl text-(--status-warning)"></iconify-icon>
                                        <div class="text-xs text-(--text-secondary)">Nenhuma tag Open Graph encontrada. Instale um plugin de SEO (Yoast, RankMath) ou adicione manualmente.</div>
                                    </div>
                                @endif
                            </div>

                            {{-- Twitter / X Cards --}}
                            <div>
                                <div class="mb-3 flex items-center gap-2">
                                    <div class="h-3.5 w-0.5 rounded-full bg-(--color-primary)"></div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-(--text-muted)">Twitter / X Cards</span>
                                    @if(empty($twitterTags))
                                        <x-ds::badge variant="warning">Ausente</x-ds::badge>
                                    @elseif(empty($twMissing))
                                        <x-ds::badge variant="success">Completo</x-ds::badge>
                                    @else
                                        <x-ds::badge variant="warning">{{ count($twMissing) }} faltando</x-ds::badge>
                                    @endif
                                </div>
                                @if(!empty($twitterTags))
                                    <div class="overflow-hidden rounded-xl border border-(--border-subtle) bg-(--surface-hover)">
                                        @if($twLargeCard && $twImage)
                                            <img src="{{ $twImage }}" alt="Twitter card image" class="h-40 w-full object-cover" onerror="this.style.display='none'">
                                            <div class="p-4">
                                                @if($twSite) <div class="text-[10px] text-(--text-muted)">{{ $twSite }}</div> @endif
                                                <div class="mt-0.5 text-sm font-bold text-(--text-primary)">{{ $twTitle ?? '— sem título' }}</div>
                                                @if($twDesc) <div class="mt-1 line-clamp-2 text-xs text-(--text-secondary)">{{ $twDesc }}</div> @endif
                                            </div>
                                        @else
                                            <div class="flex">
                                                @if($twImage)
                                                    <img src="{{ $twImage }}" alt="Twitter card" class="h-28 w-28 shrink-0 object-cover" onerror="this.style.display='none'">
                                                @else
                                                    <div class="flex h-28 w-28 shrink-0 items-center justify-center bg-(--surface-card)">
                                                        <iconify-icon icon="solar:gallery-minimalistic-linear" class="text-2xl text-(--text-muted)"></iconify-icon>
                                                    </div>
                                                @endif
                                                <div class="min-w-0 flex-1 p-4">
                                                    @if($twSite) <div class="text-[10px] text-(--text-muted)">{{ $twSite }}</div> @endif
                                                    <div class="mt-0.5 text-sm font-bold text-(--text-primary)">{{ $twTitle ?? '— sem título' }}</div>
                                                    @if($twDesc) <div class="mt-1 line-clamp-2 text-xs text-(--text-secondary)">{{ $twDesc }}</div> @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                        @if($twCardType) <x-ds::badge variant="secondary">{{ $twCardType }}</x-ds::badge> @endif
                                        @if($twCreator) <span class="text-xs text-(--text-muted)">by <span class="text-(--color-primary)">{{ $twCreator }}</span></span> @endif
                                    </div>
                                    <div class="mt-3 grid grid-cols-2 gap-1.5">
                                        @foreach($twRequired as $req)
                                            @php $present = !empty($twitterTags[$req]); @endphp
                                            <div class="flex items-center gap-1.5 rounded-lg border border-(--border-subtle) px-2.5 py-1.5 text-xs {{ $present ? 'bg-(--surface-card)' : 'bg-(--surface-hover)' }}">
                                                <iconify-icon icon="{{ $present ? 'solar:check-circle-bold' : 'solar:close-circle-bold' }}"
                                                              class="shrink-0 text-sm {{ $present ? 'text-(--status-success)' : 'text-(--status-danger)' }}"></iconify-icon>
                                                <span class="truncate text-(--text-secondary)">twitter:{{ $req }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-3 space-y-1">
                                        @foreach($twitterTags as $tag => $value)
                                            <div class="flex gap-2 rounded-lg bg-(--surface-hover) px-3 py-2 text-xs">
                                                <code class="w-36 shrink-0 text-(--text-muted)">twitter:{{ $tag }}</code>
                                                @if($tag === 'image' && str_starts_with($value, 'http'))
                                                    <a href="{{ $value }}" target="_blank" rel="noopener" class="truncate text-(--color-primary) hover:underline" title="{{ $value }}">{{ $value }}</a>
                                                @else
                                                    <span class="truncate text-(--text-secondary)" title="{{ $value }}">{{ $value }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex items-center gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-hover) p-4">
                                        <iconify-icon icon="solar:info-circle-linear" class="text-xl text-(--text-muted)"></iconify-icon>
                                        <div class="text-xs text-(--text-secondary)">Adicione <code class="font-mono">twitter:card</code>, <code class="font-mono">twitter:title</code>, <code class="font-mono">twitter:description</code> e <code class="font-mono">twitter:image</code>.</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if(empty($schemaItems) && empty($ogTags) && empty($twitterTags) && $scStatus === 'done')
                            <div class="flex flex-col items-center gap-2 py-10 text-center">
                                <iconify-icon icon="solar:inbox-line-linear" class="text-4xl text-(--text-muted)"></iconify-icon>
                                <div class="text-sm text-(--text-muted)">Nenhum dado encontrado.</div>
                            </div>
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
            $geoPassed  = array_filter($geoChecks, fn($c) => ($c['status'] ?? 'fail') === 'pass');
            $geoWarn    = array_filter($geoChecks, fn($c) => ($c['status'] ?? 'fail') === 'warn');
            $geoFailed  = array_filter($geoChecks, fn($c) => ($c['status'] ?? 'fail') === 'fail');
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($geoOpenVal) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:magic-stick-3-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">GEO · Generative Engine Optimization</span>
                        @if($geoStatus !== 'running' && !empty($geoChecks))
                            <x-ds::badge variant="{{ $geoVariant }}">{{ $geoScore }}/100</x-ds::badge>
                        @endif
                        @if($geoStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($geoStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-6 space-y-5">
                    @if($geoStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Analisando sinais GEO...</span>
                        </div>
                    @elseif(!empty($geoChecks))
                        {{-- Score panel --}}
                        <div class="flex items-center gap-5 rounded-2xl border border-(--border-subtle) bg-(--surface-hover) p-5">
                            <div class="relative h-20 w-20 shrink-0">
                                {!! $scoreRing($geoScore, $geoVariant) !!}
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-bold text-(--text-primary)">{{ $geoScore }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="mb-2 flex items-center justify-between text-sm">
                                    <span class="font-medium text-(--text-primary)">Score GEO/IA</span>
                                    <span class="text-xs text-(--text-muted)">{{ count($geoPassed) }} OK · {{ count($geoWarn) }} avisos · {{ count($geoFailed) }} falhas</span>
                                </div>
                                <div class="h-2.5 w-full overflow-hidden rounded-full bg-(--border-subtle)">
                                    <div class="h-full rounded-full" style="width: {{ $geoScore }}%; background-color: var(--status-{{ $geoVariant }})"></div>
                                </div>
                                <div class="mt-2 flex gap-3 text-xs">
                                    <span class="text-(--status-success)">✓ {{ count($geoPassed) }} aprovados</span>
                                    <span class="text-(--status-warning)">⚠ {{ count($geoWarn) }} avisos</span>
                                    <span class="text-(--status-danger)">✗ {{ count($geoFailed) }} falhas</span>
                                </div>
                            </div>
                        </div>

                        {{-- Grouped checks --}}
                        @foreach([
                            ['items' => $geoFailed,  'color' => 'var(--status-error)',   'icon' => 'solar:close-circle-bold',    'label' => 'Falhas'],
                            ['items' => $geoWarn,    'color' => 'var(--status-warning)', 'icon' => 'solar:danger-triangle-bold', 'label' => 'Avisos'],
                            ['items' => $geoPassed,  'color' => 'var(--status-success)', 'icon' => 'solar:check-circle-bold',    'label' => 'Aprovados'],
                        ] as $group)
                            @if(!empty($group['items']))
                                <div class="space-y-2">
                                    @foreach($group['items'] as $check)
                                        <div class="flex items-start gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4" style="border-left: 3px solid {{ $group['color'] }}">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg" style="background-color: color-mix(in srgb, {{ $group['color'] }} 12%, transparent)">
                                                <iconify-icon icon="{{ $group['icon'] }}" class="text-base" style="color: {{ $group['color'] }}"></iconify-icon>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="font-semibold text-(--text-primary)">{{ $check['name'] ?? '—' }}</div>
                                                @if(!empty($check['description']))
                                                    <div class="mt-0.5 text-xs text-(--text-muted)">{{ $check['description'] }}</div>
                                                @endif
                                                @if(!empty($check['value']))
                                                    <div class="mt-1 text-xs text-(--text-secondary)">{{ $check['value'] }}</div>
                                                @endif
                                                @if(($check['status'] ?? 'fail') !== 'pass' && !empty($check['tip']))
                                                    <div class="mt-1.5 flex items-start gap-1 text-xs text-(--text-muted)">
                                                        <iconify-icon icon="solar:lightbulb-linear" class="mt-0.5 shrink-0 text-(--status-warning)"></iconify-icon>
                                                        {{ $check['tip'] }}
                                                    </div>
                                                @endif
                                            </div>
                                            @if(!empty($check['weight']))
                                                <span class="shrink-0 rounded-full bg-(--surface-hover) px-2 py-0.5 text-xs text-(--text-muted)">{{ $check['weight'] }}pts</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    @elseif($geoStatus === 'done')
                        <div class="flex flex-col items-center gap-2 py-10 text-center">
                            <iconify-icon icon="solar:inbox-line-linear" class="text-4xl text-(--text-muted)"></iconify-icon>
                            <div class="text-sm text-(--text-muted)">Nenhum dado disponível.</div>
                        </div>
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
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:map-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">Sitemap & Robots.txt</span>
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
                <div x-show="open" x-cloak class="mt-6">
                    @if($smStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Verificando sitemap e robots...</span>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            {{-- Sitemap --}}
                            <div class="rounded-2xl border border-(--border-subtle) bg-(--surface-hover) overflow-hidden">
                                <div class="flex items-center gap-2.5 border-b border-(--border-subtle) px-5 py-3">
                                    <iconify-icon icon="solar:map-point-linear" class="text-base text-(--color-primary)"></iconify-icon>
                                    <span class="font-semibold text-(--text-primary)">Sitemap.xml</span>
                                    @if($sitemapData)
                                        <x-ds::badge variant="{{ ($sitemapData['found'] ?? false) ? 'success' : 'danger' }}">{{ ($sitemapData['found'] ?? false) ? 'Encontrado' : 'Ausente' }}</x-ds::badge>
                                    @endif
                                </div>
                                @if($sitemapData && ($sitemapData['found'] ?? false))
                                    <div class="divide-y divide-(--border-subtle)">
                                        @foreach([
                                            ['URL', 'link', $sitemapData['url'] ?? null, true],
                                            ['Tipo', null, ($sitemapData['is_index'] ?? false) ? 'Sitemap Index' : 'Sitemap simples', false],
                                            ['URLs indexadas', null, (string) ($sitemapData['url_count'] ?? 0), false],
                                        ] as [$lbl, $type, $val, $isLink])
                                            <div class="flex items-center gap-3 px-5 py-3">
                                                <span class="w-32 shrink-0 text-xs font-medium text-(--text-muted)">{{ $lbl }}</span>
                                                @if($isLink && $val)
                                                    <a href="{{ $val }}" target="_blank" rel="noopener" class="min-w-0 flex-1 truncate text-xs text-(--color-primary) hover:underline">{{ $val }}</a>
                                                @else
                                                    <span class="text-xs text-(--text-secondary)">{{ $val ?? '—' }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        <div class="flex items-center gap-3 px-5 py-3">
                                            <span class="w-32 shrink-0 text-xs font-medium text-(--text-muted)">Lastmod</span>
                                            <x-ds::badge variant="{{ ($sitemapData['has_lastmod'] ?? false) ? 'success' : 'warning' }}">{{ ($sitemapData['has_lastmod'] ?? false) ? 'Presente' : 'Ausente' }}</x-ds::badge>
                                        </div>
                                        <div class="flex items-center gap-3 px-5 py-3">
                                            <span class="w-32 shrink-0 text-xs font-medium text-(--text-muted)">Image sitemap</span>
                                            <x-ds::badge variant="{{ ($sitemapData['has_images'] ?? false) ? 'success' : 'secondary' }}">{{ ($sitemapData['has_images'] ?? false) ? 'Sim' : 'Não' }}</x-ds::badge>
                                        </div>
                                        @if(!empty($sitemapData['sample_urls']))
                                            <div class="px-5 py-3">
                                                <div class="mb-2 text-[10px] uppercase tracking-wider text-(--text-muted)">URLs de amostra</div>
                                                <div class="space-y-1">
                                                    @foreach(array_slice($sitemapData['sample_urls'], 0, 5) as $su)
                                                        <div class="truncate text-xs text-(--text-muted)" title="{{ $su }}">{{ $su }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if(!empty($sitemapData['issues']))
                                        <div class="space-y-1 px-5 py-3">
                                            @foreach($sitemapData['issues'] as $issue)
                                                <x-ds::alert variant="warning">{{ $issue }}</x-ds::alert>
                                            @endforeach
                                        </div>
                                    @endif
                                @elseif($sitemapData)
                                    <div class="flex flex-col items-center gap-2 px-5 py-8 text-center">
                                        <iconify-icon icon="solar:close-circle-linear" class="text-3xl text-(--status-danger)"></iconify-icon>
                                        <div class="text-sm text-(--text-muted)">Sitemap não encontrado</div>
                                    </div>
                                @else
                                    <div class="px-5 py-6 text-sm text-(--text-muted)">—</div>
                                @endif
                            </div>

                            {{-- Robots --}}
                            <div class="rounded-2xl border border-(--border-subtle) bg-(--surface-hover) overflow-hidden">
                                <div class="flex items-center gap-2.5 border-b border-(--border-subtle) px-5 py-3">
                                    <iconify-icon icon="solar:robot-linear" class="text-base text-(--color-primary)"></iconify-icon>
                                    <span class="font-semibold text-(--text-primary)">Robots.txt</span>
                                    @if($robotsData)
                                        <x-ds::badge variant="{{ ($robotsData['found'] ?? false) ? 'success' : 'danger' }}">{{ ($robotsData['found'] ?? false) ? 'Encontrado' : 'Ausente' }}</x-ds::badge>
                                    @endif
                                </div>
                                @if($robotsData && ($robotsData['found'] ?? false))
                                    <div class="divide-y divide-(--border-subtle)">
                                        <div class="flex items-center gap-3 px-5 py-3">
                                            <span class="w-32 shrink-0 text-xs font-medium text-(--text-muted)">Regras totais</span>
                                            <span class="text-xs font-semibold text-(--text-primary)">{{ $robotsData['rules_count'] ?? 0 }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 px-5 py-3">
                                            <span class="w-32 shrink-0 text-xs font-medium text-(--text-muted)">Bloqueia tudo</span>
                                            @php $disAll = $robotsData['disallow_all'] ?? false; @endphp
                                            <x-ds::badge variant="{{ $disAll ? 'danger' : 'success' }}">{{ $disAll ? 'Sim — Disallow: /' : 'Não' }}</x-ds::badge>
                                        </div>
                                        <div class="flex items-start gap-3 px-5 py-3">
                                            <span class="w-32 shrink-0 text-xs font-medium text-(--text-muted)">Sitemap declarado</span>
                                            @if(!empty($robotsData['sitemap_urls']))
                                                <div class="min-w-0 flex-1 space-y-0.5">
                                                    @foreach($robotsData['sitemap_urls'] as $su)
                                                        <a href="{{ $su }}" target="_blank" rel="noopener" class="block truncate text-xs text-(--color-primary) hover:underline" title="{{ $su }}">{{ $su }}</a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <x-ds::badge variant="warning">Não declarado</x-ds::badge>
                                            @endif
                                        </div>
                                    </div>
                                    @if(!empty($robotsData['content']))
                                        <div class="px-5 py-3">
                                            <div class="mb-2 text-[10px] uppercase tracking-wider text-(--text-muted)">Conteúdo do arquivo</div>
                                            <pre class="max-h-40 overflow-y-auto rounded-lg border border-(--border-subtle) bg-(--surface-card) p-3 text-[10px] leading-relaxed text-(--text-secondary)">{{ $robotsData['content'] }}</pre>
                                        </div>
                                    @endif
                                    @if(!empty($robotsData['issues']))
                                        <div class="space-y-1 px-5 py-3">
                                            @foreach($robotsData['issues'] as $issue)
                                                <x-ds::alert variant="warning">{{ $issue }}</x-ds::alert>
                                            @endforeach
                                        </div>
                                    @endif
                                @elseif($robotsData)
                                    <div class="flex flex-col items-center gap-2 px-5 py-8 text-center">
                                        <iconify-icon icon="solar:close-circle-linear" class="text-3xl text-(--status-danger)"></iconify-icon>
                                        <div class="text-sm text-(--text-muted)">Robots.txt não encontrado</div>
                                    </div>
                                @else
                                    <div class="px-5 py-6 text-sm text-(--text-muted)">—</div>
                                @endif
                            </div>
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
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:stars-minimalistic-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">Análise de Conteúdo com IA</span>
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
                <div x-show="open" x-cloak class="mt-6 space-y-5">
                    @if($aiStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Analisando conteúdo com IA...</span>
                        </div>
                    @elseif($aiAnalysis)
                        @php
                            $aiScore   = $aiAnalysis['score_qualidade'] ?? null;
                            $aiVariant = $aiScore !== null ? ($aiScore >= 80 ? 'success' : ($aiScore >= 50 ? 'warning' : 'danger')) : 'secondary';
                        @endphp
                        {{-- Score + meta info --}}
                        <div class="flex flex-col gap-5 sm:flex-row">
                            {{-- Score ring --}}
                            @if($aiScore !== null)
                                <div class="flex shrink-0 flex-col items-center gap-2 rounded-2xl border border-(--border-subtle) bg-(--surface-hover) p-5 sm:w-36">
                                    <div class="relative h-20 w-20">
                                        {!! $scoreRing($aiScore, $aiVariant) !!}
                                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                                            <span class="text-2xl font-bold text-(--text-primary)">{{ $aiScore }}</span>
                                        </div>
                                    </div>
                                    <div class="text-center text-xs text-(--text-muted)">Qualidade de conteúdo</div>
                                </div>
                            @endif
                            {{-- Meta info --}}
                            <div class="flex-1 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach([
                                    ['tom_de_voz', 'Tom de voz', 'solar:voice-linear'],
                                    ['publico_alvo_estimado', 'Público-alvo', 'solar:users-group-rounded-linear'],
                                    ['legibilidade', 'Legibilidade', 'solar:text-linear'],
                                    ['intencao_de_busca', 'Intenção de busca', 'solar:magnifer-linear'],
                                ] as [$field, $label, $icon])
                                    @if(!empty($aiAnalysis[$field]))
                                        <div class="flex items-start gap-2.5 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-3">
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-(--color-primary-light)">
                                                <iconify-icon icon="{{ $icon }}" class="text-xs text-(--color-primary)"></iconify-icon>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-[10px] uppercase tracking-wider text-(--text-muted)">{{ $label }}</div>
                                                <div class="mt-0.5 text-xs text-(--text-secondary)">{{ $aiAnalysis[$field] }}</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        @if(!empty($aiAnalysis['resumo']))
                            <div class="rounded-xl border border-(--border-subtle) bg-(--surface-hover) p-4">
                                <div class="mb-2 text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">Resumo</div>
                                <div class="text-sm leading-relaxed text-(--text-secondary)">{{ $aiAnalysis['resumo'] }}</div>
                            </div>
                        @endif

                        {{-- Keywords --}}
                        @if(!empty($aiAnalysis['palavras_chave_detectadas']) || !empty($aiAnalysis['palavras_chave_sugeridas']))
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                @if(!empty($aiAnalysis['palavras_chave_detectadas']))
                                    <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4">
                                        <div class="mb-2 text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">Palavras-chave detectadas</div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach((array) $aiAnalysis['palavras_chave_detectadas'] as $kw)
                                                <x-ds::badge variant="secondary">{{ $kw }}</x-ds::badge>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($aiAnalysis['palavras_chave_sugeridas']))
                                    <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4">
                                        <div class="mb-2 text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">Palavras-chave sugeridas</div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach((array) $aiAnalysis['palavras_chave_sugeridas'] as $kw)
                                                <x-ds::badge variant="info">{{ $kw }}</x-ds::badge>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Insights grid --}}
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                            @if(!empty($aiAnalysis['pontos_fortes']))
                                <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="mb-3 flex items-center gap-2">
                                        <iconify-icon icon="solar:star-circle-linear" class="text-base text-(--status-success)"></iconify-icon>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-(--status-success)">Pontos fortes</span>
                                    </div>
                                    <ul class="space-y-2">
                                        @foreach((array) $aiAnalysis['pontos_fortes'] as $s)
                                            <li class="flex items-start gap-2 text-xs text-(--text-secondary)">
                                                <iconify-icon icon="solar:check-circle-linear" class="mt-0.5 shrink-0 text-(--status-success)"></iconify-icon>
                                                {{ $s }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(!empty($aiAnalysis['oportunidades_melhoria']))
                                <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="mb-3 flex items-center gap-2">
                                        <iconify-icon icon="solar:chart-linear" class="text-base text-(--status-warning)"></iconify-icon>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-(--status-warning)">Oportunidades</span>
                                    </div>
                                    <ul class="space-y-2">
                                        @foreach((array) $aiAnalysis['oportunidades_melhoria'] as $w)
                                            <li class="flex items-start gap-2 text-xs text-(--text-secondary)">
                                                <iconify-icon icon="solar:danger-triangle-linear" class="mt-0.5 shrink-0 text-(--status-warning)"></iconify-icon>
                                                {{ $w }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if(!empty($aiAnalysis['recomendacoes_conteudo']))
                                <div class="rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="mb-3 flex items-center gap-2">
                                        <iconify-icon icon="solar:lightbulb-linear" class="text-base text-(--color-primary)"></iconify-icon>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-(--color-primary)">Recomendações</span>
                                    </div>
                                    <ul class="space-y-2">
                                        @foreach((array) $aiAnalysis['recomendacoes_conteudo'] as $rec)
                                            <li class="flex items-start gap-2 text-xs text-(--text-secondary)">
                                                <iconify-icon icon="solar:arrow-right-linear" class="mt-0.5 shrink-0 text-(--color-primary)"></iconify-icon>
                                                {{ $rec }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @elseif($aiStatus === 'done')
                        <div class="flex flex-col items-center gap-2 py-10 text-center">
                            <iconify-icon icon="solar:inbox-line-linear" class="text-4xl text-(--text-muted)"></iconify-icon>
                            <div class="text-sm text-(--text-muted)">Nenhum dado disponível. Verifique a OPENAI_API_KEY.</div>
                        </div>
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
            $redirects = array_filter($links, fn($l) => ($l['status'] ?? 200) >= 300 && ($l['status'] ?? 200) < 400);
            $lkOk      = array_filter($links, fn($l) => ($l['status'] ?? 200) >= 200 && ($l['status'] ?? 200) < 300);
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($lkOpenVal) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:link-minimalistic-2-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">Links</span>
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
                <div x-show="open" x-cloak class="mt-6 space-y-5">
                    @if($lkStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Verificando links...</span>
                        </div>
                    @elseif(!empty($links))
                        {{-- Summary strip --}}
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([
                                ['OK', count($lkOk), 'var(--status-success)', 'solar:link-circle-linear'],
                                ['Quebrados', count($broken), 'var(--status-error)', 'solar:link-broken-linear'],
                                ['Redirecionados', count($redirects), 'var(--status-warning)', 'solar:link-minimalistic-2-linear'],
                            ] as [$lbl, $cnt, $color, $icon])
                                <div class="flex items-center gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-hover) p-4">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg" style="background-color: color-mix(in srgb, {{ $color }} 12%, transparent)">
                                        <iconify-icon icon="{{ $icon }}" class="text-base" style="color: {{ $color }}"></iconify-icon>
                                    </div>
                                    <div>
                                        <div class="text-xl font-bold text-(--text-primary)">{{ $cnt }}</div>
                                        <div class="text-xs text-(--text-muted)">{{ $lbl }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto rounded-xl border border-(--border-subtle)">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-(--border-default) bg-(--surface-hover) text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">
                                    <tr>
                                        <th class="px-4 py-3">URL</th>
                                        <th class="px-4 py-3 w-20">Status</th>
                                        <th class="px-4 py-3 w-20">Tipo</th>
                                        <th class="px-4 py-3">Texto âncora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($links, 0, 100) as $link)
                                        @php
                                            $lkCode    = $link['status'] ?? 0;
                                            $lkVariant = $lkCode >= 400 || $lkCode === 0 ? 'danger' : ($lkCode >= 300 ? 'warning' : 'success');
                                            $lkLabel   = $lkCode === 0 ? 'Erro' : (string) $lkCode;
                                            $lkBorder  = $lkCode >= 400 || $lkCode === 0 ? 'border-left: 3px solid var(--status-error)' : ($lkCode >= 300 ? 'border-left: 3px solid var(--status-warning)' : '');
                                        @endphp
                                        <tr class="border-t border-(--border-subtle) hover:bg-(--surface-hover)" style="{{ $lkBorder }}">
                                            <td class="px-4 py-3 max-w-[260px]">
                                                <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener"
                                                   class="block truncate text-xs text-(--color-primary) hover:underline" title="{{ $link['url'] ?? '' }}">{{ $link['url'] ?? '—' }}</a>
                                            </td>
                                            <td class="px-4 py-3">
                                                <x-ds::badge variant="{{ $lkVariant }}">{{ $lkLabel }}</x-ds::badge>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-(--text-muted)">{{ $link['type'] ?? '—' }}</td>
                                            <td class="px-4 py-3 max-w-[180px]">
                                                <span class="block truncate text-xs text-(--text-secondary)">{{ $link['anchor'] ?? '—' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(count($links) > 100)
                                        <tr><td colspan="4" class="px-4 py-3 text-center text-xs text-(--text-muted)">... e mais {{ count($links) - 100 }} links</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @elseif($lkStatus === 'done')
                        <div class="flex flex-col items-center gap-2 py-10 text-center">
                            <iconify-icon icon="solar:inbox-line-linear" class="text-4xl text-(--text-muted)"></iconify-icon>
                            <div class="text-sm text-(--text-muted)">Nenhum link encontrado.</div>
                        </div>
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
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:gallery-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">Imagens WordPress</span>
                        @if($wiStatus !== 'running' && $totalImages > 0)
                            <x-ds::badge variant="secondary">{{ $totalImages }} imagens</x-ds::badge>
                            @if($imagesError > 0) <x-ds::badge variant="danger">{{ $imagesError }} erro</x-ds::badge> @endif
                            @if($imagesNotWebp > 0) <x-ds::badge variant="warning">{{ $imagesNotWebp }} não WEBP</x-ds::badge> @endif
                        @endif
                        @if($wiStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($wiStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-6 space-y-5">
                    @if($wiStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Auditando imagens...</span>
                        </div>
                    @else
                        {{-- Stat grid with icons --}}
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                            @foreach([
                                ['Total',    $totalImages,           'solar:gallery-linear',           'var(--color-primary)',   'var(--color-primary-light)'],
                                ['Corretas', $imagesOk,              'solar:check-circle-linear',      'var(--status-success)',  'color-mix(in srgb, var(--status-success) 12%, transparent)'],
                                ['Com erro', $imagesError,           'solar:close-circle-linear',      'var(--status-warning)',  'color-mix(in srgb, var(--status-warning) 12%, transparent)'],
                                ['Não WEBP', $imagesNotWebp,         'solar:gallery-remove-linear',    'var(--status-warning)',  'color-mix(in srgb, var(--status-warning) 12%, transparent)'],
                                ['Sem ALT',  $imagesWithoutAlt,      'solar:text-cross-linear',        'var(--status-danger)',   'color-mix(in srgb, var(--status-error) 12%, transparent)'],
                                ['>500KB',   $imagesLarge,           'solar:file-corrupted-linear',    'var(--status-danger)',   'color-mix(in srgb, var(--status-error) 12%, transparent)'],
                            ] as [$label, $count, $icon, $color, $bgColor])
                                <div class="flex flex-col items-center gap-2 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4 text-center">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-xl" style="background-color: {{ $bgColor }}">
                                        <iconify-icon icon="{{ $icon }}" class="text-lg" style="color: {{ $color }}"></iconify-icon>
                                    </div>
                                    <div class="text-2xl font-bold text-(--text-primary)">{{ $count }}</div>
                                    <div class="text-[10px] uppercase tracking-wide text-(--text-muted)">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>

                        @if(!empty($images))
                            {{-- Modal páginas da imagem (escopo único Alpine) --}}
                            <div x-data="{ open: false, pages: [], imgUrl: '' }"
                                 x-on:open-img-pages.window="open = true; pages = $event.detail.pages; imgUrl = $event.detail.imgUrl"
                                 x-on:keydown.escape.window="open = false">

                                {{-- Modal --}}
                                <div x-show="open" x-cloak class="fixed inset-0 z-50" role="dialog" aria-modal="true">
                                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         @click="open = false"></div>
                                    <div class="absolute inset-0 overflow-y-auto p-4">
                                        <div class="flex min-h-full items-center justify-center">
                                            <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl border border-(--border-default) bg-(--surface-card) shadow-xl"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave="transition ease-in duration-150"
                                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                                                 @click.stop>
                                                <div class="flex items-center justify-between gap-3 border-b border-(--border-subtle) px-6 py-4">
                                                    <div class="min-w-0">
                                                        <div class="text-base font-semibold text-(--text-primary)">Páginas que usam esta imagem</div>
                                                        <div class="mt-1 truncate text-xs text-(--text-muted)" x-text="imgUrl"></div>
                                                    </div>
                                                    <button type="button" @click="open = false"
                                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-(--text-secondary) transition-colors hover:bg-(--surface-hover) hover:text-(--text-primary)">
                                                        <iconify-icon icon="iconamoon:sign-times-light" class="text-xl"></iconify-icon>
                                                    </button>
                                                </div>
                                                <div class="max-h-[60vh] overflow-y-auto px-6 py-5">
                                                    <div class="divide-y divide-(--border-subtle)">
                                                        <template x-for="pg in pages" :key="pg.url">
                                                            <div class="flex items-center gap-3 py-3">
                                                                <iconify-icon icon="solar:link-linear" class="shrink-0 text-(--text-muted)"></iconify-icon>
                                                                <a :href="pg.url" target="_blank" rel="noopener"
                                                                   class="min-w-0 flex-1 truncate text-sm text-(--color-primary) hover:underline"
                                                                   x-text="pg.title || pg.url"
                                                                   :title="pg.url"></a>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="flex items-center justify-end gap-3 border-t border-(--border-subtle) bg-(--surface-page) px-6 py-4">
                                                    <x-ds::button variant="secondary" @click="open = false">Fechar</x-ds::button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Images table --}}
                                <div class="overflow-x-auto rounded-xl border border-(--border-subtle)">
                                    <table class="w-full text-left text-sm">
                                        <thead class="border-b border-(--border-default) bg-(--surface-hover) text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">
                                            <tr>
                                                <th class="px-4 py-3">ID</th>
                                                <th class="px-4 py-3">URL</th>
                                                <th class="px-4 py-3">Formato</th>
                                                <th class="px-4 py-3">Dimensões</th>
                                                <th class="px-4 py-3">Peso</th>
                                                <th class="px-4 py-3">Texto ALT</th>
                                                <th class="px-4 py-3 text-center">Páginas</th>
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
                                                    $hasIssue = !$alt || $ext !== 'webp' || ($bytes !== null && $bytes > 500000);
                                                    $rowStyle = $hasIssue ? 'border-left: 3px solid var(--status-warning)' : '';
                                                @endphp
                                                <tr class="border-t border-(--border-subtle) hover:bg-(--surface-hover)" style="{{ $rowStyle }}">
                                                    <td class="px-4 py-3 text-xs text-(--text-muted)">{{ $img['id'] ?? '—' }}</td>
                                                    <td class="px-4 py-3 max-w-[200px]">
                                                        <a href="{{ $imgUrl }}" target="_blank" rel="noopener"
                                                           class="block truncate text-xs text-(--color-primary) hover:underline" title="{{ $imgUrl }}">
                                                            {{ $imgUrl ?: '—' }}
                                                        </a>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <x-ds::badge variant="{{ $ext === 'webp' ? 'success' : 'warning' }}">{{ strtoupper($ext) ?: '?' }}</x-ds::badge>
                                                    </td>
                                                    <td class="px-4 py-3 text-xs text-(--text-secondary) whitespace-nowrap">
                                                        {{ ($imgW && $imgH) ? $imgW . '×' . $imgH : '—' }}
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if($bytes !== null)
                                                            <x-ds::badge variant="{{ $bytesVariant($bytes) }}">{{ $formatBytes($bytes) }}</x-ds::badge>
                                                        @else
                                                            <span class="text-xs text-(--text-muted)">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if($alt)
                                                            <span class="block max-w-[160px] truncate text-xs text-(--text-secondary)" title="{{ $alt }}">{{ $alt }}</span>
                                                        @else
                                                            <x-ds::badge variant="danger">Sem alt</x-ds::badge>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if(!empty($imgPages))
                                                            <button type="button"
                                                                    @click="$dispatch('open-img-pages', { pages: @js($imgPages), imgUrl: @js($imgUrl) })"
                                                                    class="inline-flex items-center gap-1 rounded-lg border border-(--border-subtle) bg-(--surface-hover) px-2.5 py-1 text-[11px] font-semibold text-(--color-primary) hover:bg-(--color-primary-light)">
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
                            <div class="flex flex-col items-center gap-2 py-10 text-center">
                                <iconify-icon icon="solar:inbox-line-linear" class="text-4xl text-(--text-muted)"></iconify-icon>
                                <div class="text-sm text-(--text-muted)">Nenhuma imagem encontrada.</div>
                            </div>
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
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:document-text-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">Páginas SEO</span>
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
                <div x-show="open" x-cloak class="mt-6 space-y-5">
                    @if($wpStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Auditando páginas...</span>
                        </div>
                    @else
                        {{-- Stat grid --}}
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            @foreach([
                                ['Sem meta desc.',  $pagesWithoutMetaDescription,  'warning', 'solar:align-left-linear'],
                                ['Sem H1',          $pagesWithoutH1,               'danger',  'solar:text-bold-linear'],
                                ['Sem title tag',   $pagesWithoutTitleTag,         'danger',  'solar:tag-linear'],
                                ['Desc. duplicadas',$pagesDuplicateDescription,    'warning', 'solar:copy-linear'],
                            ] as [$lbl, $cnt, $v, $ico])
                                @php $color = $cnt > 0 ? ('var(--status-' . $v . ')') : 'var(--text-muted)'; @endphp
                                <div class="flex items-center gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" style="background-color: color-mix(in srgb, {{ $color }} 12%, transparent)">
                                        <iconify-icon icon="{{ $ico }}" class="text-lg" style="color: {{ $color }}"></iconify-icon>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-(--text-primary)">{{ $cnt }}</div>
                                        <div class="text-[10px] text-(--text-muted)">{{ $lbl }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(!empty($pages))
                            <div class="overflow-x-auto rounded-xl border border-(--border-subtle)">
                                <table class="w-full text-left text-sm">
                                    <thead class="border-b border-(--border-default) bg-(--surface-hover) text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">
                                        <tr>
                                            <th class="px-4 py-3">Página</th>
                                            <th class="px-4 py-3">Title tag</th>
                                            <th class="px-4 py-3">Meta desc.</th>
                                            <th class="px-4 py-3">H1</th>
                                            <th class="px-4 py-3">Canonical</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pages as $page)
                                            @php
                                                $htmlFetched = (bool) ($page['html_fetched'] ?? false);
                                                $hasMissing  = !($page['title_tag'] ?? null) || !($page['meta_description'] ?? null);
                                                $rowStyle    = $hasMissing ? 'border-left: 3px solid var(--status-warning)' : '';
                                            @endphp
                                            <tr class="border-t border-(--border-subtle) hover:bg-(--surface-hover)" style="{{ $rowStyle }}">
                                                <td class="px-4 py-3 max-w-[180px]">
                                                    @if(!empty($page['url']))
                                                        <a href="{{ $page['url'] }}" target="_blank" rel="noopener"
                                                           class="block truncate text-xs font-semibold text-(--color-primary) hover:underline" title="{{ $page['url'] }}">{{ $page['title'] ?? $page['url'] }}</a>
                                                    @else
                                                        <span class="block truncate text-xs font-medium text-(--text-primary)">{{ $page['title'] ?? '—' }}</span>
                                                    @endif
                                                    <span class="block truncate text-[10px] text-(--text-muted)">{{ $page['url'] ?? '' }}</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php $t = $page['title_tag'] ?? null; @endphp
                                                    @if($t)
                                                        <span class="block max-w-[140px] truncate text-xs text-(--text-secondary)" title="{{ $t }}">{{ $t }}</span>
                                                    @else
                                                        <x-ds::badge variant="danger">Sem title</x-ds::badge>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @php $md = $page['meta_description'] ?? null; $mdLen = $md ? mb_strlen(trim($md)) : 0; @endphp
                                                    @if($md)
                                                        @php $mdV = $mdLen > 160 ? 'danger' : ($mdLen < 50 ? 'warning' : 'success'); @endphp
                                                        <x-ds::badge variant="{{ $mdV }}">{{ $mdLen }}c</x-ds::badge>
                                                    @else
                                                        <x-ds::badge variant="danger">Sem meta</x-ds::badge>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
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
                                                <td class="px-4 py-3">
                                                    @php $can = $page['canonical'] ?? null; @endphp
                                                    <x-ds::badge variant="{{ $can ? 'success' : 'warning' }}">{{ $can ? 'OK' : 'Ausente' }}</x-ds::badge>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($wpStatus === 'done')
                            <div class="flex flex-col items-center gap-2 py-10 text-center">
                                <iconify-icon icon="solar:inbox-line-linear" class="text-4xl text-(--text-muted)"></iconify-icon>
                                <div class="text-sm text-(--text-muted)">Nenhuma página encontrada.</div>
                            </div>
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
            // Group by severity
            $wsCritical = array_filter($wpSecurityChecks, fn($c) => in_array($c['severity'] ?? '', ['critical', 'high']) && ($c['status'] ?? 'pass') !== 'pass');
            $wsMedium   = array_filter($wpSecurityChecks, fn($c) => ($c['severity'] ?? '') === 'medium' && ($c['status'] ?? 'pass') !== 'pass');
            $wsOk       = array_filter($wpSecurityChecks, fn($c) => ($c['status'] ?? 'pass') === 'pass');
        @endphp
        <x-ds::card>
            <div x-data="{ open: @js($wsOpenVal) }">
                <div class="flex cursor-pointer items-center justify-between" @click="open = !open">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-(--color-primary-light)">
                            <iconify-icon icon="solar:shield-warning-linear" class="text-base text-(--color-primary)"></iconify-icon>
                        </div>
                        <span class="font-semibold text-(--text-primary)">Segurança WordPress</span>
                        @if($wsStatus !== 'running' && !empty($wpSecurityChecks))
                            <x-ds::badge variant="{{ $wsVariant }}">{{ $wpSecurityScore }}/100</x-ds::badge>
                            @if(count($wsCritical) > 0)
                                <x-ds::badge variant="danger">{{ count($wsCritical) }} crítico(s)</x-ds::badge>
                            @endif
                        @endif
                        @if($wsStatus === 'running') <x-ds::spinner size="sm" />
                        @elseif($wsStatus === 'error') <x-ds::badge variant="danger">Erro</x-ds::badge>
                        @endif
                    </div>
                    <iconify-icon icon="solar:alt-arrow-down-linear" x-bind:class="open ? 'rotate-180' : ''" class="transition-transform text-(--text-muted)"></iconify-icon>
                </div>
                <div x-show="open" x-cloak class="mt-6 space-y-5">
                    @if($wsStatus === 'running')
                        <div class="flex items-center gap-3 py-6">
                            <x-ds::spinner />
                            <span class="text-sm text-(--text-muted)">Verificando segurança WordPress...</span>
                        </div>
                    @else
                        {{-- Score + general info --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            {{-- Score ring --}}
                            <div class="flex items-center gap-5 rounded-2xl border border-(--border-subtle) bg-(--surface-hover) p-5">
                                <div class="relative h-20 w-20 shrink-0">
                                    {!! $scoreRing($wpSecurityScore, $wsVariant) !!}
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-2xl font-bold text-(--text-primary)">{{ $wpSecurityScore }}</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="mb-2 text-sm font-semibold text-(--text-primary)">Score de segurança</div>
                                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-(--border-subtle)">
                                        <div class="h-full rounded-full" style="width: {{ $wpSecurityScore }}%; background-color: var(--status-{{ $wsVariant }})"></div>
                                    </div>
                                    <div class="mt-2 text-xs text-(--text-muted)">{{ count($wsOk) }} de {{ count($wpSecurityChecks) }} verificações OK</div>
                                </div>
                            </div>
                            {{-- General WP info --}}
                            @if($general)
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="flex items-center gap-2.5 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-3">
                                        <iconify-icon icon="solar:wordpress-linear" class="text-xl text-(--color-primary)"></iconify-icon>
                                        <div>
                                            <div class="text-[10px] text-(--text-muted)">Versão WP</div>
                                            <div class="text-sm font-semibold text-(--text-primary)">{{ $general['wp_version'] ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2.5 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-3">
                                        <iconify-icon icon="solar:lock-linear" class="text-xl {{ $sslEnabled ? 'text-(--status-success)' : 'text-(--status-danger)' }}"></iconify-icon>
                                        <div>
                                            <div class="text-[10px] text-(--text-muted)">SSL</div>
                                            <x-ds::badge variant="{{ $sslEnabled ? 'success' : 'danger' }}">{{ $sslEnabled ? 'Ativo' : 'Sem SSL' }}</x-ds::badge>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2.5 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-3">
                                        <iconify-icon icon="solar:plug-circle-linear" class="text-xl {{ $inactivePlugins > 0 ? 'text-(--status-warning)' : 'text-(--status-success)' }}"></iconify-icon>
                                        <div>
                                            <div class="text-[10px] text-(--text-muted)">Plugins inativos</div>
                                            <x-ds::badge variant="{{ $inactivePlugins > 0 ? 'warning' : 'success' }}">{{ $inactivePlugins }}</x-ds::badge>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2.5 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-3">
                                        <iconify-icon icon="solar:palette-linear" class="text-xl text-(--text-muted)"></iconify-icon>
                                        <div class="min-w-0">
                                            <div class="text-[10px] text-(--text-muted)">Tema ativo</div>
                                            <div class="truncate text-xs font-medium text-(--text-secondary)">{{ $general['active_theme'] ?? '—' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Critical / High issues --}}
                        @if(!empty($wsCritical))
                            <div>
                                <div class="mb-3 flex items-center gap-2">
                                    <div class="h-3.5 w-0.5 rounded-full bg-(--status-danger)"></div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-(--status-danger)">Crítico / Alto risco ({{ count($wsCritical) }})</span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($wsCritical as $check)
                                        @php
                                            $sevV = match($check['severity'] ?? 'low') { 'critical' => 'danger', 'high' => 'danger', 'medium' => 'warning', default => 'secondary' };
                                        @endphp
                                        <div class="flex items-start gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4" style="border-left: 3px solid var(--status-error)">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg" style="background-color: color-mix(in srgb, var(--status-error) 12%, transparent)">
                                                <iconify-icon icon="solar:close-circle-bold" class="text-base text-(--status-danger)"></iconify-icon>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="font-semibold text-(--text-primary)">{{ $check['name'] ?? '—' }}</span>
                                                    <x-ds::badge variant="{{ $sevV }}" size="xs">{{ $check['severity'] ?? '' }}</x-ds::badge>
                                                </div>
                                                @if(!empty($check['value']))
                                                    <div class="mt-0.5 text-xs text-(--text-secondary)">{{ $check['value'] }}</div>
                                                @endif
                                                @if(!empty($check['tip']))
                                                    <div class="mt-1.5 flex items-start gap-1 text-xs text-(--text-muted)">
                                                        <iconify-icon icon="solar:lightbulb-linear" class="mt-0.5 shrink-0 text-(--status-warning)"></iconify-icon>
                                                        {{ $check['tip'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Medium issues --}}
                        @if(!empty($wsMedium))
                            <div>
                                <div class="mb-3 flex items-center gap-2">
                                    <div class="h-3.5 w-0.5 rounded-full bg-(--status-warning)"></div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-(--status-warning)">Risco médio ({{ count($wsMedium) }})</span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($wsMedium as $check)
                                        <div class="flex items-start gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-4" style="border-left: 3px solid var(--status-warning)">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg" style="background-color: color-mix(in srgb, var(--status-warning) 12%, transparent)">
                                                <iconify-icon icon="solar:danger-triangle-bold" class="text-base text-(--status-warning)"></iconify-icon>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="font-semibold text-(--text-primary)">{{ $check['name'] ?? '—' }}</div>
                                                @if(!empty($check['value']))
                                                    <div class="mt-0.5 text-xs text-(--text-secondary)">{{ $check['value'] }}</div>
                                                @endif
                                                @if(!empty($check['tip']))
                                                    <div class="mt-1.5 flex items-start gap-1 text-xs text-(--text-muted)">
                                                        <iconify-icon icon="solar:lightbulb-linear" class="mt-0.5 shrink-0 text-(--status-warning)"></iconify-icon>
                                                        {{ $check['tip'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- OK checks --}}
                        @if(!empty($wsOk))
                            <div>
                                <div class="mb-3 flex items-center gap-2">
                                    <div class="h-3.5 w-0.5 rounded-full bg-(--status-success)"></div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-(--status-success)">Aprovados ({{ count($wsOk) }})</span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($wsOk as $check)
                                        <div class="flex items-center gap-3 rounded-xl border border-(--border-subtle) bg-(--surface-card) p-3.5" style="border-left: 3px solid var(--status-success)">
                                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg" style="background-color: color-mix(in srgb, var(--status-success) 12%, transparent)">
                                                <iconify-icon icon="solar:check-circle-bold" class="text-sm text-(--status-success)"></iconify-icon>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <span class="text-sm font-medium text-(--text-primary)">{{ $check['name'] ?? '—' }}</span>
                                                @if(!empty($check['value']))
                                                    <span class="ml-2 text-xs text-(--text-muted)">{{ $check['value'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(empty($wpSecurityChecks) && $wsStatus === 'done')
                            <div class="flex flex-col items-center gap-2 py-10 text-center">
                                <iconify-icon icon="solar:inbox-line-linear" class="text-4xl text-(--text-muted)"></iconify-icon>
                                <div class="text-sm text-(--text-muted)">Nenhum dado disponível.</div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </x-ds::card>
    @endif

</div>
