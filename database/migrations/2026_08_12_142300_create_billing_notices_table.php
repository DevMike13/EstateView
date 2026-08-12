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
        Schema::create('billing_notices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('billing_id')
                ->constrained('billings')
                ->cascadeOnDelete();

            $table->foreignId('purchase_account_id')
                ->constrained('purchase_accounts')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('notice_type');

            $table->unsignedTinyInteger('overdue_month')
                ->default(0);

            $table->decimal('amount', 15, 2)
                ->default(0);

            $table->date('billing_due_date');

            $table->date('notice_date');

            $table->date('deadline_date')
                ->nullable();

            $table->date('effective_date')
                ->nullable();

            $table->string('pdf_path')
                ->nullable();

            $table->string('email_to');

            $table->string('status')
                ->default('pending');

            $table->timestamp('sent_at')
                ->nullable();

            $table->text('failure_reason')
                ->nullable();

            $table->unique(
                [
                    'billing_id',
                    'notice_type',
                    'overdue_month',
                ],
                'billing_notice_period_unique'
            );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_notices');
    }
};
