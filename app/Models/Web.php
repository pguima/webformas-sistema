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
        'site_created_at',
        'site_updated_at',
        'hosting',
        'domain_until',
        'ssl',
        'certificate_until',
        'gtm_analytics',
        'pagespeed_mobile',
        'pagespeed_desktop',
        'seo_score',
        'priority',
        'notes',
    ];

    protected $casts = [
        'site_created_at' => 'date',
        'site_updated_at' => 'date',
        'domain_until' => 'date',
        'certificate_until' => 'date',
        'pagespeed_mobile' => 'integer',
        'pagespeed_desktop' => 'integer',
        'seo_score' => 'integer',
        'priority' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
