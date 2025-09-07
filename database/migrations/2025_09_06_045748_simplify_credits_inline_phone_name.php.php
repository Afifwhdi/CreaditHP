<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credits', function (Blueprint $table) {
            // Tambah phone_name
            if (!Schema::hasColumn('credits', 'phone_name')) {
                $table->string('phone_name')->after('customer_id');
            }

            // Drop relasi HP
            if (Schema::hasColumn('credits', 'phone_id')) {
                // coba lepas FK (nama constraint bisa beda)
                try { DB::statement('ALTER TABLE credits DROP FOREIGN KEY credits_phone_id_foreign'); } catch (\Throwable $e) {}
                try { DB::statement('ALTER TABLE credits DROP FOREIGN KEY credits_phone_foreign'); } catch (\Throwable $e) {}
                $table->dropColumn('phone_id');
            }

            // Drop biaya & bunga
            foreach ([
                'interest_rate_year','admin_fee','insurance_fee','other_fee','commission_fee',
                'monthly_interest','total_interest','expected_profit',
                // kalau pernah ada brand/model:
                'phone_brand','phone_model',
                // kalau pernah ada phone_cost:
                'phone_cost',
            ] as $col) {
                if (Schema::hasColumn('credits', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // Drop tabel phones kalau ada
        if (Schema::hasTable('phones')) {
            Schema::drop('phones');
        }
    }

    public function down(): void
    {
        // Rekreasi tabel phones minimum (opsional)
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->string('display_name')->nullable();
            $table->timestamps();
        });

        Schema::table('credits', function (Blueprint $table) {
            // Kembalikan phone_id
            if (!Schema::hasColumn('credits', 'phone_id')) {
                $table->unsignedBigInteger('phone_id')->nullable()->after('customer_id');
                $table->foreign('phone_id')->references('id')->on('phones')->nullOnDelete();
            }

            // Tambahkan kembali kolom yang di-drop (default 0/nullable)
            if (!Schema::hasColumn('credits', 'interest_rate_year')) $table->decimal('interest_rate_year', 8, 2)->default(0);
            if (!Schema::hasColumn('credits', 'admin_fee')) $table->bigInteger('admin_fee')->default(0);
            if (!Schema::hasColumn('credits', 'insurance_fee')) $table->bigInteger('insurance_fee')->default(0);
            if (!Schema::hasColumn('credits', 'other_fee')) $table->bigInteger('other_fee')->default(0);
            if (!Schema::hasColumn('credits', 'commission_fee')) $table->bigInteger('commission_fee')->default(0);
            if (!Schema::hasColumn('credits', 'monthly_interest')) $table->decimal('monthly_interest', 12, 2)->default(0);
            if (!Schema::hasColumn('credits', 'total_interest')) $table->decimal('total_interest', 12, 2)->default(0);
            if (!Schema::hasColumn('credits', 'expected_profit')) $table->decimal('expected_profit', 12, 2)->default(0);

            // Hapus phone_name (balik ke relasi)
            if (Schema::hasColumn('credits', 'phone_name')) $table->dropColumn('phone_name');
        });
    }
};
