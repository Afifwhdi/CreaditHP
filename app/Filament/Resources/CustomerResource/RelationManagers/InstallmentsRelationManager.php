<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Enums\InstallmentStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use filament\Tables\Actions\Action;
use filament\Tables\Columns\TextColumn;
use Filament\Resources\Components\Tab;


class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';
    protected static ?string $title = 'Angsuran';

    public function table(Tables\Table $table): Tables\Table
    {
        $today = Carbon::today();
        $h3 = $today->copy()->addDays(3);

        return $table
            ->modifyQueryUsing(fn (Builder $q) => $q->with('credit.phone'))
            ->columns([
                Tables\Columns\TextColumn::make('credit.phone.display_name')->label('HP'),
                Tables\Columns\TextColumn::make('installment_no')->label('Ke-')->sortable(),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('IDR'),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->tabs([
                'Semua' => Tab::make(),
                'H-3 Jatuh Tempo' => Tab::make()
                    ->modifyQueryUsing(fn (Builder $q) => $q->whereDate('due_date', $h3)->where('status', InstallmentStatus::PENDING)),
                'Jatuh Tempo Hari Ini' => Tab::make()
                    ->modifyQueryUsing(fn (Builder $q) => $q->whereDate('due_date', $today)->where('status', InstallmentStatus::PENDING)),
            ])
            ->actions([
                Tables\Actions\Action::make('remind')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-paper-airplane')
                    ->action(function ($record) {
                        $c = $record->credit->customer;
                        $due = $record->due_date->format('d M Y');
                        $amt = number_format((float) $record->amount, 2, ',', '.');
                        $msg = "Halo {$c->name}, angsuran ke-{$record->installment_no} jatuh tempo {$due} sebesar Rp{$amt}.";
                        app(\App\Contracts\WhatsAppGateway::class)->send($c->phone, $msg);
                    }),
            ]);
    }
}
