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
        Schema::create('swap_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_pair_id')->constrained('currency_pairs')->cascadeOnDelete();
            $table->foreignId('profile_id')->nullable()->constrained('swap_profiles')->nullOnDelete();

            $table->decimal('lot_size', 12, 2);
            $table->enum('position_type', ['Long', 'Short']);
            $table->decimal('swap_rate', 12, 4);
            $table->unsignedInteger('days');
            $table->boolean('cross_wednesday')->default(false);
            $table->decimal('total_swap', 14, 4);

            $table->string('note', 255)->nullable();
            $table->json('inputs')->nullable();
            $table->timestampsTz();

            $table->index(['created_at']);
            $table->index(['currency_pair_id', 'profile_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('swap_calculations');
    }
};
