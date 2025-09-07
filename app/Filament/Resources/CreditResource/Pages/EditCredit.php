<?php

namespace App\Filament\Resources\CreditResource\Pages;

use App\Filament\Resources\CreditResource;
use Filament\Resources\Pages\EditRecord;

class EditCredit extends EditRecord
{
    protected static string $resource = CreditResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $dp    = (float) ($data['down_payment'] ?? 0);
        $tenor = (int)   ($data['tenor'] ?? 1);

        $principal     = max(0, $dp * $tenor);
        $installment   = max(0, $dp);
        $totalPayable  = $principal;

        $data['principal']          = $principal;
        $data['installment_amount'] = $installment;
        $data['total_payable']      = $totalPayable;

        return $data;
    }
}
