<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('url')->nullable();

            $table->string('type')->nullable();
            $table->string('objective')->nullable();
            $table->string('cta_main')->nullable();
            $table->string('platform')->nullable();
            $table->string('status')->nullable();

            $table->string('responsible')->nullable();

            $table->date('site_created_at')->nullable();
            $table->date('site_updated_at')->nullable();

            $table->string('hosting')->nullable();
            $table->date('domain_until')->nullable();
            $table->string('ssl')->nullable();
            $table->date('certificate_until')->nullable();
            $table->string('gtm_analytics')->nullable();

            $table->unsignedSmallInteger('pagespeed_mobile')->nullable();
            $table->unsignedSmallInteger('pagespeed_desktop')->nullable();
            $table->unsignedSmallInteger('seo_score')->nullable();

            $table->unsignedSmallInteger('priority')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webs');
    }
};
