<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currency_pairs', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 10)->unique();
            $table->char('base', 6);
            $table->char('quote', 6);
            $table->unsignedTinyInteger('digits')->default(5);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_pairs');
    }
};
