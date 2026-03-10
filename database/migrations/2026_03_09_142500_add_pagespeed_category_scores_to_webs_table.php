<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webs', function (Blueprint $table) {
            $table->unsignedSmallInteger('performance')->nullable()->after('seo_score');
            $table->unsignedSmallInteger('seo')->nullable()->after('performance');
            $table->unsignedSmallInteger('accessibility')->nullable()->after('seo');
            $table->unsignedSmallInteger('best_practices')->nullable()->after('accessibility');
        });
    }

    public function down(): void
    {
        Schema::table('webs', function (Blueprint $table) {
            $table->dropColumn(['performance', 'seo', 'accessibility', 'best_practices']);
        });
    }
};
