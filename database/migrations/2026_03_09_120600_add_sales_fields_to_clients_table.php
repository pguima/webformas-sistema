<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete()->after('logo_path');
            $table->json('service_ids')->nullable()->after('plan_id');
            $table->decimal('contract_value', 12, 2)->nullable()->after('service_ids');
            $table->string('origin')->nullable()->after('contract_value');
            $table->string('campaign')->nullable()->after('origin');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'service_ids', 'contract_value', 'origin', 'campaign']);
        });
    }
};
