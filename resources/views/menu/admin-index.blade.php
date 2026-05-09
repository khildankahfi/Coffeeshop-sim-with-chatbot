@extends('layouts.admin')
@section('title', 'Kelola Menu')
@push('styles')
<style>
.page-actions { display: flex; justify-content: flex-end; margin-bottom: 20px; }
.btn-add {
    background: var(--brown-main); color: var(--cream);
    padding: 10px 22px; border-radius: 20px; font-size: 13px;
    font-weight: 600; display: flex; align-items: center; gap: 6px;
    transition: background .2s;
}
.btn-add:hover { background: var(--accent); }

.menu-table-wrap { background: #fff; border: 0.5px solid #e8d8c4; border-radius: 14px; overflow: hidden; }
.menu-table { width: 100%; border-collapse: collapse; }
.menu-table th { background: var(--cream-mid); padding: 13px 16px; text-align: left; font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; border-bottom: 0.5px solid #e8d8c4; }
.menu-table td { padding: 14px 16px; font-size: 13px; border-bottom: 0.5px solid #f0e8d8; vertical-align: middle; }
.menu-table tr:last-child td { border-bottom: none; }
.menu-table tr:hover td { background: var(--cream-light); }
.menu-name { font-weight: 600; color: var(--brown-main); }
.menu-price { font-weight: 600; color: var(--accent); }
.badge-available { background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.badge-unavailable { background: #fee2e2; color: #991b1b; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.action-btns { display: flex; gap: 8px; }
.btn-edit { background: var(--cream-mid); color: var(--brown-main); border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-weight: 500; }
.btn-edit:hover { background: var(--brown-light); }
.btn-delete { background: #fee2e2; color: #991b1b; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-weight: 500; }
.btn-delete:hover { background: #fecaca; }
.empty-state { text-align: center; padding: 60px; color: var(--text-muted); }
</style>
@endpush

@section('content')
<div class="page-actions">
    <a href="{{ route('admin.menu.create') }}" class="btn-add">➕ Tambah Menu</a>
</div>

@if($products->count() > 0)
<div class="menu-table-wrap">
    <table class="menu-table">
        <thead>
            <tr>
                <th>Nama Menu</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td><div class="menu-name">{{ $product->name }}</div></td>
                <td>{{ $product->category->name }}</td>
                <td class="menu-price">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td>
                    @if($product->is_available)
                    <span class="badge-available">✅ Tersedia</span>
                    @else
                    <span class="badge-unavailable">❌ Tidak Tersedia</span>
                    @endif
                </td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('admin.menu.edit', $product) }}">
                            <button class="btn-edit">✏️ Edit</button>
                        </a>
                        <form action="{{ route('admin.menu.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Hapus menu ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-delete">🗑️ Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="empty-state">
    <div style="font-size:48px; margin-bottom:16px;">🍽️</div>
    <p>Belum ada menu. Tambahkan menu pertama kamu!</p>
</div>
@endif
@endsection