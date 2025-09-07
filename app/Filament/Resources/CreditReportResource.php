<?php

namespace App\Filament\Resources;

use App\Models\CreditReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\CreditReportResource\Pages;
use Filament\Tables\Actions\Action;

class CreditReportResource extends Resource
{
    protected static ?string $model = CreditReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Reports & Export';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'Reports';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Setting Laporan')
                ->schema([
                    Forms\Components\ToggleButtons::make('report_type')
                        ->label('Tipe Laporan')
                        ->options([
                            'overdue'   => 'Tunggakan',
                            'paid'      => 'Pembayaran Lunas',
                            'customers' => 'Customer',
                        ])
                        ->colors([
                            'overdue'   => 'danger',
                            'paid'      => 'success',
                            'customers' => 'info',
                        ])
                        ->required()
                        ->grouped()
                        ->live(),

                    // Range tanggal untuk type paid (opsional bisa dipakai overdue/customers juga)
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->visible(fn (callable $get) => in_array($get('report_type'), ['paid','overdue','customers'])),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->visible(fn (callable $get) => in_array($get('report_type'), ['paid','overdue','customers'])),
                    ]),

                    // Filter status untuk report customers
                    Forms\Components\Select::make('customer_status')
                        ->label('Status Customer')
                        ->options([
                            'active'    => 'Aktif',
                            'lunas'     => 'Lunas',
                            'blacklist' => 'Blacklist',
                        ])
                        ->native(false)
                        ->visible(fn (callable $get) => $get('report_type') === 'customers'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama/Kode Laporan')
                    ->weight('semibold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('report_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'overdue'   => 'Tunggakan',
                        'paid'      => 'Pembayaran Lunas',
                        'customers' => 'Customer',
                        default     => ucfirst($state),
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'overdue'   => 'heroicon-o-exclamation-triangle',
                        'paid'      => 'heroicon-o-check-circle',
                        'customers' => 'heroicon-o-user-group',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'overdue'   => 'danger',
                        'paid'      => 'success',
                        'customers' => 'info',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('start_date')->label('Dari')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->label('Sampai')->date()->sortable(),
                Tables\Columns\TextColumn::make('customer_status')
                    ->label('Status Cust.')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'info',
                        'lunas'     => 'success',
                        'blacklist' => 'danger',
                        default     => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (CreditReport $record) => route('credit-reports.download', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditReports::route('/'),
            // 'create' => Pages\CreateCreditReport::route('/create'),
            // 'edit' => Pages\EditCreditReport::route('/{record}/edit'),
        ];
    }
}
