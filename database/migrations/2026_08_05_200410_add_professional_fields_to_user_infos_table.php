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
        Schema::table('user_infos', function (Blueprint $table) {
            $table->decimal('commission_percentage', 5, 2)
                ->nullable()
                ->after('phone');

            $table->string('professional_agent_id')
                ->nullable()
                ->unique()
                ->after('commission_percentage');

            $table->string('real_estate_license_number')
                ->nullable()
                ->unique()
                ->after('professional_agent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropUnique([
                'professional_agent_id',
            ]);

            $table->dropUnique([
                'real_estate_license_number',
            ]);

            $table->dropColumn([
                'commission_percentage',
                'professional_agent_id',
                'real_estate_license_number',
            ]);
        });
    }
};
