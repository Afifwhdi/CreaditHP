<?php

namespace App\Filament\Resources\CreditReportResource\Pages;

use App\Filament\Resources\CreditReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCreditReport extends EditRecord
{
    protected static string $resource = CreditReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
