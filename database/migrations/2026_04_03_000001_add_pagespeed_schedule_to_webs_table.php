<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webs', function (Blueprint $table) {
            $table->string('pagespeed_schedule')->default('none')->after('pagespeed_last_checked_at');
            // Values: none | daily | weekly | monthly
        });
    }

    public function down(): void
    {
        Schema::table('webs', function (Blueprint $table) {
            $table->dropColumn('pagespeed_schedule');
        });
    }
};
