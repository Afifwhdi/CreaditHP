<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\ExtraStats;
use App\Filament\Widgets\PaymentsMonthlyChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Beranda';
    protected static ?string $title           = 'Dashboard Kredit HP';

    public function getWidgets(): array
    {
        return [
            DashboardStats::class,       
            ExtraStats::class,            
            PaymentsMonthlyChart::class,  
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'sm' => 1,
            'lg' => 3, 
        ];
    }
}
