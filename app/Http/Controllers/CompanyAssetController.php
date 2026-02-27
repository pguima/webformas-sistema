<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyAssetController extends Controller
{
    public function show(Request $request, string $asset): StreamedResponse
    {
        $settings = CompanySetting::current();

        $path = match ($asset) {
            'logo-light' => $settings?->logo_light_path,
            'logo-dark' => $settings?->logo_dark_path,
            'favicon' => $settings?->favicon_path,
            'auth-side' => $settings?->auth_side_image_path,
            default => null,
        };

        if (!is_string($path) || $path === '') {
            abort(404);
        }

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
}
