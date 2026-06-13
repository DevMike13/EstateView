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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_account_id')->constrained()->cascadeOnDelete();

            $table->string('billing_no')->unique();
            $table->string('title');

            $table->date('due_date');

            $table->decimal('amount_due', 15, 2);
            $table->decimal('amount_paid', 15, 2)->default(0);

            $table->enum('status', [
                'unpaid',
                'partial',
                'paid',
                'cancelled',
            ])->default('unpaid');
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
