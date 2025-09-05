<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('credits', function (Blueprint $t) {
            $t->decimal('interest_rate_year', 5, 2)->default(0); 
            $t->decimal('admin_fee', 12, 2)->default(0);
            $t->decimal('insurance_fee', 12, 2)->default(0);
            $t->decimal('other_fee', 12, 2)->default(0);
            $t->decimal('commission_fee', 12, 2)->default(0); 

            $t->decimal('principal', 12, 2)->default(0);        
            $t->decimal('monthly_interest', 12, 2)->default(0); 
            $t->decimal('installment_amount', 12, 2)->default(0); 
            $t->decimal('total_interest', 12, 2)->default(0);
            $t->decimal('total_payable', 12, 2)->default(0);    
            $t->decimal('expected_profit', 12, 2)->default(0);  
        });
    }
    public function down(): void {
        Schema::table('credits', function (Blueprint $t) {
            $t->dropColumn([
                'interest_rate_year','admin_fee','insurance_fee','other_fee','commission_fee',
                'principal','monthly_interest','installment_amount','total_interest','total_payable','expected_profit',
            ]);
        });
    }
};