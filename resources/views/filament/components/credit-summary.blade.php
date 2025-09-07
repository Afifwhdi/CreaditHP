<div class="space-y-2">
    <div class="grid grid-cols-2 gap-2">
        <div>Pokok (Principal)</div>
        <div class="text-right font-semibold">Rp {{ number_format($calc['principal'] ?? 0, 0, ',', '.') }}</div>

        <div>Cicilan / Bulan</div>
        <div class="text-right">Rp {{ number_format($calc['installment'] ?? 0, 0, ',', '.') }}</div>

        <div>Total Cicilan ({{ request()->get('tenor') ?? '' }} bln)</div>
        <div class="text-right">Rp {{ number_format($calc['total_installments'] ?? 0, 0, ',', '.') }}</div>

        <div>Total Bayar (DP + semua cicilan)</div>
        <div class="text-right">Rp {{ number_format($calc['total_payable'] ?? 0, 0, ',', '.') }}</div>

        <div class="font-semibold text-green-600">Estimasi Profit</div>
        <div class="text-right font-semibold text-green-600">
            Rp {{ number_format($calc['expected_profit'] ?? 0, 0, ',', '.') }}
        </div>
    </div>
</div>
