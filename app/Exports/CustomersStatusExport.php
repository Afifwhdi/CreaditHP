<?php

namespace App\Exports;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersStatusExport implements FromQuery, WithHeadings
{
    public function __construct(public ?string $status = null) {}

    public function query()
    {
        $q = Customer::query(); 
        
        if ($this->status) {
            $q->where('status', $this->status); 
        }

        return $q->select(['id','name','phone','status']);
    }

    public function headings(): array
    {
        return ['ID', 'Nama', 'HP/WA', 'Status'];
    }
}
