<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            if (!Schema::hasColumn('credits', 'phone_name')) {
                $table->string('phone_name')->after('customer_id');
            }
            if (!Schema::hasColumn('credits', 'phone_cost')) {
                $table->decimal('phone_cost', 12, 2)->default(0)->after('phone_name');
            }

            if (Schema::hasColumn('credits', 'phone_id')) {
                try {
                    DB::statement('ALTER TABLE credits DROP FOREIGN KEY credits_phone_id_foreign');
                } catch (\Throwable $e) {}
                $table->dropColumn('phone_id');
            }

            if (Schema::hasColumn('credits', 'phone_brand')) {
                $table->dropColumn('phone_brand');
            }
            if (Schema::hasColumn('credits', 'phone_model')) {
                $table->dropColumn('phone_model');
            }
        });

        if (Schema::hasTable('phones')) {
            Schema::drop('phones');
        }
    }

    public function down(): void
    {
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->string('display_name')->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->unsignedBigInteger('phone_id')->nullable()->after('customer_id');
            $table->foreign('phone_id')->references('id')->on('phones')->nullOnDelete();

            if (Schema::hasColumn('credits', 'phone_cost')) {
                $table->dropColumn('phone_cost');
            }
            if (Schema::hasColumn('credits', 'phone_name')) {
                $table->dropColumn('phone_name');
            }
        });
    }
};
