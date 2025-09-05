<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PhoneResource\Pages;
use App\Models\Phone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PhoneResource extends Resource
{
    protected static ?string $model = Phone::class;
    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationGroup = 'Master';
    protected static ?string $navigationLabel = 'HP';

    public static function form(Form $form): Form
    {
        return $form->schema(self::formSchema());
    }

    /** @return array<int, Forms\Components\Component> */
    private static function formSchema(): array
    {
        return [
            Forms\Components\Section::make('Data HP')
                ->schema([
                    Forms\Components\TextInput::make('brand')->required()->maxLength(80),
                    Forms\Components\TextInput::make('model')->required()->maxLength(120),
                    Forms\Components\TextInput::make('variant')->maxLength(120),
                    Forms\Components\FileUpload::make('photo_path')
                        ->label('Foto')
                        ->image()                         
                        ->disk('public')                  
                        ->directory('phones/photos')    
                        ->visibility('public')          
                        ->imageEditor()                   
                        ->imageResizeMode('contain')     
                        ->imageResizeTargetWidth('800')   
                        ->imageResizeTargetHeight('800')
                        ->maxSize(5 * 1024)               
                        ->helperText('JPG/PNG, maks 5MB')
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('cash_price')
                        ->label('Harga Cash')
                        ->numeric()->prefix('Rp')->required(),

                    Forms\Components\TextInput::make('cost_price')
                        ->label('Harga Modal')
                        ->numeric()->prefix('Rp')->required(),

                    Forms\Components\TextInput::make('stock')
                        ->numeric()
                        ->minValue(0)
                        ->default(0),
                        
                ])->columns(2),
            ];
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([

            Tables\Columns\ImageColumn::make('photo_path')
                ->label('Foto')
                ->disk('public')
                ->square()           
                ->height(48)         
                ->defaultImageUrl(url('/images/phone-placeholder.png')),
                
            Tables\Columns\TextColumn::make('display_name')
                ->label('Nama')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('cash_price')
                ->money('IDR')
                ->label('Harga Cash')
                ->sortable(),

            Tables\Columns\TextColumn::make('cost_price')
                ->money('IDR')
                ->label('Modal')
                ->sortable(),

            Tables\Columns\TextColumn::make('cash_margin')
                ->money('IDR')
                ->label('Margin Cash')
                ->color('success'),

            Tables\Columns\TextColumn::make('stock')
                ->badge()
                ->color(fn (int $state) => $state > 0 ? 'success' : 'danger'),

            ])

            ->filters([
                //
            ])

            ->actions([
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),  
            ])  

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Hapus Terpilih'), 
            ])
            
            ->defaultSort('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) \App\Models\Phone::count();
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPhones::route('/'),
        ];
    }
}
