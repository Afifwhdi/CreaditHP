<?php

namespace App\Observers;

use App\Models\Credit;
use App\Models\Installment;
use Carbon\Carbon;

class CreditObserver
{
    public function creating(Credit $credit): void
    {
        $this->hydrateDerivedFields($credit);
        $this->ensureFirstDueDate($credit);
    }

    public function updating(Credit $credit): void
    {
        $this->hydrateDerivedFields($credit);
        $this->ensureFirstDueDate($credit);
    }

    public function created(Credit $credit): void
    {
        $this->seedInstallments($credit);
    }

    private function hydrateDerivedFields(Credit $credit): void
    {
        // DP diperlakukan sebagai cicilan/bulan
        $dp    = (float) ($credit->down_payment ?? 0);
        $tenor = (int)   ($credit->tenor ?? 0);

        $tenor = max(1, $tenor);
        $dp    = max(0.0, $dp);

        $principal          = $dp * $tenor;
        $installment        = $dp;
        $totalPayable       = $principal;

        $credit->principal          = $principal;
        $credit->installment_amount = $installment;
        $credit->total_payable      = $totalPayable;
        // $credit->price -> tetap dari form, tidak dipakai hitung
    }

    private function ensureFirstDueDate(Credit $credit): void
    {
        if (! $credit->contract_date || ! $credit->due_day) {
            return;
        }

        $contractDate = Carbon::parse($credit->contract_date);
        $dueDay       = (int) min(28, max(1, (int) $credit->due_day));
        $candidate    = $contractDate->copy()->day($dueDay);

        if ($candidate->lt($contractDate)) {
            $candidate->addMonthNoOverflow()->day($dueDay);
        }

        $credit->first_due_date = $candidate->startOfDay();
    }

    private function seedInstallments(Credit $credit): void
    {
        if ($credit->installments()->exists()) {
            return;
        }

        $tenor  = max(1, (int) $credit->tenor);
        $amount = (float) $credit->installment_amount; // = DP per bulan
        $dueDay = min(28, (int) $credit->due_day);

        $due = $credit->first_due_date
            ? Carbon::parse($credit->first_due_date)
            : Carbon::parse($credit->contract_date)->addMonthNoOverflow()->day($dueDay);

        for ($i = 1; $i <= $tenor; $i++) {
            Installment::create([
                'credit_id'      => $credit->id,
                'installment_no' => $i,
                'due_date'       => $due->copy(),
                'amount'         => $amount,
                'status'         => 'pending',
            ]);

            $due->addMonthNoOverflow()->day($dueDay);
        }
    }
}
