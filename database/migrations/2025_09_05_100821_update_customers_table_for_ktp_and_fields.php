<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Email kalau memang tidak dipakai bisa di-drop
            $table->dropColumn('email');

            // national_id ubah jadi path foto KTP
            $table->renameColumn('national_id', 'ktp_path');

            // pastikan address lebih fleksibel
            $table->text('address')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // rollback: balikin lagi
            $table->string('email')->nullable();

            $table->renameColumn('ktp_path', 'national_id');

            $table->string('address')->nullable()->change();
        });
    }
};
