<?php

namespace App\Console\Commands;

use App\Enums\InstallmentStatus;
use App\Models\Installment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MarkInstallmentsOverdue extends Command
{
    protected $signature = 'installments:mark-overdue';
    protected $description = 'Mark past-due pending installments as overdue';

    public function handle(): int
    {
        $today = Carbon::today();
        $updated = Installment::where('status', InstallmentStatus::PENDING)
            ->whereDate('due_date', '<', $today)
            ->update(['status' => InstallmentStatus::OVERDUE]);

        $this->info("Updated {$updated} installments to overdue.");
        return self::SUCCESS;
    }
}
