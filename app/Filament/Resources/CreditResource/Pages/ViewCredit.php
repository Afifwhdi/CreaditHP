
<?php

use App\Filament\Resources\CreditResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewCredit extends ViewRecord
{
    protected static string $resource = CreditResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        $fmtRp = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');

        return $infolist->schema([
            Section::make('Ringkasan')
                ->columns(4)
                ->schema([
                    TextEntry::make('customer.name')->label('Pelanggan'),
                    TextEntry::make('phone_name')->label('HP'),
                    TextEntry::make('contract_date')->label('Tgl Kontrak')->date(),
                    TextEntry::make('due_day')->label('Jatuh Tempo (hari)'),

                    TextEntry::make('price')->label('Harga Kredit')->formatStateUsing($fmtRp),
                    TextEntry::make('installment_amount')->label('Cicilan/Bln')->formatStateUsing($fmtRp),
                    TextEntry::make('total_payable')->label('Total Bayar')->formatStateUsing($fmtRp),
                    TextEntry::make('tenor')->label('Tenor (bulan)'),

                    TextEntry::make('paid_count')->label('Sudah Bayar')
                        ->state(fn ($record) => $record->paid_count),
                    TextEntry::make('remaining_count')->label('Sisa Bulan')
                        ->state(fn ($record) => $record->remaining_count),
                    TextEntry::make('total_paid')->label('Total Dibayar')
                        ->state(fn ($record) => $fmtRp($record->total_paid)),
                    TextEntry::make('total_remaining')->label('Sisa Nominal')
                        ->state(fn ($record) => $fmtRp($record->total_remaining)),
                ]),
        ]);
    }
}
