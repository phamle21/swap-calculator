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
        Schema::create('swap_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_pair_id')->constrained('currency_pairs')->cascadeOnDelete();
            $table->foreignId('profile_id')->constrained('swap_profiles')->cascadeOnDelete();
            $table->decimal('swap_long', 12, 4);
            $table->decimal('swap_short', 12, 4);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['currency_pair_id', 'profile_id', 'effective_from'], 'uniq_rate_start');
            $table->index(['currency_pair_id', 'profile_id', 'is_active'], 'idx_rate_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swap_rates');
    }
};
