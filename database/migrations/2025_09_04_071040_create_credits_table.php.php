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
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phone_id')->nullable()->constrained()->nullOnDelete();
            $table->date('contract_date')->default(now());
            $table->decimal('price', 12, 2);              
            $table->decimal('down_payment', 12, 2)->default(0);
            $table->unsignedSmallInteger('tenor');        
            $table->unsignedTinyInteger('due_day');        
            $table->date('first_due_date');                
            $table->string('status')->default('active');   
            $table->text('notes')->nullable();   
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
