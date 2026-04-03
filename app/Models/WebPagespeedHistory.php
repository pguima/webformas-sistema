<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebPagespeedHistory extends Model
{
    protected $table = 'web_pagespeed_history';

    protected $fillable = [
        'web_id',
        'performance_mobile',
        'seo_mobile',
        'accessibility_mobile',
        'best_practices_mobile',
        'performance_desktop',
        'seo_desktop',
        'accessibility_desktop',
        'best_practices_desktop',
        'analyzed_at',
    ];

    protected $casts = [
        'analyzed_at' => 'datetime',
        'performance_mobile' => 'integer',
        'seo_mobile' => 'integer',
        'accessibility_mobile' => 'integer',
        'best_practices_mobile' => 'integer',
        'performance_desktop' => 'integer',
        'seo_desktop' => 'integer',
        'accessibility_desktop' => 'integer',
        'best_practices_desktop' => 'integer',
    ];

    public function web(): BelongsTo
    {
        return $this->belongsTo(Web::class);
    }
}
