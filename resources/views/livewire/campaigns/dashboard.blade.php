<div class="space-y-6" wire:init="loadData">
    <x-ds::card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.clients.profile.tabs.campaign') }}</div>
                <div class="mt-1 text-2xl font-semibold text-(--text-primary)">{{ __('app.campaigns.client_tab.title') }}</div>
                <div class="mt-1 text-sm text-(--text-secondary)">
                    {{ __('app.campaigns.client_tab.subtitle', ['name' => $campaign->client->name ?? '']) }}
                </div>
            </div>

            <div class="flex w-full flex-col gap-3 sm:flex-row sm:items-end sm:justify-end">
                <div class="w-full sm:max-w-xs">
                    <x-ds::select
                        label="{{ __('app.campaigns.dashboard.period') }}"
                        wire:model.live="period"
                        :options="__('app.campaigns.periods')"
                    />
                </div>

                <div class="flex gap-2">
                    <x-ds::button
                        type="button"
                        variant="secondary"
                        icon="solar:refresh-linear"
                        wire:click="loadData"
                        wire:loading.attr="disabled"
                        wire:target="loadData"
                    >
                        {{ __('app.campaigns.dashboard.refresh') }}
                    </x-ds::button>
                </div>
            </div>
        </div>

        <div class="mt-4">
            @if (! $this->hasIds)
                <x-ds::alert variant="warning" icon="solar:info-circle-linear" :dismissible="false">
                    {{ __('app.campaigns.dashboard.missing_ids') }}
                </x-ds::alert>
            @endif

            @if ($errorMessage)
                <div class="mt-4">
                    <x-ds::alert variant="danger" icon="solar:danger-circle-linear" :dismissible="false">
                        {{ __('app.campaigns.dashboard.error') }}
                    </x-ds::alert>
                </div>
            @endif

            @php($t = $this->totals)
            @php($campaignsTotal = is_iterable($rows) ? count($rows) : 0)
            @php($campaignsActive = is_iterable($rows) ? count(array_filter($rows, fn ($r) => in_array(strtoupper((string) data_get($r, 'status', '')), ['ENABLED', 'ACTIVE', 'ATIVA', 'ATIVO'], true))) : 0)
            @php($ctrAvg = ($t['impressions'] ?? 0) > 0 ? (($t['interactions'] ?? 0) / $t['impressions']) * 100 : 0)

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-8">
                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 shadow-(--shadow-sm)" style="box-shadow: var(--shadow-sm); border-top: 2px solid color-mix(in oklab, var(--color-primary) 70%, transparent); background: radial-gradient(700px circle at 20% 0%, color-mix(in oklab, var(--color-primary) 14%, transparent), transparent 45%), var(--surface-card);">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.dashboard.table_title') }}</div>
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-hover) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ $period }}</span>
                    </div>
                    <div class="mt-3 text-3xl font-semibold leading-tight text-(--text-primary)">{{ number_format((int) $campaignsTotal, 0, ',', '.') }}</div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.table.name') }}</span>
                    </div>
                </div>

                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 shadow-(--shadow-sm)" style="box-shadow: var(--shadow-sm); border-top: 2px solid color-mix(in oklab, var(--status-success) 70%, transparent); background: radial-gradient(700px circle at 20% 0%, color-mix(in oklab, var(--status-success) 14%, transparent), transparent 45%), var(--surface-card);">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.table.status') }}</div>
                        <span class="inline-flex items-center rounded-md border px-2 py-1 text-[11px] font-semibold" style="background-color: color-mix(in oklab, var(--status-success) 18%, transparent); color: var(--status-success); border-color: color-mix(in oklab, var(--status-success) 35%, var(--border-subtle));">
                            {{ __('common.active') }}
                        </span>
                    </div>
                    <div class="mt-3 text-3xl font-semibold leading-tight text-(--text-primary)">{{ number_format((int) $campaignsActive, 0, ',', '.') }}</div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ number_format((int) $campaignsTotal, 0, ',', '.') }} total</span>
                    </div>
                </div>

                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 shadow-(--shadow-sm)" style="box-shadow: var(--shadow-sm); border-top: 2px solid color-mix(in oklab, var(--status-info) 70%, transparent); background: radial-gradient(700px circle at 20% 0%, color-mix(in oklab, var(--status-info) 14%, transparent), transparent 45%), var(--surface-card);">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.metrics.impressions') }}</div>
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-hover) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</span>
                    </div>
                    <div class="mt-3 text-3xl font-semibold leading-tight text-(--text-primary)">{{ number_format((int) $t['impressions'], 0, ',', '.') }}</div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.interactions') }}: {{ number_format((int) $t['interactions'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 shadow-(--shadow-sm)" style="box-shadow: var(--shadow-sm); border-top: 2px solid color-mix(in oklab, var(--tag-blue-text) 70%, transparent); background: radial-gradient(700px circle at 20% 0%, color-mix(in oklab, var(--tag-blue-text) 14%, transparent), transparent 45%), var(--surface-card);">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.table.ctr') }}</div>
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-hover) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</span>
                    </div>
                    <div class="mt-3 text-3xl font-semibold leading-tight text-(--text-primary)">{{ number_format((float) $ctrAvg, 2, ',', '.') }}%</div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.interactions') }} / {{ __('app.campaigns.metrics.impressions') }}</span>
                    </div>
                </div>

                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 shadow-(--shadow-sm)" style="box-shadow: var(--shadow-sm); border-top: 2px solid color-mix(in oklab, var(--tag-purple-text) 70%, transparent); background: radial-gradient(700px circle at 20% 0%, color-mix(in oklab, var(--tag-purple-text) 14%, transparent), transparent 45%), var(--surface-card);">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.metrics.cost') }}</div>
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-hover) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">BRL</span>
                    </div>
                    <div class="mt-3 text-3xl font-semibold leading-tight text-(--text-primary)">R$ {{ number_format((float) $t['cost'], 2, ',', '.') }}</div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</span>
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.cpc') }}: {{ $t['cpc'] !== null ? 'R$ ' . number_format((float) $t['cpc'], 2, ',', '.') : __('app.common.dash') }}</span>
                    </div>
                </div>

                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 shadow-(--shadow-sm)" style="box-shadow: var(--shadow-sm); border-top: 2px solid color-mix(in oklab, var(--tag-green-text) 70%, transparent); background: radial-gradient(700px circle at 20% 0%, color-mix(in oklab, var(--tag-green-text) 14%, transparent), transparent 45%), var(--surface-card);">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.metrics.conversions') }}</div>
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-hover) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</span>
                    </div>
                    <div class="mt-3 text-3xl font-semibold leading-tight text-(--text-primary)">{{ number_format((float) $t['conversions'], 0, ',', '.') }}</div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.cpa') }}: {{ $t['cpa'] !== null ? 'R$ ' . number_format((float) $t['cpa'], 2, ',', '.') : __('app.common.dash') }}</span>
                    </div>
                </div>

                <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 shadow-(--shadow-sm)" style="box-shadow: var(--shadow-sm); border-top: 2px solid color-mix(in oklab, var(--tag-orange-text) 70%, transparent); background: radial-gradient(700px circle at 20% 0%, color-mix(in oklab, var(--tag-orange-text) 14%, transparent), transparent 45%), var(--surface-card);">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.metrics.video_views') }}</div>
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-hover) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</span>
                    </div>
                    <div class="mt-3 text-3xl font-semibold leading-tight text-(--text-primary)">{{ number_format((int) $t['videoViews'], 0, ',', '.') }}</div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-md border border-(--border-subtle) bg-(--surface-card) px-2 py-1 text-[11px] font-medium text-(--text-secondary)">{{ __('app.campaigns.metrics.cpc') }}: {{ $t['cpc'] !== null ? 'R$ ' . number_format((float) $t['cpc'], 2, ',', '.') : __('app.common.dash') }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-sm font-semibold text-(--text-primary)">{{ __('app.campaigns.dashboard.table_title') }}</div>
                        <div class="mt-1 text-xs text-(--text-secondary)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div wire:loading wire:target="loadData">
                            <x-ds::spinner size="sm" variant="info" :label="__('app.campaigns.dashboard.loading')" />
                        </div>

                        <x-ds::button
                            type="button"
                            variant="secondary"
                            icon="solar:refresh-linear"
                            wire:click="loadData"
                            wire:loading.attr="disabled"
                            wire:target="loadData"
                        >
                            {{ __('app.campaigns.dashboard.refresh') }}
                        </x-ds::button>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 shadow-(--shadow-sm)" style="background: radial-gradient(900px circle at 20% 0%, color-mix(in oklab, var(--tag-blue-text) 10%, transparent), transparent 45%), var(--surface-card);">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--tag-blue-text);">Top campanhas</div>
                                    <div class="mt-1 text-xs text-(--text-muted)">Cliques vs impressões</div>
                                </div>
                            </div>

                            <div class="mt-4" wire:ignore>
                                <div id="campaign-top-chart" class="h-[260px]"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-5 shadow-(--shadow-sm)" style="background: radial-gradient(900px circle at 20% 0%, color-mix(in oklab, var(--status-success) 10%, transparent), transparent 45%), var(--surface-card);">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--status-info);">Status</div>
                                    <div class="mt-1 text-xs text-(--text-muted)">Distribuição</div>
                                </div>
                            </div>

                            <div class="mt-4" wire:ignore>
                                <div id="campaign-status-chart" class="h-[260px]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="w-full overflow-hidden rounded-lg border border-(--border-subtle) bg-(--surface-card) shadow-(--shadow-sm)">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-(--text-primary)">
                                <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-xs font-medium uppercase tracking-wider text-(--text-secondary)">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 font-semibold">{{ __('app.campaigns.table.name') }}</th>
                                        <th scope="col" class="px-6 py-3 font-semibold">{{ __('app.campaigns.table.channel') }}</th>
                                        <th scope="col" class="px-6 py-3 font-semibold">{{ __('app.campaigns.table.status') }}</th>
                                        <th scope="col" class="px-6 py-3 text-right font-semibold">{{ __('app.campaigns.table.impressions') }}</th>
                                        <th scope="col" class="px-6 py-3 text-right font-semibold">{{ __('app.campaigns.table.interactions') }}</th>
                                        <th scope="col" class="px-6 py-3 text-right font-semibold">{{ __('app.campaigns.table.ctr') }}</th>
                                        <th scope="col" class="px-6 py-3 text-right font-semibold">{{ __('app.campaigns.table.conversions') }}</th>
                                        <th scope="col" class="px-6 py-3 text-right font-semibold">{{ __('app.campaigns.table.cost') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-(--border-subtle) bg-(--surface-card)">
                                    @forelse ($rows as $row)
                                        @php($status = (string) data_get($row, 'status', ''))
                                        @php($cost = ((int) data_get($row, 'costMicros', 0)) / 1000000)
                                        @php($isActive = in_array(strtoupper($status), ['ENABLED', 'ACTIVE', 'ATIVA', 'ATIVO'], true))

                                        <tr class="border-b border-(--border-subtle) transition-colors hover:bg-(--surface-hover)">
                                            <td class="px-6 py-4 whitespace-nowrap text-(--text-primary)">
                                                <div class="text-sm font-medium">{{ data_get($row, 'name', __('app.common.dash')) }}</div>
                                                <div class="mt-1 text-xs text-(--text-muted)">ID: {{ data_get($row, 'id', __('app.common.dash')) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-(--text-secondary)">
                                                <div class="text-sm">{{ data_get($row, 'advertisingChannelType', __('app.common.dash')) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-(--text-secondary)">
                                                <x-ds::badge :variant="$isActive ? 'success' : 'secondary'">
                                                    {{ $status ?: __('app.common.dash') }}
                                                </x-ds::badge>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-(--text-secondary)">
                                                <div class="text-sm">{{ number_format((int) data_get($row, 'impressions', 0), 0, ',', '.') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-(--text-secondary)">
                                                <div class="text-sm">{{ number_format((int) data_get($row, 'interactions', 0), 0, ',', '.') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-(--text-secondary)">
                                                <div class="text-sm">{{ number_format(((float) data_get($row, 'ctr', 0)) * 100, 2, ',', '.') }}%</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-(--text-secondary)">
                                                <div class="text-sm">{{ number_format((float) data_get($row, 'conversions', 0), 0, ',', '.') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-(--text-secondary)">
                                                <div class="text-sm">R$ {{ number_format((float) $cost, 2, ',', '.') }}</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="py-8 text-center text-sm text-(--text-secondary)">
                                                {{ __('app.campaigns.dashboard.empty') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-ds::card>
</div>

@push('scripts')
    <script>
        (() => {
            let topChart = null;
            let statusChart = null;

            const cssVar = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();

            const safeTruncate = (s, max = 18) => {
                if (!s) return '';
                const str = String(s);
                return str.length > max ? str.slice(0, max - 1) + '…' : str;
            };

            const ensureApex = () => typeof window.ApexCharts !== 'undefined';

            const renderCharts = (payload) => {
                if (!ensureApex()) return;

                const data = payload?.data ?? payload;
                const top = data?.top ?? { categories: [], clicks: [], impressions: [] };
                const statusCounts = data?.status?.counts ?? { active: 0, paused: 0, removed: 0 };

                const categories = (top.categories ?? []).map((c) => safeTruncate(c, 22));
                const clicks = top.clicks ?? [];
                const impressions = top.impressions ?? [];

                const baseGrid = {
                    borderColor: cssVar('--border-subtle') || 'rgba(255,255,255,0.08)',
                    strokeDashArray: 3,
                };

                const foreColor = cssVar('--text-secondary') || '#9CA3AF';
                const tooltipTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';

                const topOptions = {
                    chart: {
                        type: 'bar',
                        height: 260,
                        toolbar: { show: false },
                        foreColor,
                        fontFamily: 'inherit',
                        animations: { enabled: true },
                    },
                    series: [
                        { name: 'Cliques', data: clicks },
                        { name: 'Impressões / 100', data: impressions.map((v) => Math.round((Number(v) || 0) / 100)) },
                    ],
                    colors: [cssVar('--status-info') || '#06B6D4', cssVar('--tag-purple-text') || '#A78BFA'],
                    plotOptions: {
                        bar: {
                            columnWidth: '46%',
                            borderRadius: 6,
                        },
                    },
                    dataLabels: { enabled: false },
                    grid: baseGrid,
                    xaxis: {
                        categories,
                        labels: { rotate: -35, style: { fontSize: '10px' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: {
                        labels: { style: { fontSize: '10px' } },
                    },
                    legend: {
                        show: true,
                        position: 'top',
                        horizontalAlign: 'center',
                        fontSize: '11px',
                        labels: { colors: foreColor },
                        markers: { radius: 3 },
                    },
                    tooltip: { theme: tooltipTheme },
                };

                const statusOptions = {
                    chart: {
                        type: 'donut',
                        height: 260,
                        toolbar: { show: false },
                        foreColor,
                        fontFamily: 'inherit',
                    },
                    labels: ['Ativas', 'Pausadas', 'Removidas'],
                    series: [
                        Number(statusCounts.active || 0),
                        Number(statusCounts.paused || 0),
                        Number(statusCounts.removed || 0),
                    ],
                    colors: [
                        cssVar('--status-success') || '#10B981',
                        cssVar('--status-warning') || '#F59E0B',
                        cssVar('--status-error') || '#EF4444',
                    ],
                    dataLabels: { enabled: false },
                    legend: {
                        position: 'bottom',
                        fontSize: '11px',
                        labels: { colors: foreColor },
                    },
                    stroke: {
                        width: 2,
                        colors: [cssVar('--surface-card') || '#0B1220'],
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                            },
                        },
                    },
                    tooltip: { theme: tooltipTheme },
                };

                const topEl = document.getElementById('campaign-top-chart');
                const statusEl = document.getElementById('campaign-status-chart');

                if (topEl) {
                    if (!topChart) {
                        topChart = new window.ApexCharts(topEl, topOptions);
                        topChart.render();
                    } else {
                        topChart.updateOptions(topOptions, true, true);
                    }
                }

                if (statusEl) {
                    if (!statusChart) {
                        statusChart = new window.ApexCharts(statusEl, statusOptions);
                        statusChart.render();
                    } else {
                        statusChart.updateOptions(statusOptions, true, true);
                    }
                }
            };

            const bind = () => {
                if (!window.Livewire?.on) return;
                window.Livewire.on('campaign-dashboard-data', (payload) => {
                    renderCharts(payload);
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', bind, { once: true });
            } else {
                bind();
            }

            document.addEventListener('livewire:navigated', () => {
                bind();
            });
        })();
    </script>
@endpush
