<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Web extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'client_id',
        'name',
        'url',
        'type',
        'objective',
        'cta_main',
        'platform',
        'status',
        'responsible',
        'gtm_analytics',
        'pagespeed_mobile',
        'pagespeed_desktop',
        'seo_score',
        'performance',
        'seo',
        'accessibility',
        'best_practices',
        'pagespeed_last_checked_at',
        'pagespeed_schedule',
        'notes',
    ];

    protected $casts = [
        'pagespeed_mobile' => 'integer',
        'pagespeed_desktop' => 'integer',
        'seo_score' => 'integer',
        'performance' => 'integer',
        'seo' => 'integer',
        'accessibility' => 'integer',
        'best_practices' => 'integer',
        'pagespeed_last_checked_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
