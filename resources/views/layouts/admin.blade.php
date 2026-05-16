<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BrewNest Admin — @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,600;1,600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --espresso: #0F0500;
            --brown:    #1C0A02;
            --brown2:   #2C1208;
            --gold:     #C8973A;
            --gold-lt:  #E8C87A;
            --cream:    #FAF0DC;
            --warm:     #F5EDD8;
            --muted:    #8A7060;
            --accent:   #D4854A;
            --green:    #2A7A5A;
            --sidebar:  220px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Outfit', sans-serif; background: var(--warm); color: #1A0800; display: flex; min-height: 100vh; overflow: hidden; }
        a { text-decoration: none; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar); background: var(--espresso);
            display: flex; flex-direction: column; height: 100vh;
            position: fixed; top: 0; left: 0;
            border-right: 1px solid rgba(200,151,58,.07);
            z-index: 50;
        }
        .sb-brand {
            padding: 22px 18px; border-bottom: 1px solid rgba(200,151,58,.07);
        }
        .sb-brand-name {
            font-family: 'Cormorant Garamond', serif; font-size: 20px;
            color: var(--cream); display: flex; align-items: center; gap: 8px;
        }
        .sb-brand-name span { color: var(--gold); }
        .sb-brand-sub { font-size: 9px; color: rgba(200,151,58,.35); letter-spacing: 1px; text-transform: uppercase; margin-top: 3px; }

        .sb-nav { padding: 14px 10px; flex: 1; overflow-y: auto; }
        .sb-nav::-webkit-scrollbar { width: 2px; }
        .sb-nav::-webkit-scrollbar-thumb { background: rgba(200,151,58,.1); }

        .sb-label { font-size: 9px; color: rgba(138,112,96,.35); letter-spacing: 1.2px; text-transform: uppercase; padding: 0 10px; margin: 16px 0 6px; }
        .sb-label:first-child { margin-top: 0; }

        .sb-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(250,240,220,.4); font-size: 13px; font-weight: 400;
            transition: all .2s; margin-bottom: 2px;
        }
        .sb-link:hover { background: rgba(200,151,58,.08); color: rgba(250,240,220,.75); }
        .sb-link.active { background: rgba(200,151,58,.1); color: var(--gold); }
        .sb-icon { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }

        .sb-footer { padding: 14px 10px; border-top: 1px solid rgba(200,151,58,.07); }
        .sb-user { display: flex; align-items: center; gap: 10px; padding: 10px 12px; margin-bottom: 8px; }
        .sb-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--accent));
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: var(--espresso); flex-shrink: 0;
        }
        .sb-uname { color: var(--cream); font-size: 13px; font-weight: 500; }
        .sb-urole { color: rgba(200,151,58,.35); font-size: 9px; letter-spacing: .5px; text-transform: uppercase; }
        .sb-logout {
            width: 100%; background: rgba(255,255,255,.03);
            border: 1px solid rgba(200,151,58,.08); color: rgba(250,240,220,.35);
            padding: 9px; border-radius: 10px; font-size: 12px; cursor: pointer;
            font-family: 'Outfit', sans-serif; transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .sb-logout:hover { background: rgba(248,113,113,.08); color: #f87171; border-color: rgba(248,113,113,.15); }

        /* ===== MAIN AREA ===== */
        .main-wrap {
            margin-left: var(--sidebar); flex: 1;
            display: flex; flex-direction: column; height: 100vh; overflow: hidden;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: #fff; border-bottom: 1px solid rgba(200,151,58,.1);
            padding: 0 28px; height: 58px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .tb-title { font-size: 15px; font-weight: 600; color: #1A0800; }
        .tb-right { display: flex; align-items: center; gap: 10px; }
        .tb-date { font-size: 12px; color: var(--muted); background: var(--warm); padding: 5px 12px; border-radius: 10px; }
        .tb-site {
            background: var(--espresso); color: var(--cream);
            padding: 7px 16px; border-radius: 12px; font-size: 12px;
            cursor: pointer; border: none; font-family: 'Outfit', sans-serif;
            font-weight: 500; transition: all .2s; letter-spacing: .3px;
        }
        .tb-site:hover { background: var(--gold); color: var(--espresso); }

        /* ===== PAGE CONTENT ===== */
        .page-content { flex: 1; overflow-y: auto; padding: 24px 28px; }
        .page-content::-webkit-scrollbar { width: 4px; }
        .page-content::-webkit-scrollbar-thumb { background: rgba(200,151,58,.2); border-radius: 2px; }

        /* ===== ALERT ===== */
        .alert-success {
            background: rgba(42,122,90,.1); color: #2A7A5A;
            border: 1px solid rgba(42,122,90,.2); padding: 12px 16px;
            border-radius: 12px; font-size: 13px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }

        /* ===== CARD ===== */
        .admin-card {
            background: #fff; border: 0.5px solid rgba(200,151,58,.12);
            border-radius: 16px; overflow: hidden;
        }

        /* ===== TABLE ===== */
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th {
            background: var(--warm); padding: 12px 16px; text-align: left;
            font-size: 11px; font-weight: 600; color: var(--muted);
            text-transform: uppercase; letter-spacing: .6px;
            border-bottom: 0.5px solid rgba(200,151,58,.1);
        }
        .admin-table td {
            padding: 13px 16px; font-size: 13px;
            border-bottom: 0.5px solid rgba(200,151,58,.06); vertical-align: middle;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background: var(--warm); }

        /* ===== BADGES ===== */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; }
        .badge-pending    { background: rgba(251,191,36,.12); color: #d97706; }
        .badge-processing { background: rgba(96,165,250,.12); color: #2563eb; }
        .badge-done       { background: rgba(42,122,90,.12);  color: #2A7A5A; }
        .badge-cancelled  { background: rgba(248,113,113,.12); color: #dc2626; }

        /* ===== FORM ===== */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px; }
        .form-label span { color: #e53e3e; }
        .form-control {
            width: 100%; border: 1px solid rgba(200,151,58,.15); border-radius: 10px;
            padding: 10px 14px; font-size: 13px; outline: none;
            font-family: 'Outfit', sans-serif; background: var(--warm); color: #1A0800;
            transition: border .2s;
        }
        .form-control:focus { border-color: var(--gold); background: #fff; }
        select.form-control { cursor: pointer; }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-check { display: flex; align-items: center; gap: 10px; margin-top: 8px; }
        .form-check input { width: 16px; height: 16px; cursor: pointer; accent-color: var(--gold); }
        .form-check label { font-size: 13px; cursor: pointer; }
        .error-msg { color: #dc2626; font-size: 12px; margin-top: 4px; }
        .alert-error { background: rgba(220,38,38,.08); color: #dc2626; border: 1px solid rgba(220,38,38,.15); padding: 12px 16px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; }

        /* ===== BUTTONS ===== */
        .btn-gold {
            background: linear-gradient(135deg, var(--gold), var(--accent));
            color: var(--espresso); border: none; padding: 10px 24px;
            border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;
            font-family: 'Outfit', sans-serif; transition: all .2s; letter-spacing: .3px;
        }
        .btn-gold:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(200,151,58,.3); }
        .btn-dark {
            background: var(--espresso); color: var(--cream);
            border: none; padding: 10px 22px; border-radius: 20px;
            font-size: 13px; font-weight: 500; cursor: pointer;
            font-family: 'Outfit', sans-serif; transition: all .2s;
        }
        .btn-dark:hover { background: var(--brown2); }
        .btn-ghost-sm {
            background: transparent; border: 1px solid rgba(200,151,58,.2);
            color: var(--muted); padding: 6px 14px; border-radius: 10px;
            font-size: 12px; cursor: pointer; font-family: 'Outfit', sans-serif;
            transition: all .2s;
        }
        .btn-ghost-sm:hover { background: var(--warm); color: #1A0800; }
        .btn-danger-sm {
            background: rgba(220,38,38,.08); border: 1px solid rgba(220,38,38,.15);
            color: #dc2626; padding: 6px 14px; border-radius: 10px;
            font-size: 12px; cursor: pointer; font-family: 'Outfit', sans-serif;
            transition: all .2s;
        }
        .btn-danger-sm:hover { background: rgba(220,38,38,.15); }

        /* ===== STATUS SELECT ===== */
        .status-select {
            border: 1px solid rgba(200,151,58,.15); border-radius: 8px;
            padding: 5px 10px; font-size: 12px; font-family: 'Outfit', sans-serif;
            color: #1A0800; background: var(--warm); cursor: pointer; outline: none;
        }
        .status-select:focus { border-color: var(--gold); }

        /* ===== PAGINATION ===== */
        .pagi-wrap { display: flex; justify-content: center; margin-top: 20px; }

        /* ===== STAT CARDS ===== */
        .stats-row { display: grid; gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: #fff; border: 0.5px solid rgba(200,151,58,.12);
            border-radius: 16px; padding: 20px; position: relative; overflow: hidden;
        }
        .stat-card::before { content: ''; position: absolute; left: 0; top: 0; width: 3px; height: 100%; background: linear-gradient(180deg, var(--gold), var(--accent)); }
        .stat-icon { font-size: 22px; margin-bottom: 10px; }
        .stat-label { font-size: 11px; color: var(--muted); font-weight: 500; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .stat-value { font-family: 'Cormorant Garamond', serif; font-size: 26px; color: #1A0800; font-weight: 600; }
        .stat-sub { font-size: 11px; color: var(--muted); margin-top: 3px; }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-brand-name">☕ <span>Brew</span>Nest</div>
        <div class="sb-brand-sub">Admin Panel</div>
    </div>
    <nav class="sb-nav">
        <div class="sb-label">Utama</div>
        <a href="{{ route('admin.kasir.index') }}"
           class="sb-link {{ request()->routeIs('admin.kasir.*') ? 'active' : '' }}">
            <span class="sb-icon">🖥️</span> Sistem Kasir
        </a>
        <a href="{{ route('admin.orders.index') }}"
        class="sb-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
        style="justify-content:flex-start">
            <span class="sb-icon">📋</span>
            <span>Daftar Order</span>
            @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
            <span style="background:linear-gradient(135deg,var(--gold),var(--accent));color:var(--espresso);font-size:10px;font-weight:700;padding:2px 8px;border-radius:10px;margin-left:auto;">
                {{ $pendingCount }}
            </span>
            @endif
        </a>
        <a href="{{ route('admin.laporan.index') }}"
           class="sb-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
            <span class="sb-icon">📊</span> Laporan
        </a>
        <a href="{{ route('admin.menu.adminIndex') }}"
           class="sb-link {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
            <span class="sb-icon">🍽️</span> Kelola Menu
        </a>
        <div class="sb-label">Lainnya</div>
        <a href="{{ route('home') }}" class="sb-link" target="_blank">
            <span class="sb-icon">🌐</span> Lihat Website
        </a>
    </nav>
    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="sb-uname">{{ auth()->user()->name }}</div>
                <div class="sb-urole">Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-logout">🚪 Logout</button>
        </form>
    </div>
</aside>

<!-- MAIN -->
<div class="main-wrap">
    <div class="topbar">
        <div class="tb-title">@yield('title', 'Dashboard')</div>
        <div class="tb-right">
            <span class="tb-date">{{ now()->isoFormat('dddd, D MMM YYYY') }}</span>
            <a href="{{ route('home') }}" target="_blank">
                <button class="tb-site">← Lihat Website</button>
            </a>
        </div>
    </div>
    <div class="page-content">
        @if(session('success'))
        <div class="alert-success">✅ {{ session('success') }}</div>
        @endif
        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>