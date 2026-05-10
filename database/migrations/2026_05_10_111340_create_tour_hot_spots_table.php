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
        Schema::create('tour_hot_spots', function (Blueprint $table) {
            $table->id();

            $table->foreignId('scene_id')
                ->constrained('tour_scenes')
                ->cascadeOnDelete();

            $table->foreignId('target_scene_id')
                ->nullable()
                ->constrained('tour_scenes')
                ->nullOnDelete();

            $table->string('label');

            $table->float('pitch');

            $table->float('yaw');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_hot_spots');
    }
};
