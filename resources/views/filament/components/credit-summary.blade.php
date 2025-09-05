@php($c = $calc ?? null)
@if($c)
    <div class="grid md:grid-cols-3 gap-4">
        <x-filament::section heading="Ringkasan">
            <dl class="text-sm space-y-1">
                <div><dt class="font-medium">Pokok (Price - DP)</dt><dd>Rp{{ number_format($c['principal'],0,',','.') }}</dd></div>
                <div><dt class="font-medium">Bunga/bln (flat)</dt><dd>Rp{{ number_format($c['monthly_interest'],0,',','.') }}</dd></div>
                <div><dt class="font-medium">Cicilan/bln</dt><dd class="text-lg font-semibold">Rp{{ number_format($c['installment'],0,',','.') }}</dd></div>
                <div><dt class="font-medium">Total Bunga</dt><dd>Rp{{ number_format($c['total_interest'],0,',','.') }}</dd></div>
                <div><dt class="font-medium">Total Dibayar</dt><dd>Rp{{ number_format($c['total_payable'],0,',','.') }}</dd></div>
                <div><dt class="font-medium">Estimasi Profit</dt><dd class="text-green-600 font-semibold">Rp{{ number_format($c['expected_profit'],0,',','.') }}</dd></div>
            </dl>
        </x-filament::section>
    </div>
@endif
