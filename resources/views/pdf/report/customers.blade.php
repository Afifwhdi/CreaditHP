@extends('pdf.report.layouts.base')

@section('content')
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Nama</th>
        <th>HP/WA</th>
        <th>Status</th>
        <th>Dibuat</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $i => $c)
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td>{{ $c->name }}</td>
            <td>{{ $c->phone }}</td>
            <td class="center">{{ $c->status ?? '-' }}</td>
            <td>{{ optional($c->created_at)->toDateString() }}</td>
        </tr>
    @empty
        <tr><td colspan="5" class="center">Tidak ada data</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
