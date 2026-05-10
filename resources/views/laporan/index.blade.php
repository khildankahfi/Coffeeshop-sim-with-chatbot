@extends('layouts.admin')
@section('title', 'Laporan & Statistik')
@push('styles')
<style>
.stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 28px; }
.stat-card {
    background: #fff; border: 0.5px solid #e8d8c4;
    border-radius: 14px; padding: 20px; position: relative; overflow: hidden;
}
.stat-card::before {
    content: ''; position: absolute; top: 0; left: 0;
    width: 4px; height: 100%; background: var(--brown-main);
}
.stat-icon { font-size: 24px; margin-bottom: 10px; }
.stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.stat-value { font-family: 'Playfair Display', serif; font-size: 24px; color: var(--brown-main); font-weight: 600; }
.stat-sub { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
.stat-up { color: var(--green); font-weight: 600; }
.stat-down { color: #e53e3e; font-weight: 600; }

.charts-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px; }
.chart-card {
    background: #fff; border: 0.5px solid #e8d8c4;
    border-radius: 14px; padding: 20px;
}
.chart-title { font-size: 14px; font-weight: 600; color: var(--brown-main); margin-bottom: 4px; }
.chart-sub { font-size: 12px; color: var(--text-muted); margin-bottom: 16px; }

.bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

/* Period filter */
.period-tabs { display: flex; gap: 6px; margin-bottom: 24px; }
.period-btn {
    padding: 6px 16px; border-radius: 16px; font-size: 12px;
    cursor: pointer; border: 1px solid #d4b896; color: var(--brown-mid);
    background: transparent; font-family: 'DM Sans', sans-serif; transition: all .2s;
}
.period-btn.active, .period-btn:hover {
    background: var(--brown-main); color: var(--cream); border-color: var(--brown-main);
}

/* Top menu table */
.top-menu-list { display: flex; flex-direction: column; gap: 10px; }
.top-menu-item { display: flex; align-items: center; gap: 12px; }
.top-rank { width: 24px; height: 24px; border-radius: 50%; background: var(--cream-mid); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--brown-main); flex-shrink: 0; }
.top-rank.gold { background: #fef3c7; color: #92400e; }
.top-rank.silver { background: #f1f5f9; color: #475569; }
.top-rank.bronze { background: #fef9ee; color: #92400e; }
.top-name { flex: 1; font-size: 13px; font-weight: 500; color: var(--text-dark); }
.top-qty { font-size: 12px; color: var(--text-muted); }
.top-bar-wrap { width: 80px; height: 6px; background: var(--cream-mid); border-radius: 3px; overflow: hidden; }
.top-bar { height: 100%; background: var(--brown-main); border-radius: 3px; transition: width .5s ease; }

/* Feedback summary */
.rating-overview { display: flex; align-items: center; gap: 20px; margin-bottom: 16px; }
.rating-big { font-family: 'Playfair Display', serif; font-size: 48px; color: var(--brown-main); line-height: 1; }
.rating-stars { color: #f59e0b; font-size: 18px; margin-bottom: 4px; }
.rating-count { font-size: 12px; color: var(--text-muted); }
.rating-bars { flex: 1; display: flex; flex-direction: column; gap: 4px; }
.rating-bar-row { display: flex; align-items: center; gap: 8px; font-size: 11px; }
.rating-bar-row .lbl { width: 20px; color: var(--text-muted); text-align: right; }
.rbar-wrap { flex: 1; height: 6px; background: var(--cream-mid); border-radius: 3px; overflow: hidden; }
.rbar-fill { height: 100%; background: #f59e0b; border-radius: 3px; }
.rating-bar-row .cnt { width: 20px; color: var(--text-muted); }

/* Status donut legend */
.status-legend { display: flex; flex-direction: column; gap: 10px; margin-top: 16px; }
.legend-item { display: flex; align-items: center; gap: 10px; font-size: 13px; }
.legend-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.legend-label { flex: 1; color: var(--text-dark); }
.legend-val { font-weight: 600; color: var(--brown-main); }

/* Export btn */
.export-btn {
    background: var(--brown-main); color: var(--cream);
    border: none; padding: 8px 18px; border-radius: 16px;
    font-size: 12px; cursor: pointer; font-family: 'DM Sans', sans-serif;
    font-weight: 500; display: flex; align-items: center; gap: 6px;
    transition: background .2s;
}
.export-btn:hover { background: var(--accent); }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
@endpush

@section('content')
<!-- Header + Export -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div>
        <h2 style="font-size:22px; color:var(--brown-main); margin-bottom:4px;">Laporan & Statistik</h2>
        <p style="font-size:13px; color:var(--text-muted);">Data real-time per {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>
    <button class="export-btn" onclick="window.print()">🖨️ Print Laporan</button>
</div>

<!-- Period Filter -->
<div class="period-tabs">
    <button class="period-btn {{ $period === 'today' ? 'active' : '' }}" onclick="window.location='?period=today'">Hari Ini</button>
    <button class="period-btn {{ $period === 'week' ? 'active' : '' }}" onclick="window.location='?period=week'">7 Hari</button>
    <button class="period-btn {{ $period === 'month' ? 'active' : '' }}" onclick="window.location='?period=month'">30 Hari</button>
    <button class="period-btn {{ $period === 'all' ? 'active' : '' }}" onclick="window.location='?period=all'">Semua</button>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
        <div class="stat-sub">Periode yang dipilih</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📋</div>
        <div class="stat-label">Total Order</div>
        <div class="stat-value">{{ $stats['total_orders'] }}</div>
        <div class="stat-sub">{{ $stats['done_orders'] }} selesai · {{ $stats['pending_orders'] }} pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">☕</div>
        <div class="stat-label">Item Terjual</div>
        <div class="stat-value">{{ $stats['total_items'] }}</div>
        <div class="stat-sub">Total item dari semua order</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-label">Rating Rata-rata</div>
        <div class="stat-value">{{ number_format($stats['avg_rating'], 1) }}</div>
        <div class="stat-sub">dari {{ $stats['total_feedback'] }} ulasan</div>
    </div>
</div>

<!-- Charts -->
<div class="charts-grid">
    <!-- Revenue Chart -->
    <div class="chart-card">
        <div class="chart-title">📈 Grafik Revenue</div>
        <div class="chart-sub">Revenue harian dalam periode ini</div>
        <canvas id="revenueChart" height="100"></canvas>
    </div>

    <!-- Status Donut -->
    <div class="chart-card">
        <div class="chart-title">📊 Status Order</div>
        <div class="chart-sub">Distribusi status semua order</div>
        <canvas id="statusChart" height="160"></canvas>
        <div class="status-legend">
            <div class="legend-item">
                <div class="legend-dot" style="background:#fbbf24"></div>
                <div class="legend-label">Pending</div>
                <div class="legend-val">{{ $statusData['pending'] }}</div>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#60a5fa"></div>
                <div class="legend-label">Processing</div>
                <div class="legend-val">{{ $statusData['processing'] }}</div>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#34d399"></div>
                <div class="legend-label">Done</div>
                <div class="legend-val">{{ $statusData['done'] }}</div>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#f87171"></div>
                <div class="legend-label">Cancelled</div>
                <div class="legend-val">{{ $statusData['cancelled'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Grid -->
<div class="bottom-grid">
    <!-- Top Menu -->
    <div class="chart-card">
        <div class="chart-title">🏆 Menu Terlaris</div>
        <div class="chart-sub">Top 5 produk paling banyak dipesan</div>
        <div class="top-menu-list">
            @php $maxQty = $topMenus->first()->total_qty ?? 1; @endphp
            @foreach($topMenus as $i => $menu)
            @php
            $rankClass = match($i) { 0 => 'gold', 1 => 'silver', 2 => 'bronze', default => '' };
            $pct = ($menu->total_qty / $maxQty) * 100;
            @endphp
            <div class="top-menu-item">
                <div class="top-rank {{ $rankClass }}">{{ $i + 1 }}</div>
                <div class="top-name">{{ $menu->name }}</div>
                <div class="top-bar-wrap">
                    <div class="top-bar" style="width:{{ $pct }}%"></div>
                </div>
                <div class="top-qty">{{ $menu->total_qty }}x</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Rating & Feedback -->
    <div class="chart-card">
        <div class="chart-title">⭐ Rating Pelanggan</div>
        <div class="chart-sub">Ringkasan kepuasan pelanggan</div>
        <div class="rating-overview">
            <div>
                <div class="rating-big">{{ number_format($stats['avg_rating'], 1) }}</div>
                <div class="rating-stars">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= round($stats['avg_rating']) ? '★' : '☆' }}
                    @endfor
                </div>
                <div class="rating-count">{{ $stats['total_feedback'] }} ulasan</div>
            </div>
            <div class="rating-bars">
                @foreach([5,4,3,2,1] as $star)
                @php $cnt = $ratingDist[$star] ?? 0; $pct = $stats['total_feedback'] > 0 ? ($cnt / $stats['total_feedback']) * 100 : 0; @endphp
                <div class="rating-bar-row">
                    <span class="lbl">{{ $star }}</span>
                    <div class="rbar-wrap"><div class="rbar-fill" style="width:{{ $pct }}%"></div></div>
                    <span class="cnt">{{ $cnt }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent feedback -->
        <div style="margin-top:12px;">
            <div style="font-size:12px; font-weight:600; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:.5px;">Ulasan Terbaru</div>
            @forelse($recentFeedback as $fb)
            <div style="padding:10px 0; border-bottom:0.5px solid #f0e8d8;">
                <div style="display:flex; justify-content:space-between; margin-bottom:3px;">
                    <span style="font-size:13px; font-weight:500; color:var(--brown-main);">{{ $fb->customer_name }}</span>
                    <span style="color:#f59e0b; font-size:13px;">{{ str_repeat('★', $fb->rating) }}{{ str_repeat('☆', 5 - $fb->rating) }}</span>
                </div>
                @if($fb->comment)
                <div style="font-size:12px; color:var(--text-muted);">{{ $fb->comment }}</div>
                @endif
                <div style="font-size:11px; color:#c0a080; margin-top:2px;">{{ $fb->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:13px;">Belum ada ulasan</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartLabels) !!},
        datasets: [{
            label: 'Revenue (Rp)',
            data: {!! json_encode($chartData) !!},
            borderColor: '#3d1f0a',
            backgroundColor: 'rgba(61,31,10,.08)',
            borderWidth: 2,
            pointBackgroundColor: '#c8a97e',
            pointRadius: 4,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v),
                    font: { size: 10 }
                },
                grid: { color: '#f0e8d8' }
            },
            x: { ticks: { font: { size: 10 } }, grid: { display: false } }
        }
    }
});

// Status Donut Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Processing', 'Done', 'Cancelled'],
        datasets: [{
            data: [
                {{ $statusData['pending'] }},
                {{ $statusData['processing'] }},
                {{ $statusData['done'] }},
                {{ $statusData['cancelled'] }},
            ],
            backgroundColor: ['#fbbf24','#60a5fa','#34d399','#f87171'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush