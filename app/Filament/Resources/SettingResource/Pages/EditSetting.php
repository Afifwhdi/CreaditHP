<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected static ?string $title = 'System Settings';

    public function mount($record = null): void
    {
        $record = Setting::singleton()->getKey();
        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return []; // tidak perlu Delete / View
    }
}
