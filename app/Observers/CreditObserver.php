<?php

namespace App\Observers;

use App\Models\Credit;
use App\Models\Installment;
use App\Support\CreditCalculator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CreditObserver
{
    public function creating(Credit $credit): void
    {
        $contract = Carbon::parse($credit->contract_date ?? now());
        $day = max(1, min(28, (int) $credit->due_day));
        $first = $contract->copy()->day($day);
        if ($first->lessThanOrEqualTo($contract)) {
            $first->addMonthNoOverflow();
        }
        $credit->first_due_date = $first->toDateString();
    }

    public function created(Credit $credit): void
    {
        $cost = optional($credit->phone)->cost_price ?? 0.0;

        $calc = CreditCalculator::compute(
            price: (float) $credit->price,
            dp: (float) $credit->down_payment,
            tenor: (int) $credit->tenor,
            interestYear: (float) $credit->interest_rate_year,
            admin: (float) $credit->admin_fee,
            insurance: (float) $credit->insurance_fee,
            other: (float) $credit->other_fee,
            commission: (float) $credit->commission_fee,
            cost: (float) $cost,
        );

        $credit->update([
            'principal'          => $calc['principal'],
            'monthly_interest'   => $calc['monthly_interest'],
            'installment_amount' => $calc['installment'],
            'total_interest'     => $calc['total_interest'],
            'total_payable'      => $calc['total_payable'],
            'expected_profit'    => $calc['expected_profit'],
        ]);

        DB::transaction(function () use ($credit, $calc) {
            $due = Carbon::parse($credit->first_due_date);
            $total = 0.0;

            for ($i = 1; $i <= (int) $credit->tenor; $i++) {
                $amount = (float) $calc['installment'];

                if ($i === (int) $credit->tenor) {
                    $targetTotal = round($calc['installment'] * (int) $credit->tenor, 2);
                    $amount = round($targetTotal - $total, 2);
                }

                Installment::create([
                    'credit_id'      => $credit->id,
                    'installment_no' => $i,
                    'due_date'       => $due->copy(),
                    'amount'         => $amount,
                    'status'         => 'pending',
                ]);

                $total += $amount;
                $due->addMonthNoOverflow();
            }
        });
    }
}
