<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditResource\Pages;
use App\Models\Credit;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CreditResource extends Resource
{
    protected static ?string $model = Credit::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Kredit';
    protected static ?string $navigationLabel = 'Kredit HP';

    public static function getGloballySearchableAttributes(): array
    {
        return ['customer.name', 'phone_name'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            self::sectionKontrak(),
            self::sectionRingkasan(),
        ]);
    }

    private static function sectionKontrak(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Data Kontrak')
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Pelanggan')
                    ->options(fn () => Customer::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('phone_name')
                    ->label('Nama / Tipe HP')
                    ->placeholder('Contoh: Samsung A15 6/128')
                    ->required(),

                Forms\Components\DatePicker::make('contract_date')
                    ->label('Tanggal Kontrak')
                    ->default(now())
                    ->required(),

                Forms\Components\TextInput::make('price')
                    ->label('Harga Kredit (Total)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->live(debounce: 300),

                Forms\Components\TextInput::make('down_payment')
                    ->label('DP / Cicilan per Bulan')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->live(debounce: 300),

                Forms\Components\TextInput::make('tenor')
                    ->label('Tenor (bulan)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(60)
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('due_day')
                    ->label('Jatuh Tempo (1–28)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(28)
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    private static function sectionRingkasan(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Ringkasan Perhitungan')
            ->schema([
                Forms\Components\View::make('filament.components.credit-summary')
                    ->viewData(function (callable $get) {
                        $dp    = (float) ($get('down_payment') ?? 0);
                        $tenor = (int)   ($get('tenor') ?? 1);

                        $principal     = max(0, $dp * $tenor); 
                        $installment   = max(0, $dp);         
                        $totalPayable  = $principal;           

                        return [
                            'calc' => [
                                'principal'           => $principal,
                                'installment'         => $installment,
                                'total_installments'  => $principal,
                                'total_payable'       => $totalPayable,
                            ],
                        ];
                    }),
            ])
            ->collapsible()
            ->collapsed();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_name')
                    ->label('HP')
                    ->searchable(),

                Tables\Columns\TextColumn::make('contract_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Kredit')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')) // <= $state
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenor')
                    ->label('Tenor')
                    ->sortable(),

                Tables\Columns\TextColumn::make('installment_amount')
                    ->label('Cicilan/Bln')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('total_payable')
                    ->label('Total Bayar')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                // Progress
                Tables\Columns\TextColumn::make('paid_count')
                    ->label('Sudah Bayar')
                    ->getStateUsing(fn ($record) => $record->paid_count),

                Tables\Columns\TextColumn::make('remaining_count')
                    ->label('Sisa Bulan')
                    ->getStateUsing(fn ($record) => $record->remaining_count),

                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Total Dibayar')
                    ->getStateUsing(fn ($record) => $record->total_paid)
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('total_remaining')
                    ->label('Sisa Nominal')
                    ->getStateUsing(fn ($record) => $record->total_remaining)
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format((float) $state, 0, ',', '.')),

                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\CreditResource\RelationManagers\InstallmentsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCredits::route('/'),
            // 'create' => Pages\CreateCredit::route('/create'),
            // 'edit'   => Pages\EditCredit::route('/{record}/edit'),
        ];
    }
}
