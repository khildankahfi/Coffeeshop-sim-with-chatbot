<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BrewNest Admin — @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --brown-dark:  #2d1508;
            --brown-main:  #3d1f0a;
            --brown-light: #c8a97e;
            --cream:       #f5e6c8;
            --cream-light: #faf5ee;
            --cream-mid:   #f0e8d8;
            --text-dark:   #1a0c04;
            --text-muted:  #8a6040;
            --accent:      #c8813e;
            --green:       #1D9E75;
            --sidebar-w:   220px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream-light); color: var(--text-dark); display: flex; min-height: 100vh; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }
        a { text-decoration: none; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-w); background: var(--brown-main);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; height: 100vh;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 24px 20px; border-bottom: 1px solid #5a3020;
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand .brand-name {
            color: var(--cream); font-family: 'Playfair Display', serif;
            font-size: 18px; font-weight: 600;
        }
        .sidebar-brand .brand-sub {
            color: var(--brown-light); font-size: 11px; margin-top: 2px;
        }

        .sidebar-menu { padding: 16px 12px; flex: 1; }
        .menu-label {
            color: #5a3020; font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 1px;
            padding: 0 8px; margin: 16px 0 8px;
        }
        .menu-label:first-child { margin-top: 0; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: var(--brown-light); font-size: 13px; font-weight: 500;
            transition: all .2s; margin-bottom: 2px;
        }
        .sidebar-link:hover { background: #ffffff15; color: var(--cream); }
        .sidebar-link.active { background: #ffffff20; color: var(--cream); }
        .sidebar-link .icon { font-size: 16px; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 16px 12px; border-top: 1px solid #5a3020;
        }
        .user-info {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            margin-bottom: 8px;
        }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--brown-light); display: flex;
            align-items: center; justify-content: center;
            font-size: 14px; font-weight: 600; color: var(--brown-main);
            flex-shrink: 0;
        }
        .user-name { color: var(--cream); font-size: 13px; font-weight: 500; }
        .user-role { color: var(--brown-light); font-size: 11px; }
        .logout-form { width: 100%; }
        .logout-btn {
            width: 100%; background: #ffffff10; border: 1px solid #5a3020;
            color: var(--brown-light); padding: 9px; border-radius: 10px;
            font-size: 13px; cursor: pointer; font-family: 'DM Sans', sans-serif;
            font-weight: 500; transition: all .2s; display: flex;
            align-items: center; justify-content: center; gap: 6px;
        }
        .logout-btn:hover { background: #ff444420; color: #ff8888; border-color: #ff444440; }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-w); flex: 1;
            display: flex; flex-direction: column; min-height: 100vh;
        }

        /* ===== TOP BAR ===== */
        .topbar {
            background: #fff; border-bottom: 0.5px solid #e8d8c4;
            padding: 0 32px; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 16px; font-weight: 600; color: var(--brown-main); }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-badge {
            background: var(--cream-mid); color: var(--text-muted);
            font-size: 12px; padding: 4px 12px; border-radius: 12px;
        }
        .view-site-btn {
            background: var(--brown-main); color: var(--cream);
            padding: 7px 16px; border-radius: 16px; font-size: 12px;
            font-weight: 500; transition: background .2s;
        }
        .view-site-btn:hover { background: var(--accent); }

        /* ===== PAGE CONTENT ===== */
        .page-content { padding: 32px; flex: 1; }

        /* ===== ALERT ===== */
        .alert-success {
            background: #d1fae5; color: #065f46; padding: 12px 16px;
            border-radius: 10px; font-size: 13px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div>
            <div class="brand-name">☕ BrewNest</div>
            <div class="brand-sub">Admin Panel</div>
        </div>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-label">Menu Utama</div>
        <a href="{{ route('admin.orders.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <span class="icon">📋</span> Daftar Order
        </a>
        <a href="{{ route('admin.menu.adminIndex') }}"
        class="sidebar-link {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
            <span class="icon">🍽️</span> Kelola Menu
        </a>

        <div class="menu-label">Lainnya</div>
        <a href="{{ route('home') }}" class="sidebar-link" target="_blank">
            <span class="icon">🌐</span> Lihat Website
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
        <form class="logout-form" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">🚪 Logout</button>
        </form>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">
    <!-- TOP BAR -->
    <div class="topbar">
        <div class="topbar-title">@yield('title', 'Dashboard')</div>
        <div class="topbar-right">
            <span class="topbar-badge">{{ now()->format('d M Y') }}</span>
            <a href="{{ route('home') }}" class="view-site-btn" target="_blank">← Lihat Website</a>
        </div>
    </div>

    <!-- PAGE CONTENT -->
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