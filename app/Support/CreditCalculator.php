<?php

namespace App\Support;

class CreditCalculator
{
    public static function compute(
        float $price, float $dp, int $tenor,
        float $interestYear = 0, float $admin = 0, float $insurance = 0,
        float $other = 0, float $commission = 0, float $cost = 0
    ): array {
        $principal = max(0, $price - $dp);
        $rateMonth = ($interestYear > 0 ? $interestYear / 12 / 100 : 0);
        $monthlyInterest = round($principal * $rateMonth, 2);
        $principalPart = round($principal / max(1,$tenor), 2);
        $installment = round($principalPart + $monthlyInterest, 2);
        $totalInterest = round($monthlyInterest * $tenor, 2);
        $totalInstallments = round($installment * $tenor, 2);
        $totalFees = $admin + $insurance + $other + $commission;
        $totalPayable = round($dp + $totalInstallments + $totalFees, 2);

        $expectedProfit = round(($dp + $totalInstallments) - ($cost + $admin + $insurance + $other + $commission), 2);

        return [
            'principal'         => $principal,
            'monthly_interest'  => $monthlyInterest,
            'installment'       => $installment,
            'total_interest'    => $totalInterest,
            'total_installments'=> $totalInstallments,
            'total_fees'        => $totalFees,
            'total_payable'     => $totalPayable,
            'expected_profit'   => $expectedProfit,
        ];
    }
}