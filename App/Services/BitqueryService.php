<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class BitqueryService
{
    /** DEX trade terbaru di satu jaringan EVM. */
    public function latestTrades(string $network, int $limit = 25): array
    {
        $network = $this->network($network);
        $limit = max(1, min($limit, 50));

        $data = $this->query(<<<GQL
        {
          EVM(network: {$network}) {
            DEXTrades(limit: {count: {$limit}}, orderBy: {descending: Block_Time}) {
              Block { Time }
              Transaction { Hash }
              Trade {
                Dex { ProtocolName }
                Buy { Amount AmountInUSD Currency { Symbol } }
                Sell { Amount Currency { Symbol } }
              }
            }
          }
        }
        GQL);

        return collect(data_get($data, 'EVM.DEXTrades', []))->map(fn ($t) => [
            'time' => $this->time(data_get($t, 'Block.Time')),
            'dex'  => data_get($t, 'Trade.Dex.ProtocolName') ?: '-',
            'buy'  => $this->amount(data_get($t, 'Trade.Buy.Amount'), data_get($t, 'Trade.Buy.Currency.Symbol')),
            'sell' => $this->amount(data_get($t, 'Trade.Sell.Amount'), data_get($t, 'Trade.Sell.Currency.Symbol')),
            'usd'  => $this->fmt(data_get($t, 'Trade.Buy.AmountInUSD'), 2),
            'tx'   => (string) data_get($t, 'Transaction.Hash'),
        ])->all();
    }

    /** Token dengan volume trading (USD) terbesar dalam N jam terakhir. */
    public function topTokens(string $network, int $hours = 24, int $limit = 10): array
    {
        $network = $this->network($network);
        $limit = max(1, min($limit, 25));
        $since = now()->utc()->subHours($hours)->startOfMinute()->format('Y-m-d\TH:i:s\Z');

        $data = $this->query(<<<GQL
        {
          EVM(network: {$network}) {
            DEXTradeByTokens(
              orderBy: {descendingByField: "volume_usd"}
              limit: {count: {$limit}}
              where: {Block: {Time: {since: "{$since}"}}}
            ) {
              Trade { Currency { Symbol Name SmartContract } }
              volume_usd: sum(of: Trade_Side_AmountInUSD)
              trades: count
            }
          }
        }
        GQL);

        return collect(data_get($data, 'EVM.DEXTradeByTokens', []))->map(fn ($r) => [
            'symbol'   => data_get($r, 'Trade.Currency.Symbol') ?: '?',
            'name'     => data_get($r, 'Trade.Currency.Name') ?: '',
            'contract' => (string) data_get($r, 'Trade.Currency.SmartContract'),
            'volume'   => $this->fmt(data_get($r, 'volume_usd'), 0),
            'trades'   => number_format((int) data_get($r, 'trades')),
        ])->all();
    }

    /** Transfer token masuk dan keluar untuk satu alamat wallet. */
    public function walletTransfers(string $network, string $address, int $limit = 25): array
    {
        $network = $this->network($network);
        $limit = max(1, min($limit, 50));
        $address = strtolower($address); // sudah divalidasi di controller (0x + 40 hex)

        $data = $this->query(<<<GQL
        {
          EVM(network: {$network}) {
            Transfers(
              limit: {count: {$limit}}
              orderBy: {descending: Block_Time}
              where: {any: [
                {Transfer: {Sender: {is: "{$address}"}}},
                {Transfer: {Receiver: {is: "{$address}"}}}
              ]}
            ) {
              Block { Time }
              Transaction { Hash }
              Transfer { Amount Sender Receiver Currency { Symbol } }
            }
          }
        }
        GQL);

        return collect(data_get($data, 'EVM.Transfers', []))->map(function ($t) use ($address) {
            $sender = strtolower((string) data_get($t, 'Transfer.Sender'));
            $outgoing = $sender === $address;

            return [
                'time'         => $this->time(data_get($t, 'Block.Time')),
                'direction'    => $outgoing ? 'Keluar' : 'Masuk',
                'outgoing'     => $outgoing,
                'amount'       => $this->amount(data_get($t, 'Transfer.Amount'), data_get($t, 'Transfer.Currency.Symbol')),
                'counterparty' => (string) ($outgoing ? data_get($t, 'Transfer.Receiver') : data_get($t, 'Transfer.Sender')),
                'tx'           => (string) data_get($t, 'Transaction.Hash'),
            ];
        })->all();
    }

    /** Kirim query GraphQL ke Bitquery. Hasil di-cache sebentar. */
    private function query(string $graphql): array
    {
        $token = config('bitquery.token');

        if (blank($token)) {
            throw new RuntimeException('BITQUERY_TOKEN belum diisi di file .env.');
        }

        return Cache::remember(
            'bitquery:'.md5($graphql),
            now()->addSeconds((int) config('bitquery.cache_ttl')),
            function () use ($graphql, $token) {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->timeout(30)
                    ->post(config('bitquery.endpoint'), ['query' => $graphql]);

                if ($response->failed()) {
                    throw new RuntimeException(
                        'Bitquery membalas HTTP '.$response->status().': '.Str::limit($response->body(), 300)
                    );
                }

                $json = $response->json();

                if (! empty($json['errors'])) {
                    throw new RuntimeException(collect($json['errors'])->pluck('message')->implode('; '));
                }

                return $json['data'] ?? [];
            }
        );
    }

    private function network(string $network): string
    {
        return array_key_exists($network, config('bitquery.networks')) ? $network : 'eth';
    }

    private function time(?string $iso): string
    {
        return $iso ? Carbon::parse($iso)->utc()->format('d M Y H:i:s') : '-';
    }

    private function amount(mixed $value, ?string $symbol): string
    {
        return trim($this->fmt($value, 6).' '.($symbol ?? ''));
    }

    private function fmt(mixed $value, int $decimals): string
    {
        $text = number_format((float) $value, $decimals, '.', ',');

        return str_contains($text, '.') ? rtrim(rtrim($text, '0'), '.') : $text;
    }
}
