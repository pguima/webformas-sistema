<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('company_settings')) {
            return;
        }

        Schema::table('company_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('company_settings', 'auth_side_image_path')) {
                $table->string('auth_side_image_path')->nullable()->after('favicon_path');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('company_settings')) {
            return;
        }

        Schema::table('company_settings', function (Blueprint $table) {
            if (Schema::hasColumn('company_settings', 'auth_side_image_path')) {
                $table->dropColumn('auth_side_image_path');
            }
        });
    }
};
