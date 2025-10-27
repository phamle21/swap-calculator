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
        Schema::create('swap_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('note', 255)->nullable();
            $table->decimal('wednesday_multiplier', 5, 2)->default(3.00);
            $table->json('settings')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swap_profiles');
    }
};
