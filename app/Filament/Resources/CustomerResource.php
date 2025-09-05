<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon  = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'Pelanggan';
    protected static ?int    $navigationSort  = 10;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'address'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Pelanggan')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama')
                        ->required()
                        ->maxLength(120),

                    Forms\Components\TextInput::make('phone')
                        ->label('No. WhatsApp')
                        ->tel()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Gunakan format (08xxxx)'),

                    Forms\Components\Textarea::make('address')
                        ->label('Alamat')
                        ->rows(3),

                    Forms\Components\FileUpload::make('ktp_path')
                        ->label('Foto KTP')
                        ->image()
                        ->disk('public')               
                        ->directory('customers/ktp')    
                        ->visibility('public')          
                        ->imageEditor()
                        ->imageResizeMode('contain')
                        ->imageResizeTargetWidth('1200')
                        ->imageResizeTargetHeight('1200')
                        ->maxSize(5 * 1024)             
                        ->helperText('JPG/PNG maksimal 5MB')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('ktp_path')
                    ->label('KTP')
                    ->disk('public')
                    ->square()
                    ->height(44),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('No. WA')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->address),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('ktp_uploaded')
                    ->label('Dengan Foto KTP')
                    ->placeholder('Semua')
                    ->trueLabel('Ada')
                    ->falseLabel('Tidak ada')
                    ->queries(
                        true:  fn ($q) => $q->whereNotNull('ktp_path'),
                        false: fn ($q) => $q->whereNull('ktp_path'),
                        blank: fn ($q) => $q
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) \App\Models\Customer::count();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
        ];
    }
}
