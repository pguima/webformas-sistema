<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'logo_light_path',
        'logo_dark_path',
        'favicon_path',
        'auth_side_image_path',
    ];

    public static function current(): ?self
    {
        try {
            if (! Schema::hasTable('company_settings')) {
                return null;
            }

            return self::query()->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
