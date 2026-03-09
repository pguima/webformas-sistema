<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->string('name');
            $table->string('whatsapp')->nullable();
            $table->string('role')->nullable();

            $table->timestamps();

            $table->index(['client_id']);
            $table->index(['whatsapp']);
            $table->index(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
