<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Phone;
use App\Models\Credit;
use App\Models\Installment;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExtraStats extends BaseWidget
{   
    protected function getStats(): array
    {
        $totalCustomers = Customer::count();
        $totalStock     = (int) Phone::sum('stock');
        $nilaiKredit    = (float) Credit::where('status', 'active')->sum('price');
        $angsPending    = (int) Installment::where('status', 'pending')->count();
        $pembayaranBln  = (float) Payment::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount');

        return [
            Stat::make('Total Pelanggan', $totalCustomers)
                ->description('Semua pelanggan terdaftar')
                ->icon('heroicon-o-user-group')
                ->color('primary'),

            // Stat::make('Stok HP Tersedia', $totalStock)
            //     ->description('Unit di gudang')
            //     ->icon('heroicon-o-device-phone-mobile')
            //     ->color('info'),

            Stat::make('Nilai Kredit Berjalan', 'Rp' . number_format($nilaiKredit, 0, ',', '.'))
                ->description('Total kontrak aktif')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Angsuran Pending', $angsPending)
                ->description('Belum dibayar')
                ->icon('heroicon-o-clock')
                ->color('danger'),

            Stat::make(
                'Pembayaran Bulan Ini',
                'Rp' . number_format($pembayaranBln, 0, ',', '.')
            )
                ->description('Uang masuk bulan berjalan')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),
        ];
    }
}
