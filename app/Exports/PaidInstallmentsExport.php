<?php

namespace App\Exports;

use App\Models\Installment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaidInstallmentsExport implements FromQuery, WithHeadings
{
    public function __construct(public ?string $startDate = null, public ?string $endDate = null) {}

    public function query()
    {
        $q = Installment::query()
            ->with(['customer'])
            ->whereNotNull('paid_at');

        if ($this->startDate) {
            $q->whereDate('paid_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $q->whereDate('paid_at', '<=', $this->endDate);
        }

        return $q->select(['id','customer_id','paid_at','amount']);
    }

    public function headings(): array
    {
        return ['ID Cicilan', 'Customer ID', 'Dibayar Pada', 'Nominal'];
    }
}
