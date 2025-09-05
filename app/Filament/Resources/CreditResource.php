<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditResource\Pages;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Phone;
use App\Support\CreditCalculator;
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
        return ['customer.name', 'phone.model'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Kontrak')
                ->schema([
                    Forms\Components\Select::make('customer_id')
                        ->label('Pelanggan')
                        ->options(fn () => Customer::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()->required()->native(false),

                    Forms\Components\Select::make('phone_id')
                        ->label('HP')
                        ->options(fn () => Phone::query()->orderBy('brand')->get()->pluck('display_name', 'id'))
                        ->searchable()->native(false),

                    Forms\Components\DatePicker::make('contract_date')
                        ->label('Tanggal Kontrak')
                        ->default(now())
                        ->required(),

                    Forms\Components\TextInput::make('price')->label('Harga Kredit')
                        ->numeric()->prefix('Rp')->required()->live(debounce: 400)
                        ->disabled(fn ($record) => filled($record?->id)),

                    Forms\Components\TextInput::make('down_payment')->label('DP')
                        ->numeric()->prefix('Rp')->default(0)->live(debounce: 400)
                        ->rules(['lte:price'])
                        ->disabled(fn ($record) => filled($record?->id)),

                    Forms\Components\TextInput::make('tenor')->label('Tenor (bulan)')
                        ->numeric()->minValue(1)->maxValue(36)->required()->live()
                        ->rules(['integer','min:1'])
                        ->disabled(fn ($record) => filled($record?->id)),

                    Forms\Components\TextInput::make('due_day')->label('Jatuh Tempo (1–28)')
                        ->numeric()->minValue(1)->maxValue(28)->required()
                        ->rules(['integer','min:1','max:28'])
                        ->disabled(fn ($record) => filled($record?->id)),
                ])->columns(2),

            Forms\Components\Section::make('Biaya & Parameter')
                ->schema([
                    Forms\Components\TextInput::make('interest_rate_year')->label('Bunga (%/tahun)')
                        ->numeric()->default(0)->live(),
                    Forms\Components\TextInput::make('admin_fee')->numeric()->prefix('Rp')->default(0)->live(),
                    Forms\Components\TextInput::make('insurance_fee')->numeric()->prefix('Rp')->default(0)->live(),
                    Forms\Components\TextInput::make('other_fee')->numeric()->prefix('Rp')->default(0)->live(),
                    Forms\Components\TextInput::make('commission_fee')->numeric()->prefix('Rp')->default(0)->live(),
                    Forms\Components\Textarea::make('notes')->columnSpanFull(),
                ])->columns(3),

            Forms\Components\Section::make('Ringkasan Perhitungan')
                ->schema([
                    Forms\Components\View::make('filament.components.credit-summary')
                        ->viewData(fn (callable $get) => [
                            'calc' => CreditCalculator::compute(
                                (float) $get('price'),
                                (float) $get('down_payment'),
                                (int)   $get('tenor'),
                                (float) $get('interest_rate_year'),
                                (float) $get('admin_fee'),
                                (float) $get('insurance_fee'),
                                (float) $get('other_fee'),
                                (float) $get('commission_fee'),
                                (float) optional(Phone::find($get('phone_id')))->cost_price ?? 0
                            ),
                        ]),
                ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')->label('Pelanggan')->searchable(),
                Tables\Columns\TextColumn::make('phone.display_name')->label('HP')->toggleable(),
                Tables\Columns\TextColumn::make('contract_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('price')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('tenor')->label('Tenor')->sortable(),
                Tables\Columns\TextColumn::make('installment_amount')->label('Cicilan/Bln')->money('IDR'),
                Tables\Columns\TextColumn::make('expected_profit')->label('Estimasi Profit')->money('IDR')->color('success'),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'active'    => 'Active',
                    'completed' => 'Completed',
                    'defaulted' => 'Defaulted',
                    'cancelled' => 'Cancelled',
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCredits::route('/'),
            'create' => Pages\CreateCredit::route('/create'),
            // 'view'   => Pages\ViewCredit::route('/{record}'),
            'edit'   => Pages\EditCredit::route('/{record}/edit'),
        ];
    }
}