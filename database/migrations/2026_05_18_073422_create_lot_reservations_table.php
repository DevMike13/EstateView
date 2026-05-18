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
        Schema::create('lot_reservations', function (Blueprint $table) {
            $table->id();

            $table->string('type');

            $table->foreignId('lot_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            
            $table->foreignId('house_model_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_reservations');
    }
};
