<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webs', function (Blueprint $table) {
            $table->dropColumn([
                'site_created_at',
                'site_updated_at',
                'hosting',
                'domain_until',
                'ssl',
                'certificate_until',
                'priority',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('webs', function (Blueprint $table) {
            $table->date('site_created_at')->nullable()->after('responsible');
            $table->date('site_updated_at')->nullable()->after('site_created_at');
            $table->string('hosting')->nullable()->after('site_updated_at');
            $table->date('domain_until')->nullable()->after('hosting');
            $table->string('ssl')->nullable()->after('domain_until');
            $table->date('certificate_until')->nullable()->after('ssl');
            $table->unsignedSmallInteger('priority')->nullable()->after('seo_score');
        });
    }
};
