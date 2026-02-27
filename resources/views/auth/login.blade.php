@extends('layouts.auth')

@section('title', __('app.auth.login.title'))

@section('content')
    <x-ds::card>
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-semibold text-(--text-primary)">{{ __('app.auth.login.heading') }}</h1>
                <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.auth.login.subtitle') }}</p>
            </div>

            @if (session('status'))
                <x-ds::alert variant="success" icon="solar:check-circle-linear">
                    {{ session('status') }}
                </x-ds::alert>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <x-ds::input
                    :label="__('app.auth.login.email')"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    :error="$errors->first('email')"
                    placeholder="you@company.com"
                />

                <x-ds::input
                    :label="__('app.auth.login.password')"
                    type="password"
                    name="password"
                    required
                    :error="$errors->first('password')"
                    placeholder="••••••••"
                />

                <div class="text-sm">
                    <x-ds::link href="{{ route('password.request') }}">{{ __('app.auth.login.forgot_password') }}</x-ds::link>
                </div>

                <label class="flex items-center gap-2 text-sm text-(--text-secondary)">
                    <input type="checkbox" name="remember" class="rounded border-(--border-default)" />
                    {{ __('app.auth.login.remember_me') }}
                </label>

                <div class="pt-2">
                    <x-ds::button type="submit" class="w-full">{{ __('app.auth.login.submit') }}</x-ds::button>
                </div>
            </form>

            
        </div>
    </x-ds::card>
@endsection
