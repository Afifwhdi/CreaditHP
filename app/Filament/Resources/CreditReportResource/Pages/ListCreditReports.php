<?php

namespace App\Filament\Resources\CreditReportResource\Pages;

use App\Filament\Resources\CreditReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditReports extends ListRecords
{
    protected static string $resource = CreditReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data) {
                    $type = $data['report_type'] ?? 'overdue';

                    $label = match ($type) {
                        'overdue'   => 'Tunggakan',
                        'paid'      => 'Pembayaran Lunas',
                        'customers' => 'Customer',
                        default     => 'Laporan',
                    };

                    $range = '';
                    if (!empty($data['start_date']) && !empty($data['end_date'])) {
                        $range = sprintf(' %s s.d. %s', $data['start_date'], $data['end_date']);
                    }

                    if ($type === 'customers' && !empty($data['customer_status'])) {
                        $label .= ' ('.ucfirst($data['customer_status']).')';
                    }

                    $data['name'] = trim($label.$range);
                    $data['path_file'] = '';

                    return $data;
                }),
        ];
    }
}
