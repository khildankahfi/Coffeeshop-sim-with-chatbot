@extends('layouts.app')
@section('title', 'Daftar Order')
@push('styles')
<style>
.page-header { background: var(--brown-main); padding: 40px; }
.page-header h1 { color: var(--cream); font-size: 32px; margin-bottom: 6px; }
.page-header p { color: #a08060; font-size: 14px; }

.orders-page { padding: 40px; }

.orders-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 32px; }
.o-stat { background: #fff; border: 0.5px solid #e8d8c4; border-radius: 14px; padding: 20px; text-align: center; }
.o-stat .num { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--brown-main); }
.o-stat .lbl { font-size: 12px; color: var(--text-muted); margin-top: 3px; }

.filter-bar { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; align-items: center; }
.filter-label { font-size: 13px; color: var(--text-muted); font-weight: 500; }
.filter-btn { padding: 6px 16px; border-radius: 20px; font-size: 12px; cursor: pointer; border: 1px solid #d4b896; color: var(--brown-mid); background: transparent; font-family: 'DM Sans', sans-serif; transition: all .2s; }
.filter-btn.active, .filter-btn:hover { background: var(--brown-main); color: var(--cream); border-color: var(--brown-main); }

.orders-table-wrap { background: #fff; border: 0.5px solid #e8d8c4; border-radius: 14px; overflow: hidden; }
.orders-table { width: 100%; border-collapse: collapse; }
.orders-table th { background: var(--cream-mid); padding: 13px 16px; text-align: left; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; border-bottom: 0.5px solid #e8d8c4; }
.orders-table td { padding: 14px 16px; font-size: 13px; color: var(--text-dark); border-bottom: 0.5px solid #f0e8d8; vertical-align: middle; }
.orders-table tr:last-child td { border-bottom: none; }
.orders-table tr:hover td { background: var(--cream-light); }

.order-code { font-weight: 600; color: var(--brown-main); font-size: 12px; }
.order-customer { font-weight: 500; }
.order-items { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
.order-price { font-weight: 600; color: var(--accent); }

/* Status badges */
.badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
.badge-pending    { background: #fef3c7; color: #92400e; }
.badge-processing { background: #dbeafe; color: #1e40af; }
.badge-done       { background: #d1fae5; color: #065f46; }
.badge-cancelled  { background: #fee2e2; color: #991b1b; }

/* Status update select */
.status-select { border: 1px solid #e8d8c4; border-radius: 8px; padding: 5px 10px; font-size: 12px; font-family: 'DM Sans', sans-serif; color: var(--text-dark); background: var(--cream-light); cursor: pointer; outline: none; }
.status-select:focus { border-color: var(--brown-main); }

.empty-state { text-align: center; padding: 60px 20px; }
.empty-state .emoji { font-size: 48px; margin-bottom: 16px; }
.empty-state p { color: var(--text-muted); font-size: 14px; }

.pagination-wrap { margin-top: 24px; display: flex; justify-content: center; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>Daftar Order</h1>
    <p>Kelola dan pantau semua pesanan masuk</p>
</div>

<div class="orders-page">
    <!-- Stats -->
    <div class="orders-stats">
        <div class="o-stat">
            <div class="num">{{ $stats['total'] }}</div>
            <div class="lbl">Total Order</div>
        </div>
        <div class="o-stat">
            <div class="num">{{ $stats['pending'] }}</div>
            <div class="lbl">Pending</div>
        </div>
        <div class="o-stat">
            <div class="num">{{ $stats['done'] }}</div>
            <div class="lbl">Selesai</div>
        </div>
        <div class="o-stat">
            <div class="num">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div>
            <div class="lbl">Revenue Hari Ini</div>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-bar">
        <span class="filter-label">Status:</span>
        <a href="{{ route('orders.index') }}">
            <button class="filter-btn {{ !request('status') ? 'active' : '' }}">Semua</button>
        </a>
        @foreach(['pending','processing','done','cancelled'] as $s)
        <a href="{{ route('orders.index', ['status' => $s]) }}">
            <button class="filter-btn {{ request('status') === $s ? 'active' : '' }}">
                {{ ucfirst($s) }}
            </button>
        </a>
        @endforeach
    </div>

    <!-- Table -->
    @if($orders->count() > 0)
    <div class="orders-table-wrap">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Kode Order</th>
                    <th>Pelanggan</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><span class="order-code">{{ $order->order_code }}</span></td>
                    <td>
                        <div class="order-customer">{{ $order->customer_name ?? 'Guest' }}</div>
                        @if($order->notes)
                        <div class="order-items">📝 {{ $order->notes }}</div>
                        @endif
                    </td>
                    <td>
                        @foreach($order->items as $item)
                        <div class="order-items">{{ $item->qty }}x {{ $item->product->name }}</div>
                        @endforeach
                    </td>
                    <td class="order-price">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td style="font-size:12px; color:var(--text-muted);">
                        {{ $order->created_at->format('d M, H:i') }}
                    </td>
                    <td>
                        @php
                        $badgeClass = [
                            'pending'    => 'badge-pending',
                            'processing' => 'badge-processing',
                            'done'       => 'badge-done',
                            'cancelled'  => 'badge-cancelled',
                        ][$order->status] ?? 'badge-pending';
                        $icons = ['pending'=>'⏳','processing'=>'🔄','done'=>'✅','cancelled'=>'❌'];
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ $icons[$order->status] ?? '' }} {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('orders.update', $order) }}" method="POST">
                            @csrf @method('PUT')
                            <select name="status" class="status-select"
                                onchange="this.form.submit()">
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
    <div class="pagination-wrap">
        {{ $orders->links() }}
    </div>

    @else
    <div class="empty-state">
        <div class="emoji">📋</div>
        <p>Belum ada order masuk.</p>
    </div>
    @endif
</div>
@endsection