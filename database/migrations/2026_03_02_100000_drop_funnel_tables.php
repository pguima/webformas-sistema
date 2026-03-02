<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('funnel_cards');
        Schema::dropIfExists('funnel_columns');
        Schema::dropIfExists('funnels');
    }

    public function down(): void
    {
        Schema::create('funnels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });

        Schema::create('funnel_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_id')->constrained('funnels')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('position')->default(0);
            $table->string('count_variant')->nullable();
            $table->timestamps();

            $table->index(['funnel_id', 'position']);
        });

        Schema::create('funnel_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funnel_column_id')->constrained('funnel_columns')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->string('tag')->nullable();
            $table->string('tag_variant')->nullable();
            $table->string('external_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['funnel_column_id', 'position']);
            $table->index(['external_id']);
        });
    }
};
