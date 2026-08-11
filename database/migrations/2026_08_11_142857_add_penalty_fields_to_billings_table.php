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
        Schema::table('billings', function (Blueprint $table) {
            $table->decimal('early_payment_discount', 12, 2)
                ->default(200);

            $table->decimal('monthly_penalty_rate', 5, 2)
                ->default(3);

            $table->decimal('penalty_amount', 15, 2)
                ->default(0);

            $table->decimal('discount_amount', 15, 2)
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropColumn([
                'early_payment_discount',
                'monthly_penalty_rate',
                'penalty_amount',
                'discount_amount',
            ]);
        });
    }
};
