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
        Schema::table('lots', function (Blueprint $table) {
            $table->decimal('price', 12, 2)
                ->nullable()
                ->after('status');

            $table->decimal('lot_area', 10, 2)
                ->nullable()
                ->after('price');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('lot_area');
                
            $table->foreignId('house_model_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()
                ->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            //
        });
    }
};
