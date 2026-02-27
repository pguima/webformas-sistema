@extends('layouts.auth')

@section('title', __('app.auth.forgot_password.title'))

@section('content')
    <x-ds::card>
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-semibold text-(--text-primary)">{{ __('app.auth.forgot_password.heading') }}</h1>
                <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.auth.forgot_password.subtitle') }}</p>
            </div>

            @if (session('status'))
                <x-ds::alert variant="success" icon="solar:check-circle-linear">
                    {{ session('status') }}
                </x-ds::alert>
            @endif

            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-triangle-linear">
                    {{ $errors->first('email') ?: $errors->first() }}
                </x-ds::alert>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <x-ds::input
                    :label="__('app.auth.forgot_password.email')"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    :error="$errors->first('email')"
                    placeholder="you@company.com"
                />

                <div class="pt-2">
                    <x-ds::button type="submit" class="w-full">{{ __('app.auth.forgot_password.submit') }}</x-ds::button>
                </div>
            </form>

            <div class="text-sm text-(--text-secondary)">
                <x-ds::link href="{{ route('login') }}">{{ __('app.auth.forgot_password.back_to_login') }}</x-ds::link>
            </div>
        </div>
    </x-ds::card>
@endsection
