<?php

namespace App\Observers;

use App\Enums\InstallmentStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Installment;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class PaymentObserver
{
    public function creating(Payment $p): void
    {
        if ($p->method === PaymentMethod::CASH) {
            $p->status = PaymentStatus::VERIFIED;
            $p->verified_at = now();
            $p->verified_by = Auth::id();
        }
    }

    public function created(Payment $p): void
    {
        $this->markPaidIfVerified($p);
    }

    public function updated(Payment $p): void
    {
        if ($p->isDirty('status') && $p->status === PaymentStatus::VERIFIED) {
            $this->markPaidIfVerified($p);

            // Activity log (butuh spatie/laravel-activitylog terpasang & dimigrasi)
            activity('payments')
                ->performedOn($p)
                ->causedBy(auth::user())
                ->withProperties([
                    'status'         => $p->status,
                    'amount'         => (float) $p->amount,
                    'installment_id' => $p->installment_id,
                ])->log('Payment verified');
        }
    }

    protected function markPaidIfVerified(Payment $p): void
    {
        if ($p->status !== PaymentStatus::VERIFIED) {
            return;
        }

        DB::transaction(function () use ($p) {
            $ins = Installment::lockForUpdate()->find($p->installment_id);
            if ($ins && $ins->status !== InstallmentStatus::PAID) {
                $ins->update([
                    'status'  => InstallmentStatus::PAID,
                    'paid_at' => now(),
                ]);
            }
        });
    }
}
