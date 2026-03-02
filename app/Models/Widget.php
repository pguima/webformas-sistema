<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Widget extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'author',
        'category',
        'image_path',
        'json_code',
    ];

    protected function casts(): array
    {
        return [
            'json_code' => 'array',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        return Storage::disk('public')->url(ltrim((string) $this->image_path, '/'));
    }
}
