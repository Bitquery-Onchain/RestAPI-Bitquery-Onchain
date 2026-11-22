@extends('layouts.app')

@section('title', 'Trade terbaru')
@section('heading', 'Trade DEX terbaru')
@section('lead', 'Transaksi swap terakhir di bursa terdesentralisasi pada jaringan pilihanmu.')

@section('filters')
    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">@include('partials.network')</div>
        <div class="col-auto"><button class="btn btn-accent">Tampilkan</button></div>
    </form>
@endsection

@section('content')
    <div class="panel table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Waktu (UTC)</th>
                    <th>DEX</th>
                    <th>Dibeli</th>
                    <th>Dijual</th>
                    <th class="text-end">Nilai (USD)</th>
                    <th>Tx</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td class="text-nowrap">{{ $r['time'] }}</td>
                        <td>{{ $r['dex'] }}</td>
                        <td>{{ $r['buy'] }}</td>
                        <td>{{ $r['sell'] }}</td>
                        <td class="text-end">{{ $r['usd'] }}</td>
                        <td class="mono" title="{{ $r['tx'] }}">{{ \Illuminate\Support\Str::limit($r['tx'], 14, '...') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data. Pilih jaringan lalu klik Tampilkan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
