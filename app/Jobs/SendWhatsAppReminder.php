<?php

namespace App\Jobs;

use App\Contracts\WhatsAppGateway;
use App\Models\Customer;
use App\Models\Installment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWhatsAppReminder implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Customer $customer,
        public Installment $installment,
        public string $type // 'H-3' atau 'H-0'
    ) {}

    public function handle(): void
    {
        $due = $this->installment->due_date->format('d M Y');
        $amt = number_format((float) $this->installment->amount, 2, ',', '.');

        $msg = $this->type === 'H-3'
            ? "Halo {$this->customer->name}, pengingat H-3: Angsuran jatuh tempo {$due} sebesar Rp{$amt}."
            : "Halo {$this->customer->name}, hari ini jatuh tempo angsuran Rp{$amt} ({$due}).";

        app(WhatsAppGateway::class)->send($this->customer->phone, $msg);
    }
}
