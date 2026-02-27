@extends('layouts.layout-ds')

@section('content')
    <div>
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-(--text-primary)">
                    {{ __('ds.pages.forms_advanced.title') }}
                </h1>
                <div class="mt-1 text-sm text-(--text-secondary)">
                    {{ __('ds.pages.forms_advanced.subtitle') }}
                </div>
            </div>
            <div class="flex gap-2">
                <x-ds::button variant="ghost" icon="solar:arrow-left-linear" onclick="history.back()">Back</x-ds::button>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Advanced Selects -->
            <x-ds::card :title="__('ds.pages.forms_advanced.sections.selects')"
                :description="__('ds.pages.forms_advanced.sections.selects_desc')">
                <div class="mt-4 space-y-6">
                    <!-- Single Search -->
                    <x-ds::select-search label="Searchable Select" :options="['New York', 'London', 'Paris', 'Tokyo', 'Berlin', 'São Paulo', 'Toronto']" placeholder="Select a city..." />

                    <!-- Multiple Search -->
                    <x-ds::select-search label="Multiple Tags" :multiple="true" :options="[
            ['value' => 'html', 'label' => 'HTML'],
            ['value' => 'css', 'label' => 'CSS'],
            ['value' => 'js', 'label' => 'JavaScript'],
            ['value' => 'php', 'label' => 'PHP'],
            ['value' => 'laravel', 'label' => 'Laravel'],
            ['value' => 'vue', 'label' => 'Vue.js'],
            ['value' => 'react', 'label' => 'React'],
        ]" placeholder="Select skills..."
                        helper="You can select multiple skills." />
                </div>
            </x-ds::card>

            <!-- Input Masks -->
            <x-ds::card :title="__('ds.pages.forms_advanced.sections.masks')"
                :description="__('ds.pages.forms_advanced.sections.masks_desc')">
                <div class="mt-4 space-y-4">
                    <x-ds::input-mask label="CPF" mask="cpf" placeholder="000.000.000-00" icon="solar:user-id-linear" />

                    <x-ds::input-mask label="Phone Number" mask="phone" placeholder="(00) 00000-0000"
                        icon="solar:phone-linear" helper="Auto-adjusts for 8 or 9 digits" />

                    <x-ds::input-mask label="Date" mask="date" placeholder="DD/MM/YYYY" icon="solar:calendar-linear" />

                    <x-ds::input-mask label="Currency (BRL)" mask="money" placeholder="R$ 0,00"
                        icon="solar:wallet-money-linear" />
                </div>
            </x-ds::card>

            <!-- Documentation -->
            <div class="lg:col-span-2">
                <div class="mb-6 text-sm font-semibold text-(--text-secondary)">
                    {{ __('ds.pages.forms_advanced.docs.title') }}
                </div>

                <x-ds::card class="h-full" :title="__('ds.pages.forms_advanced.docs.usage_title')"
                    :description="__('ds.pages.forms_advanced.docs.usage_subtitle')">
                    <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <div class="text-sm font-semibold text-(--text-primary)">
                                {{ __('ds.pages.forms_advanced.docs.select_title') }}</div>
                            <div
                                class="mt-3 overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-hover)">
                                <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
                                    <!-- Multiple Select -->
                                    <x-ds::select-search 
                                        label="Skills" 
                                        :multiple="true" 
                                        :options="$skillOptions" 
                                    />

                                    <!-- Searchable Single -->
                                    <x-ds::select-search 
                                        label="City" 
                                        :options="['Option 1', 'Option 2']" 
                                    />
                                @endverbatim</code></pre>
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-(--text-primary)">
                                {{ __('ds.pages.forms_advanced.docs.mask_title') }}</div>
                            <div
                                class="mt-3 overflow-hidden rounded-lg border border-(--border-default) bg-(--surface-hover)">
                                <pre class="overflow-x-auto p-4 text-xs leading-relaxed"><code>@verbatim
                                    <!-- CPF Mask -->
                                    <x-ds::input-mask mask="cpf" label="CPF" />

                                    <!-- Money -->
                                    <x-ds::input-mask mask="money" label="Price" />

                                    <!-- Custom Pattern -->
                                    <x-ds::input-mask mask="999-99" label="Code" />
                                @endverbatim</code></pre>
                            </div>
                        </div>
                    </div>
                </x-ds::card>
            </div>
        </div>
    </div>
@endsection