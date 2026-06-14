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
        Schema::table('lot_reservations', function (Blueprint $table) {
            $table->decimal('downpayment_percentage', 5, 2)->nullable()->after('house_model_id');
            $table->integer('downpayment_term_months')->nullable()->after('downpayment_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lot_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'downpayment_percentage',
                'downpayment_term_months',
            ]);
        });
    }
};
