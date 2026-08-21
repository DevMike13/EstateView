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
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->dropUnique(
                'client_appointments_appointment_date_appointment_time_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->unique([
                'appointment_date',
                'appointment_time',
            ]);
        });
    }
};
