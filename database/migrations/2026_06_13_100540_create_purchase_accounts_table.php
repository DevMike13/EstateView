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
        Schema::create('purchase_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lot_reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('house_model_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('payment_scheme', [
                'cash',
                'bank_loan',
                'deferred_payment',
            ]);

            $table->decimal('lot_price', 15, 2)->default(0);
            $table->decimal('house_price', 15, 2)->default(0);
            $table->decimal('total_contract_price', 15, 2)->default(0);

            $table->decimal('cash_discount', 15, 2)->default(0);
            $table->decimal('net_contract_price', 15, 2)->default(0);

            $table->decimal('downpayment_amount', 15, 2)->default(0);
            $table->decimal('reservation_fee_credit', 15, 2)->default(0);
            $table->decimal('remaining_downpayment', 15, 2)->default(0);

            $table->decimal('loanable_amount', 15, 2)->default(0);
            $table->integer('loan_term_years')->nullable();
            $table->decimal('monthly_amortization', 15, 2)->default(0);

            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('remaining_balance', 15, 2)->default(0);

            $table->enum('status', [
                'active',
                'downpayment_pending',
                'bank_processing',
                'fully_paid',
                'cancelled',
            ])->default('active');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_accounts');
    }
};
