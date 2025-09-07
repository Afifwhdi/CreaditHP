<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            foreach ([
                'interest_rate_year',
                'admin_fee',
                'insurance_fee',
                'other_fee',
                'commission_fee',
            ] as $col) {
                if (Schema::hasColumn('credits', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            if (!Schema::hasColumn('credits', 'interest_rate_year')) {
                $table->decimal('interest_rate_year', 8, 2)->default(0);
            }
            if (!Schema::hasColumn('credits', 'admin_fee')) {
                $table->bigInteger('admin_fee')->default(0);
            }
            if (!Schema::hasColumn('credits', 'insurance_fee')) {
                $table->bigInteger('insurance_fee')->default(0);
            }
            if (!Schema::hasColumn('credits', 'other_fee')) {
                $table->bigInteger('other_fee')->default(0);
            }
            if (!Schema::hasColumn('credits', 'commission_fee')) {
                $table->bigInteger('commission_fee')->default(0);
            }
        });
    }
};
