<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyAssetController;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    $user = auth()->user();
    if ($user) {
        return redirect('/profile');
    }
    return redirect('/login');
});

Route::get('/company-assets/{asset}', [CompanyAssetController::class, 'show'])
    ->whereIn('asset', ['logo-light', 'logo-dark', 'favicon', 'auth-side'])
    ->name('company.assets.show');

Route::get('/locale/{locale}', function (Request $request, string $locale) {
    if (! in_array($locale, ['pt_BR', 'en', 'es'], true)) {
        abort(404);
    }

    $request->session()->put('locale', $locale);

    return redirect()->back()->withCookie(cookie()->forever('locale', $locale));
})->name('locale.set');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request) {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __('app.auth.forgot_password.status_sent'))
            : back()->withErrors(['email' => __('app.auth.forgot_password.status_error')]);
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request) {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                    event(new Verified($user));
                }
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    })->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('login');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', \App\Livewire\Users\Profile::class);
});

// ============================================
// ROTAS ADMINISTRATIVAS (com sidebar)
// ============================================
Route::middleware(['auth', 'verified', 'role:SuperAdmin,Admin'])->group(function () {
    Route::get('/users', \App\Livewire\Users\Index::class);

    Route::get('/settings/company', \App\Livewire\Settings\Company::class)->middleware('role:SuperAdmin');

    Route::prefix('design-system')
        ->middleware('role:SuperAdmin')
        ->group(function () {
            Route::view('/', 'design-system.index');
            Route::view('/blank', 'design-system.pages.blank');
            Route::view('/card', 'design-system.pages.card');
            Route::view('/button', 'design-system.pages.button');
            Route::view('/badges', 'design-system.pages.badges');
            Route::view('/alerts', 'design-system.pages.alerts');
            Route::view('/toasts', 'design-system.pages.toasts');
            Route::view('/spinners', 'design-system.pages.spinners');
            Route::view('/tooltips', 'design-system.pages.tooltips');
            Route::view('/accordion', 'design-system.pages.accordion');
            Route::view('/tabs', 'design-system.pages.tabs');
            Route::view('/tags', 'design-system.pages.tags');
            Route::view('/modals', 'design-system.pages.modals');
            Route::view('/offcanvas', 'design-system.pages.offcanvas');
            Route::view('/tables', 'design-system.pages.tables');
            Route::view('/forms', 'design-system.pages.forms');
            Route::view('/forms-advanced', 'design-system.pages.forms-advanced');
            Route::view('/links', 'design-system.pages.links');
            Route::view('/kanban', 'design-system.pages.kanban');
        });
});
