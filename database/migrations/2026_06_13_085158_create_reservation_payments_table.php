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
        Schema::create('reservation_payments', function (Blueprint $table) {
            $table->id();

             $table->foreignId('lot_reservation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('amount', 15, 2);

            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'gcash',
                'maya',
            ]);

            $table->string('reference_no')
                ->nullable();

            $table->string('proof_of_payment')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();

            $table->enum('status', [
                'pending',
                'verified',
                'rejected',
            ])->default('pending');

            $table->text('remarks')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_payments');
    }
};
