@extends('layouts.admin')
@section('title', '📋 Daftar Order')
@push('styles')
<style>
/* ===== STATS ===== */
.orders-stats {
    display: grid; grid-template-columns: repeat(4,1fr);
    gap: 16px; margin-bottom: 24px;
}

/* ===== FILTER ===== */
.filter-wrap {
    display: flex; gap: 8px; margin-bottom: 20px;
    align-items: center; flex-wrap: wrap;
}
.filter-label { font-size: 12px; color: var(--muted); font-weight: 500; letter-spacing: .4px; }
.filter-btn {
    padding: 7px 16px; border-radius: 16px; font-size: 12px; cursor: pointer;
    border: 1px solid rgba(200,151,58,.15); color: var(--muted);
    background: transparent; font-family: 'Outfit', sans-serif; transition: all .2s;
}
.filter-btn.active, .filter-btn:hover {
    background: var(--espresso); color: var(--cream); border-color: var(--espresso);
}

/* ===== TABLE ===== */
.table-wrap {
    background: #fff; border: 0.5px solid rgba(200,151,58,.12);
    border-radius: 16px; overflow: hidden;
}
.admin-table th { font-size: 11px; }
.order-code { font-weight: 700; color: var(--espresso); font-size: 12px; font-family: 'Outfit', sans-serif; }
.order-customer { font-weight: 600; font-size: 13px; color: #1A0800; }
.order-note { font-size: 11px; color: var(--muted); margin-top: 2px; }
.order-items-list { font-size: 12px; color: var(--muted); }
.order-price { font-weight: 700; color: var(--accent); font-size: 14px; }
.order-time { font-size: 11px; color: var(--muted); }

/* ===== PAGINATION ===== */
.pagi-wrap { display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 24px; flex-wrap: wrap; }
.pagi-wrap a, .pagi-wrap span {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 10px;
    border-radius: 10px; font-size: 13px; font-weight: 500;
    font-family: 'Outfit', sans-serif; transition: all .2s; border: 1px solid transparent;
}
.pagi-wrap a {
    background: #fff; color: #1A0800;
    border-color: rgba(200,151,58,.15); cursor: pointer;
}
.pagi-wrap a:hover { background: var(--espresso); color: var(--cream); border-color: var(--espresso); }
.pagi-wrap span[aria-current="page"] {
    background: var(--espresso); color: var(--cream);
    border-color: var(--espresso); font-weight: 600;
}
.pagi-wrap span.disabled {
    background: var(--warm); color: rgba(138,112,96,.4);
    border-color: rgba(200,151,58,.08); cursor: not-allowed;
}
.pagi-info { font-size: 12px; color: var(--muted); text-align: center; margin-top: 10px; }

/* ===== AUTO REFRESH INDICATOR ===== */
.refresh-bar {
    display: flex; align-items: center; gap: 10px;
    background: rgba(42,122,90,.08); border: 1px solid rgba(42,122,90,.15);
    border-radius: 10px; padding: 8px 14px; margin-bottom: 16px;
    font-size: 12px; color: #2A7A5A;
}
.refresh-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #2A7A5A; animation: pulse-green 2s infinite; flex-shrink: 0;
}
@keyframes pulse-green { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.8)} }
.refresh-progress {
    flex: 1; height: 3px; background: rgba(42,122,90,.15);
    border-radius: 2px; overflow: hidden;
}
.refresh-bar-fill {
    height: 100%; background: #2A7A5A; border-radius: 2px;
    animation: refill 30s linear infinite;
}
@keyframes refill { from{width:100%} to{width:0%} }
.refresh-countdown { font-weight: 600; min-width: 30px; text-align: right; }

