<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('webs', function (Blueprint $table) {
            $table->timestamp('pagespeed_last_checked_at')->nullable()->after('best_practices');
        });
    }

    public function down(): void
    {
        Schema::table('webs', function (Blueprint $table) {
            $table->dropColumn('pagespeed_last_checked_at');
        });
    }
};
