@extends('layouts.auth')

@section('title', __('app.auth.register.title'))

@section('content')
    <x-ds::card>
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-semibold text-(--text-primary)">{{ __('app.auth.register.heading') }}</h1>
                <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.auth.register.subtitle') }}</p>
            </div>

            @if ($errors->any())
                <x-ds::alert variant="danger" icon="solar:danger-circle-linear">
                    {{ $errors->first() }}
                </x-ds::alert>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <x-ds::input
                    :label="__('app.auth.register.name')"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    :error="$errors->first('name')"
                    placeholder="John Doe"
                />

                <x-ds::input
                    :label="__('app.auth.register.email')"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    :error="$errors->first('email')"
                    placeholder="you@company.com"
                />

                <x-ds::input
                    :label="__('app.auth.register.password')"
                    type="password"
                    name="password"
                    required
                    :error="$errors->first('password')"
                    placeholder="••••••••"
                />

                <x-ds::input
                    :label="__('app.auth.register.password_confirmation')"
                    type="password"
                    name="password_confirmation"
                    required
                    placeholder="••••••••"
                />

                <div class="pt-2">
                    <x-ds::button type="submit" class="w-full">{{ __('app.auth.register.submit') }}</x-ds::button>
                </div>
            </form>

            <div class="text-sm text-(--text-secondary)">
                {{ __('app.auth.register.have_account') }}
                <x-ds::link href="{{ route('login') }}">{{ __('app.auth.register.login_link') }}</x-ds::link>
            </div>
        </div>
    </x-ds::card>
@endsection
