<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Laporan' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        h1   { font-size: 18px; margin: 0 0 6px; }
        .meta { color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
        .right { text-align: right; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Laporan' }}</h1>
    <div class="meta">
        @if(!empty($start) || !empty($end))
            Periode: {{ $start ?? '—' }} s.d. {{ $end ?? '—' }}
        @endif
        @isset($status)
            &nbsp; | &nbsp; Status: {{ ucfirst($status) }}
        @endisset
    </div>

    @yield('content')
</body>
</html>
