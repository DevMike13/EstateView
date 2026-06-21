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
        Schema::create('payment_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method');
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('qr_image');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_qr_codes');
    }
};