/* ===== NEW ORDER BADGE ===== */
.new-order-toast {
    position: fixed; top: 70px; right: 24px; z-index: 9999;
    background: var(--espresso); border: 1px solid rgba(42,122,90,.3);
    color: var(--cream); padding: 14px 18px; border-radius: 14px;
    box-shadow: 0 12px 32px rgba(0,0,0,.25); font-size: 13px; min-width: 260px;
    transform: translateY(-20px); opacity: 0;
    transition: all .3s cubic-bezier(.34,1.56,.64,1); pointer-events: none;
}
.new-order-toast.show { transform: translateY(0); opacity: 1; }
.not-title { font-weight: 600; color: #34d399; margin-bottom: 3px; font-size: 14px; }
.not-sub { color: rgba(250,240,220,.45); font-size: 12px; }

/* ===== EMPTY STATE ===== */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { font-size: 48px; margin-bottom: 14px; opacity: .4; display: block; }
.empty-text { color: var(--muted); font-size: 14px; }

/* ===== SOURCE BADGE ===== */
.source-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; padding: 2px 8px; border-radius: 8px; font-weight: 500;
}
.source-ai { background: rgba(200,151,58,.1); color: var(--gold); }
.source-kasir { background: rgba(42,122,90,.1); color: #2A7A5A; }
</style>
@endpush

@section('content')

<!-- Auto refresh indicator -->
<div class="refresh-bar">
    <div class="refresh-dot"></div>
    <span>Auto refresh aktif</span>
    <div class="refresh-progress"><div class="refresh-bar-fill" id="refreshFill"></div></div>
    <span class="refresh-countdown" id="countdown">30s</span>
    <button onclick="manualRefresh()" style="background:rgba(42,122,90,.15);border:none;color:#2A7A5A;padding:4px 10px;border-radius:8px;font-size:11px;cursor:pointer;font-family:'Outfit',sans-serif;font-weight:500;">🔄 Refresh</button>
</div>

<!-- Stats -->
<div class="orders-stats">
    <div class="stat-card">
        <div class="stat-icon">📋</div>
        <div class="stat-label">Total Order</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-sub">Semua waktu</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-label">Pending</div>
        <div class="stat-value" style="color:#d97706">{{ $stats['pending'] }}</div>
        <div class="stat-sub">Menunggu diproses</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-label">Selesai</div>
        <div class="stat-value" style="color:var(--green)">{{ $stats['done'] }}</div>
        <div class="stat-sub">Order selesai</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Revenue Hari Ini</div>
        <div class="stat-value">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
        <div class="stat-sub">Order non-cancelled</div>
    </div>
</div>

<!-- Filter -->
<div class="filter-wrap">
    <span class="filter-label">Filter:</span>
    <a href="{{ route('admin.orders.index') }}">
        <button class="filter-btn {{ !request('status') ? 'active' : '' }}">Semua</button>
    </a>
    @foreach(['pending'=>'⏳ Pending','processing'=>'🔄 Processing','done'=>'✅ Done','cancelled'=>'❌ Cancelled'] as $s => $label)
    <a href="{{ route('admin.orders.index', ['status' => $s]) }}">
        <button class="filter-btn {{ request('status') === $s ? 'active' : '' }}">{{ $label }}</button>
    </a>
    @endforeach
</div>

<!-- Table -->
@if($orders->count() > 0)
<div class="table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Kode Order</th>
                <th>Pelanggan</th>
                <th>Items</th>
                <th>Total</th>
                <th>Sumber</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="ordersTableBody">
            @foreach($orders as $order)
            <tr>
                <td><span class="order-code">{{ $order->order_code }}</span></td>
                <td>
                    <div class="order-customer">{{ $order->customer_name ?? 'Guest' }}</div>
                    @if($order->notes)
                    <div class="order-note">📝 {{ Str::limit($order->notes, 30) }}</div>
                    @endif
                </td>
                <td>
                    <div class="order-items-list">
                        @foreach($order->items->take(3) as $item)
                        <div>{{ $item->qty }}x {{ $item->product->name ?? '-' }}</div>
                        @endforeach
                        @if($order->items->count() > 3)
                        <div style="color:var(--gold);font-size:11px">+{{ $order->items->count() - 3 }} lainnya</div>
                        @endif
                    </div>
                </td>
                <td><span class="order-price">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></td>
                <td>
                    @if($order->created_by)
                    <span class="source-badge source-kasir">🖥️ Kasir</span>
                    @else
                    <span class="source-badge source-ai">🤖 Karen AI</span>
                    @endif
                </td>
                <td><div class="order-time">{{ $order->created_at->format('d M') }}</div><div class="order-time">{{ $order->created_at->format('H:i') }}</div></td>
                <td>
                    @php
                    $badge = ['pending'=>'badge-pending','processing'=>'badge-processing','done'=>'badge-done','cancelled'=>'badge-cancelled'][$order->status] ?? 'badge-pending';
                    $icons = ['pending'=>'⏳','processing'=>'🔄','done'=>'✅','cancelled'=>'❌'];
                    @endphp
                    <span class="badge {{ $badge }}">{{ $icons[$order->status] }} {{ ucfirst($order->status) }}</span>
                </td>
                <td>
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf @method('PUT')
                        <select name="status" class="status-select" onchange="this.form.submit()">
                            @foreach(['pending','processing','done','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagi-wrap">
    {{-- Previous --}}
    @if($orders->onFirstPage())
        <span class="disabled">← Prev</span>
    @else
        <a href="{{ $orders->previousPageUrl() }}">← Prev</a>
    @endif

    {{-- Page numbers --}}
    @foreach($orders->getUrlRange(max(1, $orders->currentPage()-2), min($orders->lastPage(), $orders->currentPage()+2)) as $page => $url)
        @if($page == $orders->currentPage())
            <span aria-current="page">{{ $page }}</span>
        @else
            <a href="{{ $url }}">{{ $page }}</a>
        @endif
    @endforeach

    {{-- Next --}}
    @if($orders->hasMorePages())
        <a href="{{ $orders->nextPageUrl() }}">Next →</a>
    @else
        <span class="disabled">Next →</span>
    @endif
</div>
<div class="pagi-info">
    Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }} dari {{ $orders->total() }} order
</div>

@else
<div class="table-wrap">
    <div class="empty-state">
        <span class="empty-icon">📋</span>
        <div class="empty-text">Belum ada order masuk.</div>
    </div>
</div>
@endif

<!-- New order toast -->
<div class="new-order-toast" id="newOrderToast">
    <div class="not-title">🔔 Order baru masuk!</div>
    <div class="not-sub" id="newOrderSub">Ada pesanan baru dari pelanggan</div>
</div>
@endsection

@push('scripts')
<script>
let countdown = 30;
let lastOrderCount = {{ $stats['total'] }};
let countdownInterval;

function startCountdown() {
    countdown = 30;
    clearInterval(countdownInterval);

    // Reset animasi progress bar
    const fill = document.getElementById('refreshFill');
    fill.style.animation = 'none';
    fill.offsetHeight; // trigger reflow
    fill.style.animation = 'refill 30s linear forwards';

    countdownInterval = setInterval(() => {
        countdown--;
        document.getElementById('countdown').textContent = countdown + 's';
        if (countdown <= 0) {
            clearInterval(countdownInterval);
            checkNewOrders();
        }
    }, 1000);
}

async function checkNewOrders() {
    try {
        const res  = await fetch('{{ route("admin.orders.check-new") }}');
        const data = await res.json();

        if (data.total > lastOrderCount) {
            const diff = data.total - lastOrderCount;
            document.getElementById('newOrderSub').textContent =
                `${diff} pesanan baru dari pelanggan`;
            const toast = document.getElementById('newOrderToast');
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 4000);

            // Update badge di sidebar
            updateSidebarBadge(data.pending);

            // Reload halaman
            setTimeout(() => window.location.reload(), 1500);
        } else {
            startCountdown();
        }

        lastOrderCount = data.total;
    } catch {
        startCountdown();
    }
}

function manualRefresh() {
    window.location.reload();
}

function updateSidebarBadge(pendingCount) {
    const link = document.querySelector('.sb-link[href*="orders"]');
    if (!link) return;
    let badge = link.querySelector('.sb-pending-badge');
    if (pendingCount > 0) {
        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'sb-pending-badge';
            badge.style.cssText = 'background:linear-gradient(135deg,#C8973A,#D4854A);color:#0F0500;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:auto;';
            link.style.justifyContent = 'flex-start';
            link.appendChild(badge);
        }
        badge.textContent = pendingCount;
    } else if (badge) {
        badge.remove();
    }
}

// Mulai countdown saat halaman load
startCountdown();

// Update badge pending saat load
updateSidebarBadge({{ $stats['pending'] }});
</script>
@endpush