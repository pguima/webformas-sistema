<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'cnpj',
        'category',
        'logo_path',
        'plan_id',
        'service_ids',
        'contract_value',
        'origin',
        'campaign',
    ];

    protected function casts(): array
    {
        return [
            'service_ids' => 'array',
            'contract_value' => 'decimal:2',
        ];
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    public function webs(): HasMany
    {
        return $this->hasMany(Web::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function campaign(): HasOne
    {
        return $this->hasOne(Campaign::class);
    }
}
