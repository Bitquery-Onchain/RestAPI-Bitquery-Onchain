<?php

namespace App\Http\Controllers;

use App\Services\BitqueryService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Throwable;

class BitqueryController extends Controller
{
    public function __construct(private BitqueryService $bitquery)
    {
    }

    /** Halaman 1: DEX trade terbaru. */
    public function trades(Request $request)
    {
        $network = $this->network($request);
        [$rows, $error] = $this->safely(fn () => $this->bitquery->latestTrades($network));

        return view('trades', compact('rows', 'error', 'network'));
    }

    /** Halaman 2: token dengan volume terbesar. */
    public function tokens(Request $request)
    {
        $network = $this->network($request);
        $hours = in_array((int) $request->query('hours'), [1, 6, 24], true) ? (int) $request->query('hours') : 24;
        [$rows, $error] = $this->safely(fn () => $this->bitquery->topTokens($network, $hours));

        return view('tokens', compact('rows', 'error', 'network', 'hours'));
    }

    /** Halaman 3: transfer masuk dan keluar sebuah wallet. */
    public function wallet(Request $request)
    {
        $network = $this->network($request);
        $address = trim((string) $request->query('address', ''));
        $rows = [];
        $error = null;

        if ($address !== '') {
            if (! preg_match('/^0x[a-fA-F0-9]{40}$/', $address)) {
                $error = 'Alamat tidak valid. Gunakan format 0x diikuti 40 karakter heksadesimal.';
            } else {
                [$rows, $error] = $this->safely(fn () => $this->bitquery->walletTransfers($network, $address));
            }
        }

        return view('wallet', compact('rows', 'error', 'network', 'address'));
    }

    private function network(Request $request): string
    {
        $network = (string) $request->query('network', 'eth');

        return array_key_exists($network, config('bitquery.networks')) ? $network : 'eth';
    }

    /** Jalankan pemanggilan API; kalau gagal, kembalikan pesan error untuk ditampilkan di halaman. */
    private function safely(Closure $callback): array
    {
        try {
            return [$callback(), null];
        } catch (Throwable $e) {
            report($e);

            return [[], $e->getMessage()];
        }
    }
}
