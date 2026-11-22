@extends('layouts.app')

@section('title', 'Token teratas')
@section('heading', 'Token dengan volume terbesar')
@section('lead', 'Diurutkan menurut total nilai trading dalam USD pada rentang waktu yang dipilih.')

@section('filters')
    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">@include('partials.network')</div>
        <div class="col-auto">
            <select name="hours" class="form-select" aria-label="Rentang waktu">
                @foreach ([1 => '1 jam terakhir', 6 => '6 jam terakhir', 24 => '24 jam terakhir'] as $h => $label)
                    <option value="{{ $h }}" @selected($h === $hours)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-accent">Tampilkan</button></div>
    </form>
@endsection

@section('content')
    <div class="panel table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Token</th>
                    <th>Kontrak</th>
                    <th class="text-end">Volume (USD)</th>
                    <th class="text-end">Jumlah trade</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $r['symbol'] }}</strong> <span class="text-secondary">{{ $r['name'] }}</span></td>
                        <td class="mono" title="{{ $r['contract'] }}">{{ \Illuminate\Support\Str::limit($r['contract'], 14, '...') }}</td>
                        <td class="text-end">{{ $r['volume'] }}</td>
                        <td class="text-end">{{ $r['trades'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada data. Pilih jaringan dan rentang waktu, lalu klik Tampilkan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
