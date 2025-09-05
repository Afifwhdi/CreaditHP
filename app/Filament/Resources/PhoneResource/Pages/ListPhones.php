<?php

namespace App\Filament\Resources\PhoneResource\Pages;

use App\Filament\Resources\PhoneResource;
use App\Models\Phone;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPhones extends ListRecords
{
    protected static string $resource = PhoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            Tab::make('Tersedia', fn (Builder $query) => $query->where('stock', '>', 0))
                ->badgeColor('success')
                ->badge(Phone::where('stock', '>', 0)->count()),

            Tab::make('Habis', fn (Builder $query) => $query->where('stock', '<=', 0))
                ->badgeColor('danger')
                ->badge(Phone::where('stock', '<=', 0)->count()),
        ];
    }

}
