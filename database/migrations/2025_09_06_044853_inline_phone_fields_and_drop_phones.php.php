<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 3.1 Tambah kolom manual di credits
        Schema::table('credits', function (Blueprint $table) {
            if (!Schema::hasColumn('credits', 'phone_name')) {
                $table->string('phone_name')->after('customer_id');
            }
            if (!Schema::hasColumn('credits', 'phone_brand')) {
                $table->string('phone_brand')->nullable()->after('phone_name');
            }
            if (!Schema::hasColumn('credits', 'phone_model')) {
                $table->string('phone_model')->nullable()->after('phone_brand');
            }
            if (!Schema::hasColumn('credits', 'phone_cost')) {
                $table->decimal('phone_cost', 12, 2)->nullable()->after('phone_model');
            }
        });

        // 3.2 Lepas FK & drop kolom phone_id bila ada
        if (Schema::hasColumn('credits', 'phone_id')) {
            // Nama constraint FK bisa beda; aman pakai try…catch
            try {
                DB::statement('ALTER TABLE credits DROP FOREIGN KEY credits_phone_id_foreign');
            } catch (\Throwable $e) {}
            // beberapa sistem nama FK beda:
            try {
                DB::statement('ALTER TABLE credits DROP FOREIGN KEY credits_phone_foreign');
            } catch (\Throwable $e) {}

            Schema::table('credits', function (Blueprint $table) {
                $table->dropColumn('phone_id');
            });
        }

        // 3.3 (Opsional) Pindahkan data lama dari phones ke credits (kalau masih ada datanya)
        // Lewatin aja kalau tidak perlu/phones sudah kosong.

        // 3.4 Drop tabel phones
        if (Schema::hasTable('phones')) {
            Schema::drop('phones');
        }
    }

    public function down(): void
    {
        // Balikkan perubahan (merekonstruksi minimum)
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('display_name')->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->unsignedBigInteger('phone_id')->nullable()->after('customer_id');
            $table->foreign('phone_id')->references('id')->on('phones')->nullOnDelete();

            if (Schema::hasColumn('credits', 'phone_cost')) $table->dropColumn('phone_cost');
            if (Schema::hasColumn('credits', 'phone_model')) $table->dropColumn('phone_model');
            if (Schema::hasColumn('credits', 'phone_brand')) $table->dropColumn('phone_brand');
            if (Schema::hasColumn('credits', 'phone_name')) $table->dropColumn('phone_name');
        });
    }
};
