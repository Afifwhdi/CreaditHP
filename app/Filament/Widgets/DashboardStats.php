<?php

namespace App\Filament\Widgets;

use App\Enums\InstallmentStatus;
use App\Models\Credit;
use App\Models\Installment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class DashboardStats extends BaseWidget {
    protected function getStats(): array {
        $today = Carbon::today(); $h3 = $today->copy()->addDays(3);
        return [
            Stat::make('Kredit Aktif', Credit::where('status','active')
                ->count())
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->description('Jumlah kredit yang sedang berjalan'),
                
            Stat::make('Jatuh Tempo Hari Ini', Installment::whereDate('due_date',$today)
                ->where('status',InstallmentStatus::PENDING)->count())
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning')
                ->description('Jumlah angsuran yang jatuh tempo hari ini'),

            Stat::make('H-3 Jatuh Tempo', Installment::whereDate('due_date',$h3)
                ->where('status',InstallmentStatus::PENDING)->count())
                ->descriptionIcon('heroicon-o-bell')
                ->color('info')
                ->description('Jumlah angsuran yang jatuh tempo 3 hari lagi'),

            Stat::make('Angsuran Pending', Installment::where('status', InstallmentStatus::PENDING)->count())
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger')
                ->description('Total angsuran yang belum dibayar'),

        ];
    }
}
