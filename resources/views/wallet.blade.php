@extends('layouts.app')

@section('title', 'Cek wallet')
@section('heading', 'Cek transfer wallet')
@section('lead', 'Masukkan alamat wallet untuk melihat transfer token masuk dan keluar terbaru.')

@section('filters')
    <form method="GET" class="row g-2 mb-3">
        <div class="col-12 col-md">
            <input type="text" name="address" value="{{ $address }}" class="form-control mono"
                   placeholder="0x..." aria-label="Alamat wallet" autocomplete="off" required>
        </div>
        <div class="col-auto">@include('partials.network')</div>
        <div class="col-auto"><button class="btn btn-accent">Cari transfer</button></div>
    </form>
@endsection

@section('content')
    <div class="panel table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Waktu (UTC)</th>
                    <th>Arah</th>
                    <th>Jumlah</th>
                    <th>Lawan transaksi</th>
                    <th>Tx</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td class="text-nowrap">{{ $r['time'] }}</td>
                        <td class="{{ $r['outgoing'] ? 'out' : 'in' }}">{{ $r['direction'] }}</td>
                        <td>{{ $r['amount'] }}</td>
                        <td class="mono" title="{{ $r['counterparty'] }}">{{ \Illuminate\Support\Str::limit($r['counterparty'], 14, '...') }}</td>
                        <td class="mono" title="{{ $r['tx'] }}">{{ \Illuminate\Support\Str::limit($r['tx'], 14, '...') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">
                        {{ $address === '' ? 'Masukkan alamat wallet di atas untuk mulai.' : 'Tidak ada transfer ditemukan untuk alamat ini.' }}
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
