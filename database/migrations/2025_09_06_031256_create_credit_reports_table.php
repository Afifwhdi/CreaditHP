<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('credit_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->enum('report_type', ['overdue','paid','customers']);
            $table->date('start_date')->nullable(); 
            $table->date('end_date')->nullable();  
            $table->string('customer_status')->nullable(); 
            $table->string('path_file')->nullable(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_reports');
    }
};
