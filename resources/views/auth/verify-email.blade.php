@extends('layouts.auth')

@section('title', __('app.auth.verify_email.title'))

@section('content')
    <x-ds::card>
        <div class="space-y-6">
            <div>
                <h1 class="text-xl font-semibold text-(--text-primary)">{{ __('app.auth.verify_email.heading') }}</h1>
                <p class="mt-1 text-sm text-(--text-secondary)">{{ __('app.auth.verify_email.subtitle') }}</p>
            </div>

            @if (session('status') === 'verification-link-sent')
                <x-ds::alert variant="success" icon="solar:check-circle-linear">
                    {{ __('app.auth.verify_email.status_sent') }}
                </x-ds::alert>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                @csrf
                <x-ds::button type="submit" class="w-full">{{ __('app.auth.verify_email.resend') }}</x-ds::button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ds::button type="submit" variant="secondary" class="w-full">{{ __('app.auth.verify_email.logout') }}</x-ds::button>
            </form>
        </div>
    </x-ds::card>
@endsection
