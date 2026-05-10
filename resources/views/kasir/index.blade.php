@extends('layouts.admin')
@section('title', 'Sistem Kasir')
@push('styles')
<style>
.kasir-wrap {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
    align-items: start;
}

/* ===== PANEL KIRI — MENU ===== */
.menu-panel { display: flex; flex-direction: column; gap: 16px; }

.search-bar {
    display: flex; gap: 10px; align-items: center;
}
.search-input {
    flex: 1; border: 1px solid #e8d8c4; border-radius: 10px;
    padding: 10px 14px; font-size: 13px; outline: none;
    font-family: 'DM Sans', sans-serif; background: var(--cream-light);
}
.search-input:focus { border-color: var(--brown-main); }

.cat-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
.cat-tab {
    padding: 6px 16px; border-radius: 20px; font-size: 12px;
    cursor: pointer; border: 1px solid #d4b896; color: var(--brown-mid);
    background: transparent; font-family: 'DM Sans', sans-serif;
    transition: all .2s;
}
.cat-tab.active, .cat-tab:hover {
    background: var(--brown-main); color: var(--cream);
    border-color: var(--brown-main);
}

.menu-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
}
.menu-item {
    background: #fff; border: 1.5px solid #e8d8c4;
    border-radius: 12px; padding: 14px; cursor: pointer;
    transition: all .2s; text-align: center; position: relative;
}
.menu-item:hover { border-color: var(--brown-main); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(61,31,10,.1); }
.menu-item.selected { border-color: var(--brown-main); background: #faf0e0; }
.menu-item.unavailable { opacity: 0.5; cursor: not-allowed; }
.menu-emoji { font-size: 28px; margin-bottom: 8px; }
.menu-name { font-size: 13px; font-weight: 600; color: var(--brown-main); margin-bottom: 3px; }
.menu-cat-label { font-size: 11px; color: var(--text-muted); margin-bottom: 6px; }
.menu-price { font-size: 13px; font-weight: 600; color: var(--accent); }
.item-qty-ctrl {
    display: none; align-items: center; justify-content: center;
    gap: 8px; margin-top: 10px;
}
.menu-item.selected .item-qty-ctrl { display: flex; }
.qty-btn {
    width: 26px; height: 26px; border-radius: 50%;
    border: 1.5px solid var(--brown-main); background: transparent;
    color: var(--brown-main); font-size: 16px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-family: 'DM Sans', sans-serif; transition: all .15s;
}
.qty-btn:hover { background: var(--brown-main); color: var(--cream); }
.qty-num { font-size: 14px; font-weight: 600; color: var(--brown-main); min-width: 20px; text-align: center; }
.selected-badge {
    position: absolute; top: -6px; right: -6px;
    width: 20px; height: 20px; border-radius: 50%;
    background: var(--brown-main); color: var(--cream);
    font-size: 10px; font-weight: 700;
    display: none; align-items: center; justify-content: center;
}
.menu-item.selected .selected-badge { display: flex; }

/* ===== PANEL KANAN — ORDER ===== */
.order-panel {
    background: #fff; border: 0.5px solid #e8d8c4;
    border-radius: 14px; position: sticky; top: 80px;
    overflow: hidden;
}
.order-header {
    background: var(--brown-main); padding: 16px 20px;
    display: flex; align-items: center; justify-content: space-between;
}
.order-header h3 { color: var(--cream); font-size: 15px; font-family: 'Playfair Display', serif; }
.order-count {
    background: var(--brown-light); color: var(--brown-main);
    font-size: 12px; font-weight: 700; padding: 2px 10px;
    border-radius: 12px;
}
.order-body { padding: 16px 20px; }

.customer-input-wrap { margin-bottom: 16px; }
.input-label { font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; display: block; text-transform: uppercase; letter-spacing: .5px; }
.customer-input {
    width: 100%; border: 1px solid #e8d8c4; border-radius: 10px;
    padding: 9px 12px; font-size: 13px; outline: none;
    font-family: 'DM Sans', sans-serif; background: var(--cream-light);
}
.customer-input:focus { border-color: var(--brown-main); }

.order-items-list { min-height: 120px; margin-bottom: 16px; }
.empty-cart {
    text-align: center; padding: 30px 20px;
    color: var(--text-muted); font-size: 13px;
}
.empty-cart .empty-icon { font-size: 36px; margin-bottom: 8px; }

.order-item-row {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 0; border-bottom: 0.5px solid #f0e8d8;
    animation: slideIn .2s ease;
}
@keyframes slideIn { from { opacity:0; transform:translateX(10px); } to { opacity:1; transform:none; } }
.order-item-row:last-child { border-bottom: none; }
.oi-name { flex: 1; font-size: 13px; font-weight: 500; color: var(--brown-main); }
.oi-qty { font-size: 12px; color: var(--text-muted); }
.oi-price { font-size: 13px; font-weight: 600; color: var(--accent); }
.oi-remove { background: none; border: none; color: #ccc; cursor: pointer; font-size: 16px; padding: 0 4px; transition: color .15s; }
.oi-remove:hover { color: #e53e3e; }

.order-divider { border: none; border-top: 1px dashed #e8d8c4; margin: 12px 0; }

.order-summary { margin-bottom: 16px; }
.summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; font-size: 13px; }
.summary-row .label { color: var(--text-muted); }
.summary-row .value { font-weight: 500; color: var(--text-dark); }
.summary-row.total .label { font-size: 14px; font-weight: 600; color: var(--brown-main); }
.summary-row.total .value { font-size: 16px; font-weight: 700; color: var(--accent); }

.notes-wrap { margin-bottom: 16px; }
.notes-input {
    width: 100%; border: 1px solid #e8d8c4; border-radius: 10px;
    padding: 8px 12px; font-size: 12px; outline: none;
    font-family: 'DM Sans', sans-serif; background: var(--cream-light);
    resize: none; height: 60px;
}
.notes-input:focus { border-color: var(--brown-main); }

.btn-order {
    width: 100%; background: var(--brown-main); color: var(--cream);
    border: none; padding: 13px; border-radius: 12px;
    font-size: 14px; font-weight: 600; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: background .2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-order:hover { background: var(--accent); }
.btn-order:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-clear {
    width: 100%; background: transparent; color: var(--text-muted);
    border: 1px solid #e8d8c4; padding: 9px; border-radius: 12px;
    font-size: 13px; cursor: pointer; font-family: 'DM Sans', sans-serif;
    margin-top: 8px; transition: all .2s;
}
.btn-clear:hover { background: #fee2e2; color: #991b1b; border-color: #fecaca; }

/* ===== SUCCESS TOAST ===== */
.toast {
    position: fixed; top: 80px; right: 28px; z-index: 9999;
    background: var(--brown-main); color: var(--cream);
    padding: 14px 20px; border-radius: 12px;
    box-shadow: 0 8px 24px rgba(61,31,10,.25);
    font-size: 13px; min-width: 280px;
    transform: translateY(-20px); opacity: 0;
    transition: all .3s cubic-bezier(.34,1.56,.64,1);
    pointer-events: none;
}
.toast.show { transform: translateY(0); opacity: 1; }
.toast-title { font-weight: 600; margin-bottom: 4px; font-size: 14px; }
.toast-code { color: var(--brown-light); font-size: 12px; }
</style>
@endpush

@section('content')
<div class="kasir-wrap">
    <!-- PANEL KIRI: MENU -->
    <div class="menu-panel">
        <!-- Search -->
        <div class="search-bar">
            <input type="text" class="search-input" id="searchInput"
                   placeholder="🔍 Cari menu..." oninput="filterMenu()"/>
        </div>

        <!-- Kategori tabs -->
        <div class="cat-tabs" id="catTabs">
            <button class="cat-tab active" data-cat="all" onclick="filterCat('all', this)">Semua</button>
            @foreach($categories as $cat)
            <button class="cat-tab" data-cat="{{ $cat->slug }}" onclick="filterCat('{{ $cat->slug }}', this)">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>

        <!-- Grid menu -->
        <div class="menu-grid" id="menuGrid">
            @php
            $emojis = [
                'Americano'=>'☕','Cappuccino'=>'☕','Latte'=>'🥛',
                'V60 Ethiopia'=>'🍵','Aeropress'=>'🫗',
                'Matcha Latte'=>'🍃','Coklat Panas'=>'🍫',
                'Croissant'=>'🥐','Banana Bread'=>'🍌',
            ];
            @endphp
            @foreach($products as $product)
            <div class="menu-item {{ !$product->is_available ? 'unavailable' : '' }}"
                 id="item-{{ $product->id }}"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $product->price }}"
                 data-cat="{{ $product->category->slug }}"
                 onclick="{{ $product->is_available ? 'toggleItem(this)' : '' }}">
                <div class="selected-badge" id="badge-{{ $product->id }}">1</div>
                <div class="menu-emoji">{{ $emojis[$product->name] ?? '☕' }}</div>
                <div class="menu-name">{{ $product->name }}</div>
                <div class="menu-cat-label">{{ $product->category->name }}</div>
                <div class="menu-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                <div class="item-qty-ctrl" onclick="event.stopPropagation()">
                    <button class="qty-btn" onclick="changeQty({{ $product->id }}, -1)">−</button>
                    <span class="qty-num" id="qty-{{ $product->id }}">1</span>
                    <button class="qty-btn" onclick="changeQty({{ $product->id }}, 1)">+</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- PANEL KANAN: ORDER -->
    <div class="order-panel">
        <div class="order-header">
            <h3>🛒 Pesanan</h3>
            <span class="order-count" id="orderCount">0 item</span>
        </div>
        <div class="order-body">
            <!-- Nama pelanggan -->
            <div class="customer-input-wrap">
                <label class="input-label">Nama Pelanggan</label>
                <input type="text" class="customer-input" id="customerName"
                       placeholder="Masukkan nama pelanggan..."/>
            </div>

            <!-- List item order -->
            <label class="input-label">Item Pesanan</label>
            <div class="order-items-list" id="orderList">
                <div class="empty-cart">
                    <div class="empty-icon">🛒</div>
                    Pilih menu dari kiri
                </div>
            </div>

            <hr class="order-divider">

            <!-- Summary -->
            <div class="order-summary" id="orderSummary" style="display:none">
                <div class="summary-row">
                    <span class="label">Subtotal</span>
                    <span class="value" id="subtotalVal">Rp 0</span>
                </div>
                <div class="summary-row total">
                    <span class="label">Total</span>
                    <span class="value" id="totalVal">Rp 0</span>
                </div>
            </div>

            <!-- Catatan -->
            <div class="notes-wrap">
                <label class="input-label">Catatan (opsional)</label>
                <textarea class="notes-input" id="orderNotes"
                          placeholder="Contoh: tanpa gula, es sedikit..."></textarea>
            </div>

            <!-- Tombol -->
            <button class="btn-order" id="btnOrder" onclick="submitOrder()" disabled>
                ☕ Buat Pesanan
            </button>
            <button class="btn-clear" onclick="clearOrder()">🗑️ Kosongkan</button>
        </div>
    </div>
</div>

<!-- Toast notifikasi -->
<div class="toast" id="toast">
    <div class="toast-title">✅ Pesanan berhasil dibuat!</div>
    <div class="toast-code" id="toastCode"></div>
</div>
@endsection

@push('scripts')
<script>
// State
let cart    = {}; // { product_id: { name, price, qty } }
let allCat  = 'all';

// Toggle item masuk/keluar cart
function toggleItem(el) {
    const id    = parseInt(el.dataset.id);
    const name  = el.dataset.name;
    const price = parseFloat(el.dataset.price);

    if (cart[id]) {
        // Sudah ada → remove
        delete cart[id];
        el.classList.remove('selected');
        document.getElementById(`qty-${id}`).textContent = '1';
        document.getElementById(`badge-${id}`).textContent = '1';
    } else {
        // Belum ada → add dengan qty 1
        cart[id] = { name, price, qty: 1 };
        el.classList.add('selected');
    }
    renderCart();
}

// Ubah qty dari kontrol di kartu menu
function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty = Math.max(1, cart[id].qty + delta);
    document.getElementById(`qty-${id}`).textContent = cart[id].qty;
    document.getElementById(`badge-${id}`).textContent = cart[id].qty;
    renderCart();
}

// Render cart di panel kanan
function renderCart() {
    const list    = document.getElementById('orderList');
    const summary = document.getElementById('orderSummary');
    const count   = document.getElementById('orderCount');
    const btnOrder = document.getElementById('btnOrder');
    const keys    = Object.keys(cart);

    if (keys.length === 0) {
        list.innerHTML = '<div class="empty-cart"><div class="empty-icon">🛒</div>Pilih menu dari kiri</div>';
        summary.style.display = 'none';
        count.textContent = '0 item';
        btnOrder.disabled = true;
        return;
    }

    let total    = 0;
    let html     = '';
    let totalQty = 0;

    keys.forEach(id => {
        const item    = cart[id];
        const subtotal = item.price * item.qty;
        total         += subtotal;
        totalQty      += item.qty;

        html += `
        <div class="order-item-row">
            <div class="oi-name">${item.name}</div>
            <div class="oi-qty">${item.qty}x</div>
            <div class="oi-price">Rp ${formatNum(subtotal)}</div>
            <button class="oi-remove" onclick="removeItem(${id})" title="Hapus">×</button>
        </div>`;
    });

    list.innerHTML = html;
    summary.style.display = 'block';
    count.textContent = `${totalQty} item`;
    document.getElementById('subtotalVal').textContent = `Rp ${formatNum(total)}`;
    document.getElementById('totalVal').textContent    = `Rp ${formatNum(total)}`;
    btnOrder.disabled = false;
}

// Hapus item dari cart
function removeItem(id) {
    const el = document.getElementById(`item-${id}`);
    if (el) {
        el.classList.remove('selected');
        document.getElementById(`qty-${id}`).textContent = '1';
        document.getElementById(`badge-${id}`).textContent = '1';
    }
    delete cart[id];
    renderCart();
}

// Kosongkan semua
function clearOrder() {
    Object.keys(cart).forEach(id => {
        const el = document.getElementById(`item-${id}`);
        if (el) {
            el.classList.remove('selected');
            document.getElementById(`qty-${id}`).textContent = '1';
            document.getElementById(`badge-${id}`).textContent = '1';
        }
    });
    cart = {};
    document.getElementById('customerName').value = '';
    document.getElementById('orderNotes').value   = '';
    renderCart();
}

// Submit order ke server
async function submitOrder() {
    const customerName = document.getElementById('customerName').value.trim();
    const notes        = document.getElementById('orderNotes').value.trim();
    const btnOrder     = document.getElementById('btnOrder');

    if (!customerName) {
        document.getElementById('customerName').focus();
        document.getElementById('customerName').style.borderColor = '#e53e3e';
        setTimeout(() => document.getElementById('customerName').style.borderColor = '', 2000);
        return;
    }

    if (Object.keys(cart).length === 0) return;

    const items = Object.entries(cart).map(([id, item]) => ({
        product_id: parseInt(id),
        qty:        item.qty,
    }));

    btnOrder.disabled    = true;
    btnOrder.textContent = '⏳ Memproses...';

    try {
        const res = await fetch('{{ route("admin.kasir.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ customer_name: customerName, items, notes }),
        });

        const data = await res.json();

        if (data.success) {
            showToast(data.order_code, data.total);
            clearOrder();
        } else {
            alert('Gagal membuat pesanan: ' + (data.message ?? 'Unknown error'));
        }
    } catch (err) {
        alert('Terjadi kesalahan koneksi.');
    } finally {
        btnOrder.disabled    = false;
        btnOrder.innerHTML   = '☕ Buat Pesanan';
    }
}

// Toast notifikasi sukses
function showToast(code, total) {
    const toast = document.getElementById('toast');
    document.getElementById('toastCode').textContent =
        `Kode: ${code} · Total: Rp ${formatNum(total)}`;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
}

// Filter by kategori
function filterCat(cat, btn) {
    allCat = cat;
    document.querySelectorAll('.cat-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    filterMenu();
}

// Filter by search + kategori
function filterMenu() {
    const keyword = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.menu-item').forEach(el => {
        const name    = el.dataset.name.toLowerCase();
        const cat     = el.dataset.cat;
        const matchCat  = allCat === 'all' || cat === allCat;
        const matchName = name.includes(keyword);
        el.style.display = (matchCat && matchName) ? 'block' : 'none';
    });
}

function formatNum(n) {
    return new Intl.NumberFormat('id-ID').format(n);
}
</script>
@endpush