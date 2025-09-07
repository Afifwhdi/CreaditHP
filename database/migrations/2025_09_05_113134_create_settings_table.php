<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Dilla Store');
            $table->string('logo_path')->nullable();
            $table->json('default_tenors')->nullable(); 
            $table->unsignedInteger('reminder_days_before')->default(1); 
            $table->string('whatsapp_sender')->nullable(); 
            $table->text('whatsapp_template')->nullable(); 
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('currency')->default('IDR');
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::table('settings')->insert([
            'store_name' => 'Dilla Store',
            'default_tenors' => json_encode([6,8,10]),
            'reminder_days_before' => 1,
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
