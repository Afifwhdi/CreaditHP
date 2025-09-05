<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $t) {
            $t->index(['customer_id', 'status', 'created_at'], 'credits_customer_status_created_idx');
        });
        Schema::table('payments', function (Blueprint $t) {
            $t->index(['customer_id', 'status', 'created_at'], 'payments_customer_status_created_idx');
        });
        Schema::table('phones', function (Blueprint $t) {
            $t->index(['brand', 'model'], 'phones_brand_model_idx');
        });
    }

    public function down(): void
    {
        Schema::table('credits', function (Blueprint $t) {
            $t->dropIndex('credits_customer_status_created_idx');
        });
        Schema::table('payments', function (Blueprint $t) {
            $t->dropIndex('payments_customer_status_created_idx');
        });
        Schema::table('phones', function (Blueprint $t) {
            $t->dropIndex('phones_brand_model_idx');
        });
    }
};
