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
        Schema::create('commission_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('purchase_account_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('lot_reservation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('period_number');

            $table->string('period_label');

            /*
             * Store snapshots so changing an agent's percentage later
             * does not change old commission requests.
             */
            $table->decimal('commission_percentage', 5, 2);

            $table->decimal(
                'total_contract_price',
                15,
                2
            );

            $table->decimal(
                'total_commission_amount',
                15,
                2
            );

            $table->decimal(
                'requested_amount',
                15,
                2
            );

            $table->json('covered_billing_ids');

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'paid',
            ])->default('pending');

            $table->text('remarks')->nullable();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('requested_at')->nullable();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->string('receipt_path')->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'purchase_account_id',
                    'period_number',
                ],
                'commission_account_period_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_requests');
    }
};
