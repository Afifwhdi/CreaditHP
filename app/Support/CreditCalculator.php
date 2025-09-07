<?php

namespace App\Support;

class CreditCalculator
{
    public static function compute(float $monthly, int $tenor, float $dp = 0): array
    {
        $tenor      = max(1, (int) $tenor);
        $monthly    = max(0.0, (float) $monthly);
        $dp         = max(0.0, (float) $dp);

        $principal          = round($monthly * $tenor, 2);
        $totalPayable       = round($dp + $principal, 2);

        return [
            'principal'           => $principal,
            'installment'         => $monthly,
            'total_installments'  => $principal,
            'total_payable'       => $totalPayable,
            'derived_price'       => $totalPayable, 
        ];
    }
}
