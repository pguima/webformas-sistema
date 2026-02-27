@extends('layouts.auth')

@section('title', __('app.auth.reset_password.title'))

@section('content')
    <x-ds::card>
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-semibold text-(--text-primary)">{{ __('app.auth.reset_password.heading') }}</h1>
                <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.auth.reset_password.subtitle') }}</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}" />

                <x-ds::input
                    :label="__('app.auth.reset_password.email')"
                    type="email"
                    name="email"
                    value="{{ old('email', request('email')) }}"
                    required
                    :error="$errors->first('email')"
                    placeholder="you@company.com"
                />

                <x-ds::input
                    :label="__('app.auth.reset_password.password')"
                    type="password"
                    name="password"
                    required
                    :error="$errors->first('password')"
                    placeholder="••••••••"
                />

                <x-ds::input
                    :label="__('app.auth.reset_password.password_confirmation')"
                    type="password"
                    name="password_confirmation"
                    required
                    placeholder="••••••••"
                />

                <div class="pt-2">
                    <x-ds::button type="submit" class="w-full">{{ __('app.auth.reset_password.submit') }}</x-ds::button>
                </div>
            </form>
        </div>
    </x-ds::card>
@endsection
