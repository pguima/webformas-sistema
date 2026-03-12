@extends('layouts.layout-ds')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-8">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-(--ds-foreground)">
                    {{ __('ds.pages.charts.title') }}
                </h1>
                <p class="mt-1 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.charts.subtitle') }}
                </p>
            </div>
        </div>

        {{-- Row 1: Line + Area --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-ds::card :title="__('ds.pages.charts.sections.line')" :description="__('ds.pages.charts.sections.line_description')">
                <div class="mt-4">
                    <x-ds::chart
                        type="line"
                        :height="260"
                        :series="[
                            ['name' => __('ds.pages.charts.series.revenue'), 'data' => [12, 18, 17, 24, 28, 32, 40]],
                            ['name' => __('ds.pages.charts.series.profit'),  'data' => [8,  11, 9,  16, 19, 22, 29]],
                        ]"
                        :options="[
                            'colors' => ['#009d46', '#3B82F6'],
                            'xaxis'  => ['categories' => [
                                __('ds.pages.charts.labels.mon'),
                                __('ds.pages.charts.labels.tue'),
                                __('ds.pages.charts.labels.wed'),
                                __('ds.pages.charts.labels.thu'),
                                __('ds.pages.charts.labels.fri'),
                                __('ds.pages.charts.labels.sat'),
                                __('ds.pages.charts.labels.sun'),
                            ]],
                            'legend' => ['show' => true, 'position' => 'top'],
                        ]"
                    />
                </div>
            </x-ds::card>

            <x-ds::card :title="__('ds.pages.charts.sections.area')" :description="__('ds.pages.charts.sections.area_description')">
                <div class="mt-4">
                    <x-ds::chart
                        type="area"
                        :height="260"
                        :series="[
                            ['name' => __('ds.pages.charts.series.visitors'), 'data' => [10, 14, 13, 18, 20, 26, 30]],
                        ]"
                        :options="[
                            'colors' => ['#009d46'],
                            'fill'   => [
                                'type'     => 'gradient',
                                'gradient' => ['shadeIntensity' => 1, 'opacityFrom' => 0.35, 'opacityTo' => 0.05, 'stops' => [0, 90, 100]],
                            ],
                            'xaxis'  => ['categories' => [
                                __('ds.pages.charts.labels.mon'),
                                __('ds.pages.charts.labels.tue'),
                                __('ds.pages.charts.labels.wed'),
                                __('ds.pages.charts.labels.thu'),
                                __('ds.pages.charts.labels.fri'),
                                __('ds.pages.charts.labels.sat'),
                                __('ds.pages.charts.labels.sun'),
                            ]],
                        ]"
                    />
                </div>
            </x-ds::card>
        </div>

        {{-- Row 2: Bar (2/3) + Donut (1/3) --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <x-ds::card class="lg:col-span-2" :title="__('ds.pages.charts.sections.bar')" :description="__('ds.pages.charts.sections.bar_description')">
                <div class="mt-4">
                    <x-ds::chart
                        type="bar"
                        :height="300"
                        :series="[
                            ['name' => __('ds.pages.charts.series.sales'), 'data' => [32, 41, 29, 52, 48, 38]],
                        ]"
                        :options="[
                            'colors'       => ['#F59E0B'],
                            'plotOptions'  => ['bar' => ['borderRadius' => 6, 'columnWidth' => '55%']],
                            'xaxis'        => ['categories' => [
                                __('ds.pages.charts.labels.jan'),
                                __('ds.pages.charts.labels.feb'),
                                __('ds.pages.charts.labels.mar'),
                                __('ds.pages.charts.labels.apr'),
                                __('ds.pages.charts.labels.may'),
                                __('ds.pages.charts.labels.jun'),
                            ]],
                        ]"
                    />
                </div>
            </x-ds::card>

            <x-ds::card :title="__('ds.pages.charts.sections.donut')" :description="__('ds.pages.charts.sections.donut_description')">
                <div class="mt-4">
                    {{-- Donut: series must be a flat numeric array --}}
                    <x-ds::chart
                        type="donut"
                        :height="300"
                        :series="[44, 24, 18, 14]"
                        :options="[
                            'labels'  => [
                                __('ds.pages.charts.labels.new'),
                                __('ds.pages.charts.labels.in_progress'),
                                __('ds.pages.charts.labels.won'),
                                __('ds.pages.charts.labels.lost'),
                            ],
                            'colors'  => ['#3B82F6', '#F59E0B', '#10B981', '#EF4444'],
                            'legend'  => ['show' => true, 'position' => 'bottom'],
                            'plotOptions' => ['pie' => ['donut' => ['size' => '65%']]],
                        ]"
                    />
                </div>
            </x-ds::card>
        </div>

        {{-- Row 3: Radial (1/3) + Mixed (2/3) --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <x-ds::card :title="__('ds.pages.charts.sections.radial')" :description="__('ds.pages.charts.sections.radial_description')">
                <div class="mt-4">
                    {{-- RadialBar: series must be a flat numeric array --}}
                    <x-ds::chart
                        type="radialBar"
                        :height="300"
                        :series="[76]"
                        :options="[
                            'colors'      => ['#009d46'],
                            'plotOptions' => [
                                'radialBar' => [
                                    'hollow'     => ['size' => '55%'],
                                    'dataLabels' => [
                                        'name'  => ['fontSize' => '13px'],
                                        'value' => ['fontSize' => '28px', 'fontWeight' => 700],
                                    ],
                                ],
                            ],
                            'labels' => [__('ds.pages.charts.labels.health_score')],
                        ]"
                    />
                </div>
            </x-ds::card>

            <x-ds::card class="lg:col-span-2" :title="__('ds.pages.charts.sections.mixed')" :description="__('ds.pages.charts.sections.mixed_description')">
                <div class="mt-4">
                    <x-ds::chart
                        type="line"
                        :height="300"
                        :series="[
                            ['name' => __('ds.pages.charts.series.orders'),     'type' => 'column', 'data' => [23, 30, 28, 35, 41, 38, 49]],
                            ['name' => __('ds.pages.charts.series.conversion'), 'type' => 'line',   'data' => [2.1, 2.4, 2.3, 2.8, 3.0, 2.9, 3.2]],
                        ]"
                        :options="[
                            'colors'      => ['#3B82F6', '#009d46'],
                            'stroke'      => ['width' => [0, 3]],
                            'dataLabels'  => ['enabled' => false],
                            'legend'      => ['show' => true, 'position' => 'top'],
                            'xaxis'       => ['categories' => [
                                __('ds.pages.charts.labels.mon'),
                                __('ds.pages.charts.labels.tue'),
                                __('ds.pages.charts.labels.wed'),
                                __('ds.pages.charts.labels.thu'),
                                __('ds.pages.charts.labels.fri'),
                                __('ds.pages.charts.labels.sat'),
                                __('ds.pages.charts.labels.sun'),
                            ]],
                            'yaxis' => [
                                ['title' => ['text' => __('ds.pages.charts.labels.orders')]],
                                ['opposite' => true, 'title' => ['text' => __('ds.pages.charts.labels.conversion')]],
                            ],
                        ]"
                    />
                </div>
            </x-ds::card>
        </div>

        {{-- Documentation --}}
        <div>
            <p class="mb-4 text-xs font-semibold uppercase tracking-widest text-(--ds-muted-foreground)">
                {{ __('ds.pages.charts.docs.title') }}
            </p>

            <x-ds::card :title="__('ds.pages.charts.docs.usage_title')" :description="__('ds.pages.charts.docs.usage_subtitle')">
                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        <p class="mb-3 text-sm font-semibold text-(--ds-foreground)">{{ __('ds.pages.charts.docs.example_title') }}</p>
                        <x-ds::chart
                            type="line"
                            :height="220"
                            :series="[
                                ['name' => __('ds.pages.charts.series.revenue'), 'data' => [10, 15, 14, 22, 25, 28, 33]],
                            ]"
                            :options="[
                                'colors' => ['#009d46'],
                                'xaxis'  => ['categories' => [
                                    __('ds.pages.charts.labels.mon'),
                                    __('ds.pages.charts.labels.tue'),
                                    __('ds.pages.charts.labels.wed'),
                                    __('ds.pages.charts.labels.thu'),
                                    __('ds.pages.charts.labels.fri'),
                                    __('ds.pages.charts.labels.sat'),
                                    __('ds.pages.charts.labels.sun'),
                                ]],
                            ]"
                        />
                    </div>

                    <div>
                        <p class="mb-3 text-sm font-semibold text-(--ds-foreground)">{{ __('ds.pages.charts.docs.example_code_title') }}</p>
                        <div class="overflow-hidden rounded-xl border border-(--ds-border) bg-(--ds-surface-2)">
                            <pre class="overflow-x-auto p-4 text-xs leading-relaxed text-(--ds-muted-foreground)"><code>@verbatim
