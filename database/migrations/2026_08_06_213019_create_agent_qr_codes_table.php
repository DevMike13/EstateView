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
        Schema::create('agent_qr_codes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('label')->nullable();

            /*
             * Examples:
             * BDO, BPI, Metrobank, GCash, Maya
             */
            $table->string('provider_name');

            $table->string('account_name');

            $table->string('account_number')
                ->nullable();

            /*
             * Store only:
             * agent-qr-codes/filename.jpg
             *
             * Do not store /storage/ in this column.
             */
            $table->string('qr_image_path');

            $table->boolean('is_primary')
                ->default(false);

            $table->timestamps();

            $table->index([
                'agent_id',
                'is_primary',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_qr_codes');
    }
};
