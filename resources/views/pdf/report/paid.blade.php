@extends('pdf.report.layouts.base')

@section('content')
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Customer</th>
        <th>HP/WA</th>
        <th>Dibayar Pada</th>
        <th class="right">Nominal</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $i => $item)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td>{{ $item->customer->name ?? '-' }}</td>
            <td>{{ $item->customer->phone ?? '-' }}</td>
            <td>{{ \Illuminate\Support\Carbon::parse($item->paid_at)->toDateTimeString() }}</td>
            <td class="right">{{ number_format($item->amount ?? 0, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="center">Tidak ada data</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
