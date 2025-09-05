<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentsMonthlyChart extends ChartWidget
{
    protected static ?string $heading = 'Pembayaran / Bulan (6 bln terakhir)';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->copy()->startOfMonth()->subMonths(5);

        $rows = Payment::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") AS ym, SUM(amount) AS total')
            ->where('created_at', '>=', $start)
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $data   = [];
        for ($i = 0; $i < 6; $i++) {
            $m = $start->copy()->addMonths($i);
            $key = $m->format('Y-m');
            $labels[] = $m->translatedFormat('M Y');
            $data[]   = (float) ($rows[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pembayaran',
                    'data'  => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
