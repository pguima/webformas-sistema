@extends('layouts.layout-ds')

@section('content')
    <div>
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    {{ __('ds.pages.blank.title') }}
                </h1>
                <p class="mt-2 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.blank.description') }}
                </p>
            </div>

            <x-ds::link
                href="{{ url('/design-system') }}"
                variant="secondary"
                underline="none"
                class="rounded-md border border-(--ds-border) bg-(--ds-surface) px-3 py-1.5 text-sm hover:bg-(--ds-surface-2)"
                wire:navigate
            >
                {{ __('ds.actions.back') }}
            </x-ds::link>
        </div>

        <x-ds::card class="mt-6 border-dashed" :padded="false" :shadow="false">
            <div class="p-10">
                <div class="text-sm font-medium">
                    {{ __('ds.pages.blank.empty_title') }}
                </div>
                <div class="mt-2 text-sm text-(--ds-muted-foreground)">
                    {{ __('ds.pages.blank.empty_hint') }}
                </div>
            </div>
        </x-ds::card>
    </div>
@endsection
