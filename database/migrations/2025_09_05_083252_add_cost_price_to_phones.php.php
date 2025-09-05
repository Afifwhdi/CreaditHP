<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('phones', function (Blueprint $t) {
            $t->decimal('cost_price', 12, 2)->default(0)->after('cash_price'); // harga modal
        });
    }
    public function down(): void {
        Schema::table('phones', fn (Blueprint $t) => $t->dropColumn('cost_price'));
    }
};
