<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->string('created_by_role')
                ->nullable()
                ->after('created_by');

            $table->timestamp('client_confirmed_at')
                ->nullable()
                ->after('status');

            $table->timestamp('client_declined_at')
                ->nullable()
                ->after('client_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('client_appointments', function (Blueprint $table) {
            $table->dropForeign(['created_by']);

            $table->dropColumn([
                'created_by',
                'created_by_role',
                'client_confirmed_at',
                'client_declined_at',
            ]);
        });
    }
};