<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('whatsapp')->nullable();
            $table->string('plan')->nullable();
            $table->text('services')->nullable();
            $table->decimal('value', 12, 2)->nullable();

            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('origin')->nullable();
            $table->string('campaign')->nullable();

            $table->string('stage');
            $table->unsignedInteger('position')->default(0);

            $table->string('external_id')->nullable();
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->index(['stage', 'position']);
            $table->index(['external_id']);
            $table->index(['responsible_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
