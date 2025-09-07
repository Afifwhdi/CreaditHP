<?php

namespace App\Filament\Resources;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'System Settings';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 99;

    public static function canCreate(): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canEdit($record): bool { return true; }

    public static function getEloquentQuery() : Builder
    {
        return parent::getEloquentQuery()->where('id', 1);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas Toko')
                ->schema([
                    Forms\Components\TextInput::make('store_name')->label('Nama Toko')->required(),
                    Forms\Components\FileUpload::make('logo_path')
                        ->image()->directory('settings/logo')->label('Logo'),
                    Forms\Components\Select::make('currency')
                        ->label('Mata Uang')->options([
                            'IDR' => 'IDR (Rupiah)',
                            'USD' => 'USD',
                        ])->native(false),
                    Forms\Components\Select::make('timezone')
                        ->label('Zona Waktu')->options([
                            'Asia/Jakarta' => 'Asia/Jakarta',
                            'Asia/Makassar' => 'Asia/Makassar',
                            'Asia/Jayapura' => 'Asia/Jayapura',
                        ])->native(false),
                ])->columns(2),

            Forms\Components\Section::make('Kredit & Reminder')
                ->schema([
                    Forms\Components\CheckboxList::make('default_tenors')
                        ->label('Tenor Default (bulan)')
                        ->options([
                            6 => '6', 8 => '8', 10 => '10', 12 => '12'
                        ])->columns(4),
                    Forms\Components\TextInput::make('reminder_days_before')
                        ->numeric()->minValue(0)->label('Reminder H-minus')
                        ->helperText('0 = hari H, 1 = H-1, dst'),
                    Forms\Components\TextInput::make('whatsapp_sender')
                        ->label('WA Sender / Channel (Fontte)')->placeholder('62xxxxxxxxxx / channel id'),
                    Forms\Components\Textarea::make('whatsapp_template')
                        ->label('Template Pesan WA')
                        ->rows(5)
                        ->placeholder("Halo {{name}}, cicilan ke-{{term}} jatuh tempo pada {{due_date}}.\nTotal: {{amount}}. Silakan bayar melalui ..."),
                ])->columns(2),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('store_name')
                ->label('Nama Toko'),
            Tables\Columns\TextColumn::make('currency')
                ->label('Mata uang'),
            Tables\Columns\TextColumn::make('timezone')
                ->label('Zona Waktu'),
        ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => SettingResource\Pages\ListSettings::route('/'),
        ];
    }
}
