<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Bitquery Explorer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-body-font-family: 'IBM Plex Sans', system-ui, sans-serif;
            --bs-body-bg: #f3f5f8;
            --bs-body-color: #1b2430;
            --ink: #12263f;
            --accent: #0f6a8f;
        }
        .topbar { background: var(--ink); }
        .topbar .navbar-brand { font-weight: 600; letter-spacing: .01em; }
        .topbar .nav-link { color: #b9c7d8; }
        .topbar .nav-link.active, .topbar .nav-link:hover { color: #fff; }
        .topbar .nav-link.active { box-shadow: inset 0 -2px 0 #5cc2e6; }
        .btn-accent { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-accent:hover { background: #0b5473; border-color: #0b5473; color: #fff; }
        .panel { background: #fff; border: 1px solid #dde3ea; border-radius: .5rem; }
        table { font-variant-numeric: tabular-nums; }
        thead th { font-weight: 500; color: #5b6878; white-space: nowrap; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .85em; }
        .in { color: #17803d; font-weight: 500; }
        .out { color: #b4331f; font-weight: 500; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand topbar mb-4">
    <div class="container">
        <a class="navbar-brand text-white" href="{{ route('trades') }}">Bitquery Explorer</a>
        <div class="navbar-nav">
            <a @class(['nav-link', 'active' => request()->routeIs('trades')]) href="{{ route('trades') }}">Trade terbaru</a>
            <a @class(['nav-link', 'active' => request()->routeIs('tokens')]) href="{{ route('tokens') }}">Token teratas</a>
            <a @class(['nav-link', 'active' => request()->routeIs('wallet')]) href="{{ route('wallet') }}">Cek wallet</a>
        </div>
    </div>
</nav>

<main class="container pb-5">
    <h1 class="h4 mb-1">@yield('heading')</h1>
    <p class="text-secondary mb-3">@yield('lead')</p>

    @yield('filters')

    @if (!empty($error))
        <div class="alert alert-danger" role="alert">{{ $error }}</div>
    @endif

    @yield('content')

    <p class="text-secondary small mt-4 mb-0">Data dari Bitquery. Hasil disimpan di cache {{ config('bitquery.cache_ttl') }} detik.</p>
</main>
</body>
</html>
