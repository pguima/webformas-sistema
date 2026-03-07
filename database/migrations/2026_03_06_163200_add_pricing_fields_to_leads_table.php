<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('whatsapp')->constrained('plans')->nullOnDelete();
            $table->json('service_ids')->nullable()->after('plan_id');

            $table->decimal('value_base', 12, 2)->nullable()->after('service_ids');
            $table->string('discount_type')->nullable()->after('value_base');
            $table->decimal('discount_value', 12, 2)->nullable()->after('discount_type');
            $table->decimal('value_final', 12, 2)->nullable()->after('discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn([
                'service_ids',
                'value_base',
                'discount_type',
                'discount_value',
                'value_final',
            ]);
        });
    }
};
