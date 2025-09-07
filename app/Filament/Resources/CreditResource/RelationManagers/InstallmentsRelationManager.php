<?php

namespace App\Filament\Resources\CreditResource\RelationManagers;

use App\Models\Installment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';
    protected static ?string $title = 'Jadwal Cicilan';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('installment_no')
            ->columns([
                Tables\Columns\TextColumn::make('installment_no')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('due_date')->label('Jatuh Tempo')->date()->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.')),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'paid', 'warning' => 'pending'])
                    ->formatStateUsing(fn ($s) => $s === 'paid' ? 'Lunas' : 'Pending'),
                Tables\Columns\TextColumn::make('paid_at')->label('Dibayar')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('markPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Installment $r) => $r->status !== 'paid')
                    ->action(fn (Installment $r) => $r->markPaid()),

                Tables\Actions\Action::make('markPending')
                    ->label('Batalkan Lunas')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn (Installment $r) => $r->status === 'paid')
                    ->action(fn (Installment $r) => $r->markPending()),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulkPaid')->label('Tandai Terpilih Lunas')->color('success')
                    ->action(fn ($records) => $records->each->markPaid()),
                Tables\Actions\BulkAction::make('bulkPending')->label('Tandai Terpilih Pending')->color('warning')
                    ->action(fn ($records) => $records->each->markPending()),
            ]);
    }
}
