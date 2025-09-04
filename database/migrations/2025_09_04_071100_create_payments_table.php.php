<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->foreignId('installment_id')
                ->constrained('installments')
                ->cascadeOnDelete()
                ->unique();

            $table->string('method'); 
            $table->decimal('amount', 12, 2);
            $table->string('proof_url')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('receipt_no')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'method']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
