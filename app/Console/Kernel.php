<?php

namespace App\Console;

use App\Enums\InstallmentStatus;
use App\Jobs\SendWhatsAppReminder;
use App\Models\Installment;
use Illuminate\Support\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    protected function schedule(Schedule $schedule): void {

        $schedule->call(function () {
            $today = Carbon::today(); $h3 = Carbon::today()->addDays(3);
            $ins3 = Installment::with('credit.customer')->whereDate('due_date',$h3)->where('status',InstallmentStatus::PENDING)->get();
            foreach ($ins3 as $i) dispatch(new SendWhatsAppReminder($i->credit->customer,$i,'H-3'));
            $ins0 = Installment::with('credit.customer')->whereDate('due_date',$today)->where('status',InstallmentStatus::PENDING)->get();
            foreach ($ins0 as $i) dispatch(new SendWhatsAppReminder($i->credit->customer,$i,'H-0'));
        })->timezone('Asia/Jakarta')->dailyAt('08:00');

        $schedule->command('installments:mark-overdue')
            ->timezone('Asia/Jakarta')
            ->dailyAt('00:30');
    }
}