<x-ds::chart
    type="line"
    :height="240"
    :series="[
        ['name' => 'Revenue', 'data' => [10, 15, 14, 22, 25, 28, 33]],
    ]"
    :options="[
        'colors' => ['#009d46'],
        'xaxis'  => ['categories' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']],
    ]"
/>
@endverbatim</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <p class="mb-3 text-sm font-semibold text-(--ds-foreground)">{{ __('ds.pages.charts.docs.props_title') }}</p>
                    <div class="overflow-hidden rounded-xl border border-(--ds-border)">
                        <x-ds::table
                            :headers="[
                                __('ds.pages.charts.docs.props.name'),
                                __('ds.pages.charts.docs.props.type'),
                                __('ds.pages.charts.docs.props.default'),
                                __('ds.pages.charts.docs.props.description'),
                            ]"
                        >
                            @php
                                $chartProps = [
                                    ['id',      'string|null',  'null',  __('ds.pages.charts.docs.props_list.id')],
                                    ['type',    'string',       'line',  __('ds.pages.charts.docs.props_list.type')],
                                    ['series',  'array',        '[]',    __('ds.pages.charts.docs.props_list.series')],
                                    ['options', 'array',        '[]',    __('ds.pages.charts.docs.props_list.options')],
                                    ['height',  'int|string',   '280',   __('ds.pages.charts.docs.props_list.height')],
                                    ['width',   'int|string',   '100%',  __('ds.pages.charts.docs.props_list.width')],
                                    ['theme',   'string',       'auto',  __('ds.pages.charts.docs.props_list.theme')],
                                ];
                            @endphp

                            @foreach ($chartProps as [$name, $type, $default, $desc])
                                <tr>
                                    <x-ds::table-cell>
                                        <code class="rounded bg-(--ds-surface-2) px-1.5 py-0.5 font-mono text-xs text-(--ds-foreground)">{{ $name }}</code>
                                    </x-ds::table-cell>
                                    <x-ds::table-cell>
                                        <span class="text-xs text-(--ds-muted-foreground)">{{ $type }}</span>
                                    </x-ds::table-cell>
                                    <x-ds::table-cell>
                                        <span class="text-xs text-(--ds-muted-foreground)">{{ $default }}</span>
                                    </x-ds::table-cell>
                                    <x-ds::table-cell>
                                        <span class="text-xs text-(--ds-muted-foreground)">{{ $desc }}</span>
                                    </x-ds::table-cell>
                                </tr>
                            @endforeach

                            <x-slot:footer>
                                <div class="p-4 text-xs text-(--ds-muted-foreground)">
                                    {{ __('ds.pages.charts.docs.notes') }}
                                </div>
                            </x-slot:footer>
                        </x-ds::table>
                    </div>
                </div>
            </x-ds::card>
        </div>

    </div>
@endsection
