<div class="space-y-6" wire:init="loadData" x-data x-on:campaign-tab-activated.window="$wire.loadData(); window.dispatchEvent(new CustomEvent('ds-chart-rerender'))">
    <x-ds::card>
        {{-- ── Header ── --}}
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

                <div class="w-full sm:max-w-xs">
                    <x-ds::select
                        label="Plataforma"
                        wire:model.live="platform"
                        :options="['all' => 'Geral', 'google' => 'Google', 'meta' => 'Meta']"
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
            {{-- ── Alerts ── --}}
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

            {{-- ── Data section (loading overlay) ── --}}
            <div class="relative mt-6">
                <div
                    wire:loading
                    wire:target="loadData"
                    class="absolute inset-0 z-10 flex items-center justify-center rounded-xl"
                    style="background: color-mix(in oklab, var(--surface-card) 85%, transparent); backdrop-filter: blur(3px);"
                >
                    <div class="flex flex-col items-center gap-3">
                        <x-ds::spinner size="lg" variant="primary" />
                        <span class="text-sm font-medium text-(--text-secondary)">{{ __('app.campaigns.dashboard.loading') }}</span>
                    </div>
                </div>

                @php
                    $k = $this->kpis;
                    $rows = $this->filteredAds;

                    $campaignsTotal  = is_iterable($rows) ? count($rows) : 0;
                    $campaignsActive = is_iterable($rows)
                        ? count(array_filter($rows, fn ($r) => in_array(strtoupper((string) data_get($r, 'campaign_status', '')), ['ENABLED', 'ACTIVE', 'ATIVA', 'ATIVO'], true)))
                        : 0;
                @endphp

                {{-- ══ PRIMARY KPI CARDS ══ --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                    {{-- Spend --}}
                    <div class="relative overflow-hidden rounded-xl border border-(--border-subtle) bg-(--surface-card) p-5"
                         style="box-shadow: var(--shadow-sm); border-top: 3px solid var(--color-primary); background: radial-gradient(600px circle at 10% 0%, color-mix(in oklab, var(--color-primary) 10%, transparent), transparent 50%), var(--surface-card);">
                        <div class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-lg"
                             style="background: color-mix(in oklab, var(--color-primary) 14%, transparent);">
                            <iconify-icon icon="solar:dollar-minimalistic-linear" style="color: var(--color-primary); font-size: 18px;"></iconify-icon>
                        </div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.metrics.cost') }}</div>
                        <div class="mt-2 text-3xl font-bold leading-none text-(--text-primary)">R$ {{ number_format((float) ($k['spend'] ?? 0), 2, ',', '.') }}</div>
                        <div class="mt-3 flex items-center gap-1.5">
                            <span class="text-xs text-(--text-muted)">{{ __('app.campaigns.metrics.cpc') }}</span>
                            <span class="text-xs font-semibold text-(--text-primary)">
                                {{ array_key_exists('cpc', $k) && $k['cpc'] !== null ? 'R$ ' . number_format((float) $k['cpc'], 2, ',', '.') : '—' }}
                            </span>
                        </div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>

                    {{-- Impressions --}}
                    <div class="relative overflow-hidden rounded-xl border border-(--border-subtle) bg-(--surface-card) p-5"
                         style="box-shadow: var(--shadow-sm); border-top: 3px solid var(--status-info); background: radial-gradient(600px circle at 10% 0%, color-mix(in oklab, var(--status-info) 10%, transparent), transparent 50%), var(--surface-card);">
                        <div class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-lg"
                             style="background: color-mix(in oklab, var(--status-info) 14%, transparent);">
                            <iconify-icon icon="solar:eye-linear" style="color: var(--status-info); font-size: 18px;"></iconify-icon>
                        </div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.metrics.impressions') }}</div>
                        <div class="mt-2 text-3xl font-bold leading-none text-(--text-primary)">{{ number_format((int) ($k['impressions'] ?? 0), 0, ',', '.') }}</div>
                        <div class="mt-3 flex items-center gap-1.5">
                            <span class="text-xs text-(--text-muted)">CPM</span>
                            <span class="text-xs font-semibold text-(--text-primary)">
                                {{ array_key_exists('cpm', $k) && $k['cpm'] !== null ? 'R$ ' . number_format((float) $k['cpm'], 2, ',', '.') : '—' }}
                            </span>
                        </div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>

                    {{-- Interactions / Clicks --}}
                    <div class="relative overflow-hidden rounded-xl border border-(--border-subtle) bg-(--surface-card) p-5"
                         style="box-shadow: var(--shadow-sm); border-top: 3px solid var(--tag-purple-text); background: radial-gradient(600px circle at 10% 0%, color-mix(in oklab, var(--tag-purple-text) 10%, transparent), transparent 50%), var(--surface-card);">
                        <div class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-lg"
                             style="background: color-mix(in oklab, var(--tag-purple-text) 14%, transparent);">
                            <iconify-icon icon="solar:cursor-linear" style="color: var(--tag-purple-text); font-size: 18px;"></iconify-icon>
                        </div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.table.interactions') }}</div>
                        <div class="mt-2 text-3xl font-bold leading-none text-(--text-primary)">{{ number_format((int) ($k['clicks'] ?? 0), 0, ',', '.') }}</div>
                        <div class="mt-3 flex items-center gap-1.5">
                            <span class="text-xs text-(--text-muted)">CTR</span>
                            <span class="text-xs font-semibold text-(--text-primary)">{{ number_format((float) ($k['ctr'] ?? 0), 2, ',', '.') }}%</span>
                        </div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>

                    {{-- Conversions --}}
                    <div class="relative overflow-hidden rounded-xl border border-(--border-subtle) bg-(--surface-card) p-5"
                         style="box-shadow: var(--shadow-sm); border-top: 3px solid var(--status-success); background: radial-gradient(600px circle at 10% 0%, color-mix(in oklab, var(--status-success) 10%, transparent), transparent 50%), var(--surface-card);">
                        <div class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-lg"
                             style="background: color-mix(in oklab, var(--status-success) 14%, transparent);">
                            <iconify-icon icon="solar:target-linear" style="color: var(--status-success); font-size: 18px;"></iconify-icon>
                        </div>
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ $k['extra_label'] ?? __('app.campaigns.metrics.conversions') }}</div>
                        <div class="mt-2 text-3xl font-bold leading-none text-(--text-primary)">{{ number_format((float) ($k['extra_value'] ?? ($k['conversions'] ?? 0)), 0, ',', '.') }}</div>
                        <div class="mt-3 flex items-center gap-1.5">
                            <span class="text-xs text-(--text-muted)">{{ __('app.campaigns.metrics.cpa') }}</span>
                            <span class="text-xs font-semibold text-(--text-primary)">
                                {{ array_key_exists('cost_per_conversion', $k) && $k['cost_per_conversion'] !== null ? 'R$ ' . number_format((float) $k['cost_per_conversion'], 2, ',', '.') : '—' }}
                            </span>
                        </div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>
                </div>

                {{-- ══ SECONDARY KPI ROW ══ --}}
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">

                    {{-- Total Campaigns --}}
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4" style="box-shadow: var(--shadow-xs);">
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.dashboard.table_title') }}</div>
                        <div class="mt-1.5 text-2xl font-bold text-(--text-primary)">{{ number_format((int) $campaignsTotal, 0, ',', '.') }}</div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.dashboard.total_of', ['total' => $campaignsTotal]) }}</div>
                    </div>

                    {{-- Active Campaigns --}}
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4" style="box-shadow: var(--shadow-xs);">
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.dashboard.status.active') }}</div>
                        <div class="mt-1.5 text-2xl font-bold" style="color: var(--status-success);">{{ number_format((int) $campaignsActive, 0, ',', '.') }}</div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.dashboard.total_of', ['total' => $campaignsTotal]) }}</div>
                    </div>

                    {{-- CPM --}}
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4" style="box-shadow: var(--shadow-xs);">
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">CPM</div>
                        <div class="mt-1.5 text-2xl font-bold text-(--text-primary)">
                            {{ array_key_exists('cpm', $k) && $k['cpm'] !== null ? 'R$ ' . number_format((float) $k['cpm'], 2, ',', '.') : '—' }}
                        </div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>

                    {{-- CPC --}}
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4" style="box-shadow: var(--shadow-xs);">
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.metrics.cpc') }}</div>
                        <div class="mt-1.5 text-2xl font-bold text-(--text-primary)">
                            {{ array_key_exists('cpc', $k) && $k['cpc'] !== null ? 'R$ ' . number_format((float) $k['cpc'], 2, ',', '.') : '—' }}
                        </div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>

                    {{-- Cost per conversion --}}
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4" style="box-shadow: var(--shadow-xs);">
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.metrics.cpa') }}</div>
                        <div class="mt-1.5 text-2xl font-bold text-(--text-primary)">
                            {{ array_key_exists('cost_per_conversion', $k) && $k['cost_per_conversion'] !== null ? 'R$ ' . number_format((float) $k['cost_per_conversion'], 2, ',', '.') : '—' }}
                        </div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>

                    {{-- CTR --}}
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4" style="box-shadow: var(--shadow-xs);">
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.table.ctr') }}</div>
                        <div class="mt-1.5 text-2xl font-bold text-(--text-primary)">{{ number_format((float) ($k['ctr'] ?? 0), 2, ',', '.') }}%</div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>

                    {{-- Clicks --}}
                    <div class="rounded-lg border border-(--border-subtle) bg-(--surface-card) p-4" style="box-shadow: var(--shadow-xs);">
                        <div class="text-[10px] font-semibold uppercase tracking-wider text-(--text-muted)">{{ __('app.campaigns.table.interactions') }}</div>
                        <div class="mt-1.5 text-2xl font-bold text-(--text-primary)">{{ number_format((int) ($k['clicks'] ?? 0), 0, ',', '.') }}</div>
                        <div class="mt-1 text-[11px] text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                    </div>
                </div>

                {{-- ══ CHARTS ROW 1: Top Clicks (bar) + Status (donut) ══ --}}
                @php
                    $chart = is_array($chartData ?? null) ? $chartData : [];
                    $top   = $chart['top'] ?? ['categories' => [], 'clicks' => [], 'impressions_100' => []];

                    $topCategories   = array_map(fn ($c) => (string) $c, (array) ($top['categories'] ?? []));
                    $clicks          = array_map(fn ($v) => (int) $v, (array) ($top['clicks'] ?? []));
                    $impressions100  = array_map(fn ($v) => (int) $v, (array) ($top['impressions_100'] ?? []));

                    $topSeries = [
                        ['name' => __('app.campaigns.dashboard.series.clicks'), 'data' => $clicks],
                        ['name' => __('app.campaigns.dashboard.series.impressions_100'), 'data' => $impressions100],
                    ];

                    $topOptions = [
                        'colors'      => ['var(--status-info)', 'var(--tag-purple-text)'],
                        'plotOptions' => ['bar' => ['columnWidth' => '46%', 'borderRadius' => 5]],
                        'dataLabels'  => ['enabled' => false],
                        'grid'        => ['strokeDashArray' => 3, 'padding' => ['left' => 4, 'right' => 4]],
                        'legend'      => ['show' => true, 'position' => 'top', 'horizontalAlign' => 'center'],
                        'xaxis'       => [
                            'categories' => $topCategories,
                            'labels'     => ['rotate' => -35, 'style' => ['fontSize' => '10px']],
                            'axisBorder' => ['show' => false],
                            'axisTicks'  => ['show' => false],
                        ],
                        'yaxis'   => ['labels' => ['style' => ['fontSize' => '10px']]],
                        'tooltip' => ['shared' => true],
                    ];

                    $statusCounts = (array) data_get($chart, 'status.counts', ['active' => 0, 'paused' => 0, 'removed' => 0]);
                    $statusSeries = [
                        (int) ($statusCounts['active'] ?? 0),
                        (int) ($statusCounts['paused'] ?? 0),
                        (int) ($statusCounts['removed'] ?? 0),
                    ];
                @endphp

                <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <x-ds::card
                            class="h-full"
                            title="{{ __('app.campaigns.dashboard.top_title') }}"
                            description="{{ __('app.campaigns.dashboard.top_subtitle') }}"
                        >
                            <div wire:ignore class="w-full">
                                <div id="campaign-top-chart" style="height: 280px; width: 100%;"></div>
                            </div>
                        </x-ds::card>
                    </div>

                    <div>
                        <x-ds::card
                            class="h-full"
                            title="{{ __('app.campaigns.dashboard.status_title') }}"
                            description="{{ __('app.campaigns.dashboard.status_subtitle') }}"
                        >
                            <div wire:ignore class="w-full">
                                <div id="campaign-status-chart" style="height: 280px; width: 100%;"></div>
                            </div>
                        </x-ds::card>
                    </div>
                </div>

                {{-- ══ CHARTS ROW 2: Top Cost (horizontal bar) + Channel (donut) ══ --}}
                @php
                    $topCostData    = $chart['topCost'] ?? ['categories' => [], 'values' => []];
                    $costCategories = (array) ($topCostData['categories'] ?? []);
                    $costValues     = array_map(fn ($v) => (float) $v, (array) ($topCostData['values'] ?? []));

                    $channelData   = $chart['channel'] ?? ['labels' => [], 'counts' => []];
                    $channelLabels = (array) ($channelData['labels'] ?? []);
                    $channelCounts = array_map(fn ($v) => (int) $v, (array) ($channelData['counts'] ?? []));
                @endphp

                <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <x-ds::card
                            class="h-full"
                            title="{{ __('app.campaigns.dashboard.cost_title') }}"
                            description="{{ __('app.campaigns.dashboard.cost_subtitle') }}"
                        >
                            <div wire:ignore class="w-full">
                                <div id="campaign-cost-chart" style="height: 280px; width: 100%;"></div>
                            </div>
                        </x-ds::card>
                    </div>

                    <div>
                        <x-ds::card
                            class="h-full"
                            title="{{ __('app.campaigns.dashboard.channel_title') }}"
                            description="{{ __('app.campaigns.dashboard.channel_subtitle') }}"
                        >
                            <div wire:ignore class="w-full">
                                <div id="campaign-channel-chart" style="height: 280px; width: 100%;"></div>
                            </div>
                        </x-ds::card>
                    </div>
                </div>

                {{-- ══ CHARTS ROW 3: Mixed (Conversions + Conv. Rate) ══ --}}
                @php
                    $convData = $chart['conv'] ?? ['categories' => [], 'values' => [], 'rate_pct' => []];
                    $convCategories = (array) ($convData['categories'] ?? []);
                    $convValues = array_map(fn ($v) => (float) $v, (array) ($convData['values'] ?? []));
                    $convRatePct = array_map(fn ($v) => (float) $v, (array) ($convData['rate_pct'] ?? []));

                    $convSeries = [
                        [
                            'name' => __('app.campaigns.metrics.conversions'),
                            'type' => 'column',
                            'data' => $convValues,
                        ],
                        [
                            'name' => __('app.campaigns.table.conversions_rate'),
                            'type' => 'line',
                            'data' => $convRatePct,
                        ],
                    ];

                    $convOptions = [
                        'colors' => ['var(--tag-orange-text)', 'var(--status-success)'],
                        'stroke' => ['width' => [0, 3], 'curve' => 'smooth'],
                        'markers' => ['size' => [0, 4]],
                        'plotOptions' => ['bar' => ['borderRadius' => 5, 'columnWidth' => '50%']],
                        'grid' => ['strokeDashArray' => 3, 'padding' => ['left' => 4, 'right' => 12]],
                        'legend' => ['show' => true, 'position' => 'top', 'horizontalAlign' => 'center'],
                        'xaxis' => [
                            'categories' => $convCategories,
                            'labels'     => ['rotate' => -35, 'style' => ['fontSize' => '10px']],
                            'axisBorder' => ['show' => false],
                            'axisTicks'  => ['show' => false],
                        ],
                        'yaxis' => [
                            ['labels' => ['style' => ['fontSize' => '10px']]],
                            ['opposite' => true, 'labels' => ['style' => ['fontSize' => '10px']]],
                        ],
                        'tooltip' => ['shared' => true],
                        '__formatters' => [
                            'yaxis' => ['0' => 'number0', '1' => 'percent2'],
                            'tooltipY' => ['0' => 'number0', '1' => 'percent2', 'default' => 'number2'],
                        ],
                    ];
                @endphp

                <div class="mt-4">
                    <x-ds::card
                        title="{{ __('app.campaigns.dashboard.conversions_title') }}"
                        description="{{ __('app.campaigns.dashboard.conversions_subtitle') }}"
                    >
                        <div wire:ignore class="w-full">
                            <div id="campaign-conv-chart" style="height: 280px; width: 100%;"></div>
                        </div>
                    </x-ds::card>
                </div>

                {{-- ══ TABLE ══ --}}
                <div class="mt-4">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-(--text-primary)">{{ __('app.campaigns.dashboard.table_title') }}</div>
                            <div class="mt-0.5 text-xs text-(--text-muted)">{{ __('app.campaigns.metrics.period', ['period' => $period]) }}</div>
                        </div>
                        <div wire:loading wire:target="loadData">
                            <x-ds::spinner size="sm" variant="info" />
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-(--border-subtle) bg-(--surface-card)" style="box-shadow: var(--shadow-sm);">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-(--border-subtle) bg-(--surface-hover) text-[11px] font-semibold uppercase tracking-wider text-(--text-secondary)">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">Plataforma</th>
                                        <th class="px-4 py-3 font-semibold">{{ __('app.campaigns.table.name') }}</th>
                                        <th class="px-4 py-3 font-semibold">{{ __('app.campaigns.table.channel') }}</th>
                                        <th class="px-4 py-3 font-semibold">{{ __('app.campaigns.table.status') }}</th>
                                        <th class="px-4 py-3 text-right font-semibold">{{ __('app.campaigns.table.impressions') }}</th>
                                        <th class="px-4 py-3 text-right font-semibold">{{ __('app.campaigns.table.interactions') }}</th>
                                        <th class="px-4 py-3 text-right font-semibold">{{ __('app.campaigns.table.ctr') }}</th>
                                        <th class="px-4 py-3 text-right font-semibold">{{ __('app.campaigns.table.conversions') }}</th>
                                        <th class="px-4 py-3 text-right font-semibold">{{ __('app.campaigns.table.cost') }}</th>
                                        <th class="px-4 py-3 text-right font-semibold">CPM</th>
                                        <th class="px-4 py-3 text-right font-semibold">CPC</th>
                                        <th class="px-4 py-3 text-right font-semibold">{{ __('app.campaigns.metrics.cpa') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-(--border-subtle)">
                                    @forelse ($rows as $row)
                                        @php
                                            $source = (string) data_get($row, 'source', '');
                                            $status = (string) data_get($row, 'campaign_status', '');

                                            $rowCost = (float) data_get($row, 'spend', 0);
                                            $rowImpressions = (int) data_get($row, 'impressions', 0);
                                            $rowClicks = (int) data_get($row, 'clicks', 0);
                                            $rowCtr = (float) data_get($row, 'ctr', 0);
                                            $rowCpm = data_get($row, 'cpm');
                                            $rowCpc = data_get($row, 'cpc');
                                            $rowCpa = data_get($row, 'cost_per_conversion');

                                            $isActive       = in_array(strtoupper($status), ['ENABLED', 'ACTIVE', 'ATIVA', 'ATIVO'], true);
                                            $platformLabel = $source === 'google_ads' ? 'Google' : ($source === 'meta_ads' ? 'Meta' : '—');
                                            $platformVariant = $source === 'google_ads' ? 'info' : ($source === 'meta_ads' ? 'secondary' : 'secondary');
                                            $rowName = (string) data_get($row, 'campaign_name', data_get($row, 'ad_name', '—'));
                                            $rowChannel = (string) data_get($row, 'channel_type', data_get($row, 'objective', '—'));
                                        @endphp
                                        <tr class="transition-colors hover:bg-(--surface-hover)">
                                            {{-- Platform --}}
                                            <td class="px-4 py-3">
                                                <x-ds::badge :variant="$platformVariant">{{ $platformLabel }}</x-ds::badge>
                                            </td>
                                            {{-- Name + ID + Opt Score --}}
                                            <td class="px-4 py-3">
                                                <div class="max-w-[220px]">
                                                    <div class="truncate text-sm font-medium text-(--text-primary)">
                                                        {{ $rowName }}
                                                    </div>
                                                    <div class="mt-0.5 text-[11px] text-(--text-muted)">{{ $rowChannel }}</div>
                                                </div>
                                            </td>
                                            {{-- Channel --}}
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center rounded px-2 py-1 text-[11px] font-medium text-(--text-secondary)"
                                                      style="background: var(--surface-hover);">
                                                    {{ $rowChannel }}
                                                </span>
                                            </td>
                                            {{-- Status --}}
                                            <td class="px-4 py-3">
                                                <x-ds::badge :variant="$isActive ? 'success' : 'secondary'">
                                                    {{ $status ?: '—' }}
                                                </x-ds::badge>
                                            </td>
                                            {{-- Impressions --}}
                                            <td class="px-4 py-3 text-right text-sm tabular-nums text-(--text-secondary)">
                                                {{ number_format($rowImpressions, 0, ',', '.') }}
                                            </td>
                                            {{-- Interactions --}}
                                            <td class="px-4 py-3 text-right text-sm tabular-nums text-(--text-secondary)">
                                                {{ number_format($rowClicks, 0, ',', '.') }}
                                            </td>
                                            {{-- CTR --}}
                                            <td class="px-4 py-3 text-right text-sm tabular-nums text-(--text-secondary)">
                                                {{ number_format($rowCtr, 2, ',', '.') }}%
                                            </td>
                                            {{-- Conversions --}}
                                            <td class="px-4 py-3 text-right text-sm tabular-nums text-(--text-secondary)">
                                                {{ number_format((float) data_get($row, 'conversions', 0), 0, ',', '.') }}
                                            </td>
                                            {{-- Cost --}}
                                            <td class="px-4 py-3 text-right">
                                                <span class="text-sm font-semibold tabular-nums text-(--text-primary)">
                                                    R$ {{ number_format($rowCost, 2, ',', '.') }}
                                                </span>
                                            </td>

                                            {{-- CPM --}}
                                            <td class="px-4 py-3 text-right text-sm tabular-nums text-(--text-secondary)">
                                                {{ $rowCpm !== null ? 'R$ ' . number_format((float) $rowCpm, 2, ',', '.') : '—' }}
                                            </td>

                                            {{-- CPC --}}
                                            <td class="px-4 py-3 text-right text-sm tabular-nums text-(--text-secondary)">
                                                {{ $rowCpc !== null ? 'R$ ' . number_format((float) $rowCpc, 2, ',', '.') : '—' }}
                                            </td>

                                            {{-- CPA --}}
                                            <td class="px-4 py-3 text-right text-sm tabular-nums text-(--text-secondary)">
                                                {{ $rowCpa !== null ? 'R$ ' . number_format((float) $rowCpa, 2, ',', '.') : '—' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="py-14 text-center">
                                                <div class="flex flex-col items-center gap-3">
                                                    <iconify-icon icon="solar:chart-2-linear" style="color: var(--text-muted); font-size: 36px;"></iconify-icon>
                                                    <span class="text-sm text-(--text-secondary)">{{ __('app.campaigns.dashboard.empty') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>{{-- /relative --}}
        </div>
    </x-ds::card>
</div>

@push('scripts')
    <script>
        (() => {
            try { console.log('[campaign-charts] boot'); } catch (e) {}

            const i18n = {
                clicks: @js(__('app.campaigns.dashboard.series.clicks')),
                impressions100: @js(__('app.campaigns.dashboard.series.impressions_100')),
                cost: @js(__('app.campaigns.dashboard.series.cost')),
                statusLabels: @js([
                    __('app.campaigns.dashboard.status.active'),
                    __('app.campaigns.dashboard.status.paused'),
                    __('app.campaigns.dashboard.status.removed'),
                ]),
            };

            let lastData = @js($chartData ?? []);

            const ensureApex = (() => {
                let promise = null;
                return () => {
                    if (window.ApexCharts) return Promise.resolve(true);
                    if (promise) return promise;

                    promise = new Promise((resolve) => {
                        try { console.warn('[campaign-charts] ApexCharts not found, loading from CDN...'); } catch (e) {}

                        const existing = document.querySelector('script[data-campaign-apex]');
                        if (existing) {
                            existing.addEventListener('load', () => resolve(true), { once: true });
                            existing.addEventListener('error', () => resolve(false), { once: true });
                            return;
                        }

                        const s = document.createElement('script');
                        s.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
                        s.async = true;
                        s.defer = true;
                        s.dataset.campaignApex = '1';
                        s.onload = () => {
                            try { console.log('[campaign-charts] ApexCharts loaded'); } catch (e) {}
                            resolve(!!window.ApexCharts);
                        };
                        s.onerror = () => {
                            try { console.error('[campaign-charts] Failed to load ApexCharts'); } catch (e) {}
                            resolve(false);
                        };
                        document.head.appendChild(s);
                    });

                    return promise;
                };
            })();

            const baseOptions = () => {
                const isDark = document.documentElement.classList.contains('dark');
                const mode = isDark ? 'dark' : 'light';

                return {
                    chart: {
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        fontFamily: 'var(--font-family-base)',
                        foreColor: 'var(--text-secondary)'
                    },
                    stroke: { width: 3, curve: 'smooth' },
                    grid: {
                        borderColor: 'var(--border-subtle)',
                        strokeDashArray: 4,
                        padding: { top: 0, right: 8, bottom: 0, left: 8 }
                    },
                    dataLabels: { enabled: false },
                    tooltip: { theme: mode, intersect: false },
                    theme: { mode }
                };
            };

            const deepMerge = (target, source) => {
                if (!source || typeof source !== 'object') return target;
                for (const key of Object.keys(source)) {
                    const v = source[key];
                    if (Array.isArray(v)) target[key] = v;
                    else if (v && typeof v === 'object') target[key] = deepMerge({ ...(target[key] ?? {}) }, v);
                    else target[key] = v;
                }
                return target;
            };

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

            const applyNamedFormatters = (cfg) => {
                const fmts = cfg?.__formatters;
                if (!fmts || typeof fmts !== 'object') return cfg;
                try { delete cfg.__formatters; } catch (e) {}

                if (fmts.yaxis && Array.isArray(cfg.yaxis)) {
                    cfg.yaxis = cfg.yaxis.map((y, i) => {
                        const yy = (y && typeof y === 'object') ? { ...y } : {};
                        const chosen = (typeof fmts.yaxis === 'object' && !Array.isArray(fmts.yaxis))
                            ? (fmts.yaxis[i] ?? fmts.yaxis[String(i)] ?? fmts.yaxis.default)
                            : fmts.yaxis;
                        const fn = getFormatter(chosen);
                        if (fn) {
                            yy.labels = yy.labels ?? {};
                            yy.labels.formatter = fn;
                        }
                        return yy;
                    });
                }

                if (fmts.tooltipY) {
                    cfg.tooltip = cfg.tooltip ?? {};
                    cfg.tooltip.y = cfg.tooltip.y ?? {};

                    if (typeof fmts.tooltipY === 'object' && !Array.isArray(fmts.tooltipY)) {
                        cfg.tooltip.y.formatter = (value, ctx) => {
                            const idx = ctx?.seriesIndex ?? 0;
                            const chosen = fmts.tooltipY[idx] ?? fmts.tooltipY[String(idx)] ?? fmts.tooltipY.default;
                            const fn = getFormatter(chosen);
                            return fn ? fn(value) : value;
                        };
                    } else {
                        const fn = getFormatter(fmts.tooltipY);
                        if (fn) cfg.tooltip.y.formatter = fn;
                    }
                }

                if (fmts.dataLabels) {
                    const fn = getFormatter(fmts.dataLabels);
                    if (fn) {
                        cfg.dataLabels = cfg.dataLabels ?? {};
                        cfg.dataLabels.formatter = fn;
                    }
                }

                return cfg;
            };

            const registry = (window.__campaignApexRegistry = window.__campaignApexRegistry ?? {});

            const renderOne = (id, def) => {
                if (!window.ApexCharts) return;
                const el = document.getElementById(id);
                if (!el || !el.isConnected) return;
                if (el.clientWidth === 0 || el.clientHeight === 0) return;

                if (registry[id]) {
                    try { registry[id].destroy(); } catch (e) {}
                    delete registry[id];
                }

                const cfg = deepMerge(baseOptions(), def.options ?? {});
                cfg.chart = cfg.chart ?? {};
                cfg.chart.type = def.type;
                cfg.chart.height = def.height ?? 280;
                cfg.series = def.series ?? [];

                cfg.tooltip = cfg.tooltip ?? {};
                if (cfg.tooltip.shared === true) cfg.tooltip.intersect = false;

                applyNamedFormatters(cfg);

                try {
                    registry[id] = new ApexCharts(el, cfg);
                    registry[id].render();
                } catch (e) {}
            };

            const renderAll = () => {
                const data = (lastData && typeof lastData === 'object') ? lastData : {};

                const top = data.top ?? {};
                const topCategories = Array.isArray(top.categories) ? top.categories.map(String) : [];
                const clicks = Array.isArray(top.clicks) ? top.clicks.map((v) => Number(v) || 0) : [];
                const impr100 = Array.isArray(top.impressions_100) ? top.impressions_100.map((v) => Number(v) || 0) : [];

                const statusCounts = data.status?.counts ?? {};
                const statusSeries = [
                    Number(statusCounts.active) || 0,
                    Number(statusCounts.paused) || 0,
                    Number(statusCounts.removed) || 0,
                ];

                const topCost = data.topCost ?? {};
                const costCategories = Array.isArray(topCost.categories) ? topCost.categories.map(String) : [];
                const costValues = Array.isArray(topCost.values) ? topCost.values.map((v) => Number(v) || 0) : [];

                const channel = data.channel ?? {};
                const channelLabels = Array.isArray(channel.labels) ? channel.labels.map(String) : [];
                const channelCounts = Array.isArray(channel.counts) ? channel.counts.map((v) => Number(v) || 0) : [];

                const conv = data.conv ?? {};
                const convCategories = Array.isArray(conv.categories) ? conv.categories.map(String) : [];
                const convValues = Array.isArray(conv.values) ? conv.values.map((v) => Number(v) || 0) : [];
                const convRatePct = Array.isArray(conv.rate_pct) ? conv.rate_pct.map((v) => Number(v) || 0) : [];

                const defs = {
                    top: {
                        id: 'campaign-top-chart',
                        type: 'bar',
                        height: 280,
                        series: [
                            { name: i18n.clicks, data: clicks },
                            { name: i18n.impressions100, data: impr100 },
                        ],
                        options: {
                            colors: ['var(--status-info)', 'var(--tag-purple-text)'],
                            plotOptions: { bar: { columnWidth: '46%', borderRadius: 5 } },
                            dataLabels: { enabled: false },
                            grid: { strokeDashArray: 3, padding: { left: 4, right: 4 } },
                            legend: { show: true, position: 'top', horizontalAlign: 'center' },
                            xaxis: {
                                categories: topCategories,
                                labels: { rotate: -35, style: { fontSize: '10px' } },
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                            },
                            yaxis: { labels: { style: { fontSize: '10px' } } },
                            tooltip: { shared: true },
                        },
                    },
                    status: {
                        id: 'campaign-status-chart',
                        type: 'donut',
                        height: 280,
                        series: statusSeries,
                        options: {
                            labels: i18n.statusLabels,
                            colors: ['var(--status-success)', 'var(--status-warning)', 'var(--status-error)'],
                            legend: { show: true, position: 'bottom' },
                            dataLabels: { enabled: false },
                            plotOptions: { pie: { donut: { size: '70%' } } },
                            stroke: { width: 2, colors: ['var(--surface-card)'] },
                        },
                    },
                    cost: {
                        id: 'campaign-cost-chart',
                        type: 'bar',
                        height: 280,
                        series: [{ name: i18n.cost, data: costValues }],
                        options: {
                            colors: ['var(--color-primary)'],
                            plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '58%' } },
                            dataLabels: { enabled: false },
                            grid: { strokeDashArray: 3, padding: { left: 0, right: 12 } },
                            xaxis: {
                                categories: costCategories,
                                labels: { style: { fontSize: '10px' } },
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                            },
                            yaxis: { labels: { style: { fontSize: '10px' }, maxWidth: 140 } },
                            tooltip: { shared: false },
                            __formatters: {
                                yaxis: 'currencyBRL',
                                tooltipY: 'currencyBRL',
                            },
                        },
                    },
                    channel: {
                        id: 'campaign-channel-chart',
                        type: 'donut',
                        height: 280,
                        series: channelCounts,
                        options: {
                            labels: channelLabels,
                            colors: ['var(--status-info)', 'var(--tag-purple-text)', 'var(--status-warning)', 'var(--tag-orange-text)', 'var(--status-success)'],
                            legend: { show: true, position: 'bottom' },
                            dataLabels: { enabled: false },
                            plotOptions: { pie: { donut: { size: '70%' } } },
                            stroke: { width: 2, colors: ['var(--surface-card)'] },
                        },
                    },
                    conv: {
                        id: 'campaign-conv-chart',
                        type: 'line',
                        height: 280,
                        series: [
                            { name: @js(__('app.campaigns.metrics.conversions')), type: 'column', data: convValues },
                            { name: @js(__('app.campaigns.table.conversions_rate')), type: 'line', data: convRatePct },
                        ],
                        options: {
                            colors: ['var(--tag-orange-text)', 'var(--status-success)'],
                            stroke: { width: [0, 3], curve: 'smooth' },
                            markers: { size: [0, 4] },
                            plotOptions: { bar: { borderRadius: 5, columnWidth: '50%' } },
                            grid: { strokeDashArray: 3, padding: { left: 4, right: 12 } },
                            legend: { show: true, position: 'top', horizontalAlign: 'center' },
                            xaxis: {
                                categories: convCategories,
                                labels: { rotate: -35, style: { fontSize: '10px' } },
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                            },
                            yaxis: [
                                { labels: { style: { fontSize: '10px' } } },
                                { opposite: true, labels: { style: { fontSize: '10px' } } },
                            ],
                            tooltip: { shared: true },
                            __formatters: {
                                yaxis: { 0: 'number0', 1: 'percent2' },
                                tooltipY: { 0: 'number0', 1: 'percent2', default: 'number2' },
                            },
                        },
                    },
                };

                Object.values(defs).forEach((d) => renderOne(d.id, d));
            };

            const schedule = () => {
                let tries = 0;
                const t = setInterval(() => {
                    tries++;

                    if (tries === 1) {
                        ensureApex().then(() => {
                            // no-op; schedule loop will render when available
                        });
                    }

                    renderAll();
                    const any = ['campaign-top-chart','campaign-status-chart','campaign-cost-chart','campaign-channel-chart','campaign-conv-chart']
                        .some((id) => {
                            const el = document.getElementById(id);
                            return !!el && el.querySelector('svg');
                        });
                    if (any || tries > 60) clearInterval(t);
                }, 250);
            };

            if (!window.__campaignChartsBooted) {
                window.__campaignChartsBooted = true;

                document.addEventListener('livewire:init', () => {
                    schedule();
                    try {
                        Livewire.hook('message.processed', () => schedule());
                    } catch (e) {}
                });

                window.addEventListener('campaign-dashboard-data', (e) => {
                    try {
                        const d = e?.detail?.data ?? e?.detail ?? null;
                        if (d && typeof d === 'object') {
                            lastData = d;
                            try { console.log('[campaign-charts] data event', d); } catch (ee) {}
                            schedule();
                        }
                    } catch (err) {}
                });

                document.addEventListener('livewire:navigated', () => schedule());
                window.addEventListener('campaign-tab-activated', () => schedule());
                window.addEventListener('ds-chart-rerender', () => schedule());
                window.addEventListener('resize', () => schedule());
            }

            schedule();
        })();
    </script>
@endpush
