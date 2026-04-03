<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_pagespeed_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('web_id')->constrained('webs')->cascadeOnDelete();

            $table->unsignedSmallInteger('performance_mobile')->nullable();
            $table->unsignedSmallInteger('seo_mobile')->nullable();
            $table->unsignedSmallInteger('accessibility_mobile')->nullable();
            $table->unsignedSmallInteger('best_practices_mobile')->nullable();

            $table->unsignedSmallInteger('performance_desktop')->nullable();
            $table->unsignedSmallInteger('seo_desktop')->nullable();
            $table->unsignedSmallInteger('accessibility_desktop')->nullable();
            $table->unsignedSmallInteger('best_practices_desktop')->nullable();

            $table->timestamp('analyzed_at');
            $table->timestamps();

            $table->index(['web_id', 'analyzed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_pagespeed_history');
    }
};
