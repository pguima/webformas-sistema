<?php

namespace App\Livewire\Settings;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Company extends Component
{
    use WithFileUploads;

    public string $company_name = '';

    /** @var TemporaryUploadedFile|null */
    public $logo_light = null;

    /** @var TemporaryUploadedFile|null */
    public $logo_dark = null;

    /** @var TemporaryUploadedFile|null */
    public $favicon = null;

    /** @var TemporaryUploadedFile|null */
    public $auth_side_image = null;

    public ?string $current_logo_light_path = null;
    public ?string $current_logo_dark_path = null;
    public ?string $current_favicon_path = null;
    public ?string $current_auth_side_image_path = null;

    public bool $saved = false;

    public function mount(): void
    {
        $settings = CompanySetting::current();

        $this->company_name = (string) ($settings?->company_name ?? '');
        $this->current_logo_light_path = $settings?->logo_light_path;
        $this->current_logo_dark_path = $settings?->logo_dark_path;
        $this->current_favicon_path = $settings?->favicon_path;
        $this->current_auth_side_image_path = Schema::hasColumn('company_settings', 'auth_side_image_path')
            ? $settings?->auth_side_image_path
            : null;
    }

    public function save(): void
    {
        $this->saved = false;

        $data = $this->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'logo_light' => ['nullable', 'image', 'max:2048'],
            'logo_dark' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'file', 'max:2048', 'mimes:png,ico'],
            'auth_side_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $settings = CompanySetting::query()->first() ?? new CompanySetting();
        $settings->company_name = $data['company_name'] ?? null;

        if (! empty($data['logo_light'])) {
            $newPath = $data['logo_light']->store('company', 'public');
            $oldPath = $settings->logo_light_path;
            $settings->logo_light_path = $newPath;
            if ($oldPath && str_starts_with($oldPath, 'company/')) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        if (! empty($data['logo_dark'])) {
            $newPath = $data['logo_dark']->store('company', 'public');
            $oldPath = $settings->logo_dark_path;
            $settings->logo_dark_path = $newPath;
            if ($oldPath && str_starts_with($oldPath, 'company/')) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        if (! empty($data['favicon'])) {
            $newPath = $data['favicon']->store('company', 'public');
            $oldPath = $settings->favicon_path;
            $settings->favicon_path = $newPath;
            if ($oldPath && str_starts_with($oldPath, 'company/')) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        if (! empty($data['auth_side_image']) && Schema::hasColumn('company_settings', 'auth_side_image_path')) {
            $newPath = $data['auth_side_image']->store('company', 'public');
            $oldPath = $settings->auth_side_image_path;
            $settings->auth_side_image_path = $newPath;
            if ($oldPath && str_starts_with($oldPath, 'company/')) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $settings->save();

        $this->logo_light = null;
        $this->logo_dark = null;
        $this->favicon = null;
        $this->auth_side_image = null;

        $this->current_logo_light_path = $settings->logo_light_path;
        $this->current_logo_dark_path = $settings->logo_dark_path;
        $this->current_favicon_path = $settings->favicon_path;
        $this->current_auth_side_image_path = Schema::hasColumn('company_settings', 'auth_side_image_path')
            ? $settings->auth_side_image_path
            : null;

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.settings.company')->layout('layouts.app');
    }
}
