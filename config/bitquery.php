<?php

return [
    // Endpoint GraphQL Bitquery V2
    'endpoint' => env('BITQUERY_ENDPOINT', 'https://streaming.bitquery.io/graphql'),

    // Access token Bitquery (formatnya ory_at_...), dikirim sebagai "Authorization: Bearer ..."
    'token' => env('BITQUERY_TOKEN'),

    // Lama hasil query disimpan di cache (detik) supaya kuota API tidak cepat habis
    'cache_ttl' => (int) env('BITQUERY_CACHE_TTL', 60),

    // Jaringan EVM yang bisa dipilih di UI (kunci harus sama dengan nama network di Bitquery)
    'networks' => [
        'eth'      => 'Ethereum',
        'bsc'      => 'BNB Chain',
        'matic'    => 'Polygon',
        'arbitrum' => 'Arbitrum',
        'base'     => 'Base',
        'optimism' => 'Optimism',
    ],
];
