<?php

namespace App\Exports;

use App\Models\Installment;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OverdueInstallmentsExport implements FromQuery, WithHeadings
{
    public function __construct(public ?string $untilDate = null) {}

    public function query()
    {
        $q = Installment::query()
            ->with(['customer'])
            ->whereNull('paid_at');

        if ($this->untilDate) {
            $q->whereDate('due_date', '<=', $this->untilDate);
        }

        return $q->select(['id','customer_id','due_date','amount']);
    }

    public function headings(): array
    {
        return ['ID Cicilan', 'Customer ID', 'Jatuh Tempo', 'Nominal'];
    }
}
