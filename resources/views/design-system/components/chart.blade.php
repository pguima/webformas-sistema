@props([
    'id' => null,
    'type' => 'line',
    'series' => [],
    'options' => [],
    'height' => 280,
    'width' => '100%',
    'theme' => 'auto',
])

@php
    $chartId = $id ?: 'ds-chart-' . uniqid();

    $baseOptions = [
        'chart' => [
            'type' => $type,
            'height' => $height,
            'toolbar' => ['show' => false],
            'zoom' => ['enabled' => false],
            'fontFamily' => 'var(--font-family-base)',
            'foreColor' => 'var(--text-secondary)',
        ],
        'stroke' => ['width' => 3, 'curve' => 'smooth'],
        'grid' => [
            'borderColor' => 'var(--border-subtle)',
            'strokeDashArray' => 4,
            'padding' => ['top' => 0, 'right' => 8, 'bottom' => 0, 'left' => 8],
        ],
        'dataLabels' => ['enabled' => false],
        'legend' => [
            'show' => false,
        ],
        'tooltip' => [
            'theme' => 'light',
            'intersect' => false,
        ],
    ];
@endphp

<div
    x-data="{
        chart: null,
        resizeObs: null,
        cleanups: [],
        addCleanup(fn) {
            if (typeof fn === 'function') this.cleanups.push(fn);
        },
        destroy() {
            try {
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
            } catch (e) {}

            try {
                this.resizeObs?.disconnect();
            } catch (e) {}
            this.resizeObs = null;

            const fns = Array.isArray(this.cleanups) ? this.cleanups.splice(0) : [];
            for (const fn of fns) {
                try { fn(); } catch (e) {}
            }
        },
        buildOptions() {
            const isDark = document.documentElement.classList.contains('dark');
            const mode = @js($theme) === 'auto' ? (isDark ? 'dark' : 'light') : @js($theme);

            const base = @js($baseOptions);
            const extra = @js($options);

            const getFormatter = (name) => {
                if (!name) return null;
                if (typeof name === 'function') return name;
                if (typeof name !== 'string') return null;

                const n = name.trim();

                if (n === 'compactNumber') {
                    return (v) => {
                        const x = Number(v);
                        if (!Number.isFinite(x)) return '—';
                        if (Math.abs(x) >= 1e9) return (x / 1e9).toFixed(2).replace(/\.00$/, '') + 'B';
                        if (Math.abs(x) >= 1e6) return (x / 1e6).toFixed(2).replace(/\.00$/, '') + 'M';
                        if (Math.abs(x) >= 1e3) return (x / 1e3).toFixed(1).replace(/\.0$/, '') + 'K';
                        return x.toLocaleString('pt-BR');
                    };
                }

                if (n === 'currencyBRL') {
                    return (v) => {
                        const x = Number(v);
                        if (!Number.isFinite(x)) return '—';
                        return x.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    };
                }

                if (n === 'percent2') {
                    return (v) => {
                        const x = Number(v);
                        if (!Number.isFinite(x)) return '—';
                        return x.toFixed(2).replace('.', ',') + '%';
                    };
                }

                if (n === 'number0') {
                    return (v) => {
                        const x = Number(v);
                        if (!Number.isFinite(x)) return '—';
                        return Math.round(x).toLocaleString('pt-BR');
                    };
                }

                if (n === 'number2') {
                    return (v) => {
                        const x = Number(v);
                        if (!Number.isFinite(x)) return '—';
                        return x.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    };
                }

                return null;
            };

            const deepMerge = (target, source) => {
                if (!source || typeof source !== 'object') return target;
                for (const key of Object.keys(source)) {
                    const v = source[key];
                    if (Array.isArray(v)) {
                        target[key] = v;
                    } else if (v && typeof v === 'object') {
                        target[key] = deepMerge({ ...(target[key] ?? {}) }, v);
                    } else {
                        target[key] = v;
                    }
                }
                return target;
            };

            const merged = deepMerge({ ...base }, extra);

            const formatters = (merged && typeof merged === 'object')
                ? (merged.__formatters ?? null)
                : null;

            try {
                if (merged && typeof merged === 'object' && '__formatters' in merged) {
                    delete merged.__formatters;
                }
            } catch (e) {}

            merged.theme = { mode };
            merged.tooltip = merged.tooltip ?? {};
            merged.tooltip.theme = mode;

            // ApexCharts constraint: tooltip.shared cannot be enabled with tooltip.intersect=true
            if (merged.tooltip?.shared === true) {
                merged.tooltip.intersect = false;
            }

            if (formatters && typeof formatters === 'object') {
                const yaxisFmt = formatters.yaxis;
                const tooltipFmt = formatters.tooltipY;
                const dataLabelFmt = formatters.dataLabels;

                if (yaxisFmt) {
                    const applyYaxis = (y, fmtNameOrFn) => {
                        const fn = getFormatter(fmtNameOrFn);
                        if (!fn) return;
                        y.labels = y.labels ?? {};
                        y.labels.formatter = fn;
                    };

                    if (Array.isArray(merged.yaxis)) {
                        merged.yaxis = merged.yaxis.map((y, i) => {
                            const yy = (y && typeof y === 'object') ? { ...y } : {};
                            const chosen = (yaxisFmt && typeof yaxisFmt === 'object' && !Array.isArray(yaxisFmt))
                                ? (yaxisFmt[i] ?? yaxisFmt[String(i)] ?? yaxisFmt.default)
                                : yaxisFmt;
                            applyYaxis(yy, chosen);
                            return yy;
                        });
                    } else if (merged.yaxis && typeof merged.yaxis === 'object') {
                        const yy = { ...merged.yaxis };
                        applyYaxis(yy, yaxisFmt);
                        merged.yaxis = yy;
                    }
                }

                if (tooltipFmt) {
                    merged.tooltip = merged.tooltip ?? {};
                    merged.tooltip.y = merged.tooltip.y ?? {};

                    const applyTooltipY = (target, fmtNameOrFn) => {
                        const fn = getFormatter(fmtNameOrFn);
                        if (!fn) return;
                        target.formatter = fn;
                    };

                    if (Array.isArray(merged.series)) {
                        if (typeof tooltipFmt === 'object' && !Array.isArray(tooltipFmt)) {
                            applyTooltipY(merged.tooltip.y, (tooltipFmt.default ?? null));
                            merged.tooltip.y.formatter = (value, ctx) => {
                                const idx = ctx?.seriesIndex;
                                const key = idx ?? 0;
                                const chosen = tooltipFmt[key] ?? tooltipFmt[String(key)] ?? tooltipFmt.default;
                                const fn = getFormatter(chosen);
                                return fn ? fn(value) : value;
                            };
                        } else {
                            applyTooltipY(merged.tooltip.y, tooltipFmt);
                        }
                    } else {
                        applyTooltipY(merged.tooltip.y, tooltipFmt);
                    }
                }

                if (dataLabelFmt) {
                    const fn = getFormatter(dataLabelFmt);
                    if (fn) {
                        merged.dataLabels = merged.dataLabels ?? {};
                        merged.dataLabels.formatter = fn;
                    }
                }
            }

            return merged;
        },
        render() {
            if (!window.ApexCharts) {
                return;
            }

            const el = this.$refs?.canvas ?? document.getElementById(@js($chartId));
            if (!el) return;
            if (!el.isConnected) return;

            // If the chart is inside a hidden container (e.g. x-show/display:none),
            // ApexCharts will render with 0 width and may never recover. Wait until visible.
            if (el.clientWidth === 0 || el.clientHeight === 0) {
                return;
            }

            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }

            const config = this.buildOptions();
            config.series = @js($series);

            try {
                this.chart = new ApexCharts(el, config);
                this.chart.render();
            } catch (e) {
                try {
                    console.error('[ds::chart] render failed', {
                        id: @js($chartId),
                        type: @js($type),
                        error: e,
                        options: config,
                    });
                } catch (_) {}
            }
        },
        scheduleRender() {
            // Needs 2 conditions:
            // - ApexCharts is loaded
            // - The container is visible (has width/height)
            let ticks = 0;
            const wait = setInterval(() => {
                ticks++;
                const el = this.$refs?.canvas ?? document.getElementById(@js($chartId));
                const connected = !!el && !!el.isConnected;
                const visible = connected && el.clientWidth > 0 && el.clientHeight > 0;

                if (window.ApexCharts && visible) {
                    clearInterval(wait);
                    requestAnimationFrame(() => requestAnimationFrame(() => this.render()));
                } else if (ticks >= 200) {
                    clearInterval(wait); // give up after ~10 s
                    try {
                        console.warn('[ds::chart] scheduleRender timeout', {
                            id: @js($chartId),
                            type: @js($type),
                            apexLoaded: !!window.ApexCharts,
                            hasEl: !!el,
                            isConnected: connected,
                            width: el?.clientWidth,
                            height: el?.clientHeight,
                        });
                    } catch (_) {}
                }
            }, 50);

            this.addCleanup(() => clearInterval(wait));
        },
        observeTheme() {
            const obs = new MutationObserver(() => this.render());
            obs.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            this.addCleanup(() => obs.disconnect());
        },
        observeResize() {
            const el = this.$refs?.canvas ?? document.getElementById(@js($chartId));
            if (!el || !window.ResizeObserver) return;

            let raf = null;
            this.resizeObs = new ResizeObserver(() => {
                if (raf) cancelAnimationFrame(raf);
                raf = requestAnimationFrame(() => {
                    this.render();
                });
            });

            this.resizeObs.observe(el);
            this.addCleanup(() => {
                if (raf) cancelAnimationFrame(raf);
                this.resizeObs?.disconnect();
            });
        }
    }"
    x-init="scheduleRender(); observeTheme(); observeResize();"
    x-on:ds-chart-rerender.window="scheduleRender()"
    x-on:destroy.window="destroy()"
    class="w-full"
>
    <div x-ref="canvas" id="{{ $chartId }}" style="height: {{ is_numeric($height) ? (int) $height . 'px' : $height }}; width: {{ is_numeric($width) ? (int) $width . 'px' : $width }};"></div>
</div>
