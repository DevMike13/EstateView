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
        Schema::table('purchase_accounts', function (Blueprint $table) {
            $table->decimal('house_price', 23, 2)->default(0)->change();
            $table->decimal('total_contract_price', 23, 2)->default(0)->change();
 
            $table->decimal('cash_discount', 23, 2)->default(0)->change();
            $table->decimal('net_contract_price', 23, 2)->default(0)->change();
 
            $table->decimal('downpayment_amount', 23, 2)->default(0)->change();
            $table->decimal('reservation_fee_credit', 23, 2)->default(0)->change();
            $table->decimal('remaining_downpayment', 23, 2)->default(0)->change();
 
            $table->decimal('loanable_amount', 23, 2)->default(0)->change();
            $table->decimal('monthly_amortization', 23, 2)->default(0)->change();
 
            $table->decimal('total_paid', 23, 2)->default(0)->change();
            $table->decimal('remaining_balance', 23, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_accounts', function (Blueprint $table) {
            $table->decimal('house_price', 15, 2)->default(0)->change();
            $table->decimal('total_contract_price', 15, 2)->default(0)->change();
 
            $table->decimal('cash_discount', 15, 2)->default(0)->change();
            $table->decimal('net_contract_price', 15, 2)->default(0)->change();
 
            $table->decimal('downpayment_amount', 15, 2)->default(0)->change();
            $table->decimal('reservation_fee_credit', 15, 2)->default(0)->change();
            $table->decimal('remaining_downpayment', 15, 2)->default(0)->change();
 
            $table->decimal('loanable_amount', 15, 2)->default(0)->change();
            $table->decimal('monthly_amortization', 15, 2)->default(0)->change();
 
            $table->decimal('total_paid', 15, 2)->default(0)->change();
            $table->decimal('remaining_balance', 15, 2)->default(0)->change();
        });
    }
};
