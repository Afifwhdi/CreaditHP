<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\Phone;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;

class CreditsRelationManager extends RelationManager {
    protected static string $relationship = 'credits';
    protected static ?string $title = 'Kredit';

    public function form(Forms\Form $form): Forms\Form {
        return $form->schema([
            Forms\Components\Select::make('phone_id')->label('HP')
                ->options(Phone::query()->orderBy('brand')->get()->pluck('display_name','id'))
                ->searchable(),
            Forms\Components\DatePicker::make('contract_date')->required()->default(now()),
            Forms\Components\TextInput::make('price')->numeric()->prefix('Rp')->required(),
            Forms\Components\TextInput::make('down_payment')->numeric()->prefix('Rp')->default(0),
            Forms\Components\TextInput::make('tenor')->numeric()->minValue(1)->maxValue(24)->required(),
            Forms\Components\TextInput::make('due_day')->numeric()->minValue(1)->maxValue(28)->required(),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Tables\Table $table): Tables\Table {
        return $table->columns([
            Tables\Columns\TextColumn::make('phone.display_name')->label('HP'),
            Tables\Columns\TextColumn::make('contract_date')->date(),
            Tables\Columns\TextColumn::make('price')->money('IDR'),
            Tables\Columns\TextColumn::make('down_payment')->money('IDR'),
            Tables\Columns\TextColumn::make('tenor'),
            Tables\Columns\TextColumn::make('first_due_date')->date(),
            Tables\Columns\TextColumn::make('status')->badge(),
        ])->headerActions([Tables\Actions\CreateAction::make()->label('Tambah Kredit')])
        ->actions([Tables\Actions\EditAction::make(), Tables\Actions\ViewAction::make()]);
    }
}
