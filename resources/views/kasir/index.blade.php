@extends('layouts.admin')
@section('title', '🖥️ Sistem Kasir')
@push('styles')
<style>
.kasir-wrap {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 20px;
    height: calc(100vh - 106px);
}

/* LEFT */
.menu-panel { display: flex; flex-direction: column; gap: 14px; overflow: hidden; }
.search-row { display: flex; gap: 10px; }
.search-inp {
    flex: 1; border: 1px solid rgba(200,151,58,.15); border-radius: 12px;
    padding: 10px 14px; font-size: 13px; outline: none;
    font-family: 'Outfit', sans-serif; background: #fff; color: #1A0800;
    transition: border .2s;
}
.search-inp:focus { border-color: var(--gold); }
.cat-row { display: flex; gap: 6px; flex-wrap: wrap; }
.cat-btn {
    padding: 6px 14px; border-radius: 16px; font-size: 12px; cursor: pointer;
    border: 1px solid rgba(200,151,58,.15); color: var(--muted);
    background: transparent; font-family: 'Outfit', sans-serif; transition: all .15s;
}
.cat-btn.active, .cat-btn:hover { background: var(--espresso); color: var(--cream); border-color: var(--espresso); }

.menu-scroll { flex: 1; overflow-y: auto; padding-right: 4px; }
.menu-scroll::-webkit-scrollbar { width: 3px; }
.menu-scroll::-webkit-scrollbar-thumb { background: rgba(200,151,58,.2); border-radius: 2px; }

.prod-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; }
.prod-card {
    background: #fff; border: 1.5px solid rgba(200,151,58,.1);
    border-radius: 14px; padding: 14px; cursor: pointer;
    transition: all .25s cubic-bezier(.34,1.56,.64,1);
    text-align: center; position: relative;
}
.prod-card:hover { border-color: rgba(200,151,58,.35); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(44,18,8,.08); }
.prod-card.selected { border-color: var(--gold); background: #FFFBF2; }
.prod-card.unavail { opacity: .45; cursor: not-allowed; pointer-events: none; }
.p-badge {
    position: absolute; top: -6px; right: -6px;
    width: 20px; height: 20px; border-radius: 50%;
    background: var(--espresso); color: var(--cream);
    font-size: 10px; font-weight: 700;
    display: none; align-items: center; justify-content: center;
}
.prod-card.selected .p-badge { display: flex; }
.p-emoji { font-size: 26px; margin-bottom: 8px; display: block; }
.p-name { font-size: 12px; font-weight: 600; color: #1A0800; margin-bottom: 2px; }
.p-cat { font-size: 9px; color: var(--muted); margin-bottom: 6px; letter-spacing: .5px; text-transform: uppercase; }
.p-price { font-size: 13px; font-weight: 600; color: var(--accent); }
.qty-ctrl { display: none; align-items: center; justify-content: center; gap: 8px; margin-top: 8px; }
.prod-card.selected .qty-ctrl { display: flex; }
.qty-btn {
    width: 24px; height: 24px; border-radius: 50%;
    border: 1.5px solid var(--gold); background: transparent; color: var(--gold);
    font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all .15s; font-family: 'Outfit', sans-serif; line-height: 1;
}
.qty-btn:hover { background: var(--gold); color: var(--espresso); }
.qty-num { font-size: 13px; font-weight: 600; color: #1A0800; min-width: 18px; text-align: center; }

/* RIGHT: ORDER PANEL */
.order-panel {
    background: #fff;
    border-radius: 16px;
    display: flex; flex-direction: column; overflow: hidden;
    border: 0.5px solid rgba(200,151,58,.15);
    box-shadow: 0 4px 20px rgba(44,18,8,.06);
}
.op-head {
    padding: 16px 18px;
    background: var(--espresso);
    border-bottom: none;
    display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;
    border-radius: 16px 16px 0 0;
}
.op-title { color: var(--cream); font-size: 14px; font-weight: 600; letter-spacing: .3px; }
.op-count { background: rgba(200,151,58,.2); color: var(--gold-lt); font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 10px; }

.op-body { flex: 1; overflow-y: auto; padding: 16px; background: #fff; }
.op-body::-webkit-scrollbar { width: 3px; }
.op-body::-webkit-scrollbar-thumb { background: rgba(200,151,58,.1); border-radius: 2px; }

.op-sec { margin-bottom: 14px; }
.op-lbl { font-size: 10px; font-weight: 600; color: var(--muted); letter-spacing: .8px; text-transform: uppercase; margin-bottom: 6px; display: block; }
.op-inp {
    width: 100%; background: var(--warm); border: 1px solid rgba(200,151,58,.15);
    border-radius: 10px; padding: 9px 12px; font-size: 13px; outline: none;
    color: #1A0800; font-family: 'Outfit', sans-serif; transition: border .2s;
}
.op-inp::placeholder { color: rgba(138,112,96,.45); }
.op-inp:focus { border-color: var(--gold); background: #fff; }
textarea.op-inp { resize: none; height: 54px; font-size: 12px; }

.op-items { min-height: 80px; }
.empty-cart { text-align: center; padding: 24px 16px; color: rgba(250,240,220,.2); }
.empty-cart-icon { font-size: 32px; margin-bottom: 8px; opacity: .3; display: block; }

.oi-row { 
    display: flex; align-items: center; gap: 8px; 
    padding: 9px 0; border-bottom: 1px solid rgba(200,151,58,.08); 
    animation: slideIn .2s ease; }

@keyframes slideIn { from { opacity:0; transform:translateX(8px); } to { opacity:1; transform:none; } }
.oi-row:last-child { border-bottom: none; }
.oi-name { flex: 1; font-size: 12px; font-weight: 500; color: #1A0800; }
.oi-qty { font-size: 11px; color: var(--muted); }
.oi-price { font-size: 12px; font-weight: 600; color: var(--accent); }
.oi-del { background: none; border: none; color: rgba(138,112,96,.3); cursor: pointer; font-size: 16px; padding: 0 3px; transition: color .15s; line-height: 1; }
.oi-del:hover { color: #dc2626; }

.op-divider { border: none; border-top: 1px dashed rgba(200,151,58,.12); margin: 10px 0; }
.sum-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 12px; }
.sum-row .lbl { color: var(--muted); }
.sum-row .val { color: #1A0800; font-weight: 500; }
.sum-row.total .lbl { font-size: 13px; font-weight: 600; color: #1A0800; }
.sum-row.total .val { font-size: 16px; font-weight: 700; color: var(--accent); }

/* Empty cart */
.empty-cart { text-align: center; padding: 24px 16px; color: rgba(138,112,96,.45); }
.empty-cart-icon { font-size: 32px; margin-bottom: 8px; opacity: .35; display: block; }

.op-footer { padding: 0 16px 16px; flex-shrink: 0; }
.btn-order {
    width: 100%; background: linear-gradient(135deg, var(--gold), var(--accent));
    color: var(--espresso); border: none; padding: 13px; border-radius: 12px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    font-family: 'Outfit', sans-serif; transition: all .2s; letter-spacing: .3px;
}
.btn-order:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(200,151,58,.3); }
.btn-order:disabled { opacity: .35; cursor: not-allowed; transform: none; box-shadow: none; }
.btn-clr {
    width: 100%; background: transparent; color: rgba(250,240,220,.25);
    border: 1px solid rgba(200,151,58,.06); padding: 9px; border-radius: 12px;
    font-size: 12px; cursor: pointer; font-family: 'Outfit', sans-serif;
    margin-top: 8px; transition: all .2s;
}
.btn-clr:hover { background: rgba(248,113,113,.08); color: #f87171; border-color: rgba(248,113,113,.12); }

/* TOAST */
.toast {
    position: fixed; top: 70px; right: 24px; z-index: 9999;
    background: var(--espresso); border: 1px solid rgba(200,151,58,.2);
    color: var(--cream); padding: 14px 18px; border-radius: 14px;
    box-shadow: 0 12px 32px rgba(0,0,0,.3); font-size: 13px; min-width: 260px;
    transform: translateY(-20px); opacity: 0;
    transition: all .3s cubic-bezier(.34,1.56,.64,1); pointer-events: none;
}
.toast.show { transform: translateY(0); opacity: 1; }
.toast-title { font-weight: 600; color: var(--gold); margin-bottom: 3px; font-size: 14px; }
.toast-sub { color: rgba(250,240,220,.45); font-size: 12px; }
</style>
@endpush

@section('content')
<div class="kasir-wrap">
    <!-- LEFT: MENU GRID -->
    <div class="menu-panel">
        <div class="search-row">
            <input type="text" class="search-inp" id="searchInput"
                   placeholder="🔍 Cari menu..." oninput="filterMenu()"/>
        </div>
        <div class="cat-row" id="catRow">
            <button class="cat-btn active" data-cat="all" onclick="filterCat('all',this)">Semua</button>
            @foreach($categories as $cat)
            <button class="cat-btn" data-cat="{{ $cat->slug }}" onclick="filterCat('{{ $cat->slug }}',this)">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>
        <div class="menu-scroll">
            @php
            $emojis = ['Americano'=>'☕','Cappuccino'=>'☕','Latte'=>'🥛','V60 Ethiopia'=>'🍵','Aeropress'=>'🫗','Matcha Latte'=>'🍃','Coklat Panas'=>'🍫','Croissant'=>'🥐','Banana Bread'=>'🍌'];
            @endphp
            <div class="prod-grid" id="prodGrid">
                @foreach($products as $product)
                <div class="prod-card {{ !$product->is_available ? 'unavail' : '' }}"
                     id="item-{{ $product->id }}"
                     data-id="{{ $product->id }}"
                     data-name="{{ $product->name }}"
                     data-price="{{ $product->price }}"
                     data-cat="{{ $product->category->slug }}"
                     onclick="{{ $product->is_available ? 'toggleItem(this)' : '' }}">
                    <div class="p-badge" id="badge-{{ $product->id }}">1</div>
                    <span class="p-emoji">{{ $emojis[$product->name] ?? '☕' }}</span>
                    <div class="p-name">{{ $product->name }}</div>
                    <div class="p-cat">{{ $product->category->name }}</div>
                    <div class="p-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    <div class="qty-ctrl" onclick="event.stopPropagation()">
                        <button class="qty-btn" onclick="changeQty({{ $product->id }}, -1)">−</button>
                        <span class="qty-num" id="qty-{{ $product->id }}">1</span>
                        <button class="qty-btn" onclick="changeQty({{ $product->id }}, 1)">+</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT: ORDER PANEL -->
    <div class="order-panel">
        <div class="op-head">
            <span class="op-title">🛒 Pesanan</span>
            <span class="op-count" id="opCount">0 item</span>
        </div>
        <div class="op-body">
            <div class="op-sec">
                <label class="op-lbl">Nama Pelanggan</label>
                <input type="text" class="op-inp" id="custName" placeholder="Masukkan nama..."/>
            </div>
            <div class="op-sec">
                <label class="op-lbl">Item Pesanan</label>
                <div class="op-items" id="opItems">
                    <div class="empty-cart">
                        <span class="empty-cart-icon">🛒</span>
                        Pilih menu dari kiri
                    </div>
                </div>
            </div>
            <hr class="op-divider">
            <div id="opSummary" style="display:none; margin-bottom:14px;">
                <div class="sum-row"><span class="lbl">Subtotal</span><span class="val" id="subtotalVal">Rp 0</span></div>
                <div class="sum-row total"><span class="lbl">Total</span><span class="val" id="totalVal">Rp 0</span></div>
            </div>
            <div class="op-sec">
                <label class="op-lbl">Catatan</label>
                <textarea class="op-inp" id="orderNotes" placeholder="Contoh: tanpa gula, es sedikit..."></textarea>
            </div>
        </div>
        <div class="op-footer">
            <button class="btn-order" id="btnOrder" onclick="submitOrder()" disabled>
                ☕ Buat Pesanan
            </button>
            <button class="btn-clr" onclick="clearOrder()">🗑️ Kosongkan</button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
    <div class="toast-title">✅ Pesanan berhasil dibuat!</div>
    <div class="toast-sub" id="toastSub"></div>
</div>
@endsection

@push('scripts')
<script>
let cart = {}, activeCat = 'all';
const fmt = n => new Intl.NumberFormat('id-ID').format(n);

function toggleItem(el) {
    const id = parseInt(el.dataset.id), name = el.dataset.name, price = parseFloat(el.dataset.price);
    if (cart[id]) {
        delete cart[id]; el.classList.remove('selected');
        document.getElementById(`qty-${id}`).textContent = '1';
        document.getElementById(`badge-${id}`).textContent = '1';
    } else {
        cart[id] = { name, price, qty: 1 }; el.classList.add('selected');
    }
    renderCart();
}

function changeQty(id, d) {
    if (!cart[id]) return;
    cart[id].qty = Math.max(1, cart[id].qty + d);
    document.getElementById(`qty-${id}`).textContent = cart[id].qty;
    document.getElementById(`badge-${id}`).textContent = cart[id].qty;
    renderCart();
}

function removeItem(id) {
    const el = document.getElementById(`item-${id}`);
    if (el) { el.classList.remove('selected'); document.getElementById(`qty-${id}`).textContent = '1'; document.getElementById(`badge-${id}`).textContent = '1'; }
    delete cart[id]; renderCart();
}

function renderCart() {
    const keys = Object.keys(cart);
    const list = document.getElementById('opItems'), summary = document.getElementById('opSummary');
    const count = document.getElementById('opCount'), btn = document.getElementById('btnOrder');
    if (!keys.length) {
        list.innerHTML = '<div class="empty-cart"><span class="empty-cart-icon">🛒</span>Pilih menu dari kiri</div>';
        summary.style.display = 'none'; count.textContent = '0 item'; btn.disabled = true; return;
    }
    let total = 0, totalQty = 0, html = '';
    keys.forEach(id => {
        const item = cart[id], sub = item.price * item.qty;
        total += sub; totalQty += item.qty;
        html += `<div class="oi-row"><div class="oi-name">${item.name}</div><div class="oi-qty">${item.qty}x</div><div class="oi-price">Rp ${fmt(sub)}</div><button class="oi-del" onclick="removeItem(${id})">×</button></div>`;
    });
    list.innerHTML = html; summary.style.display = 'block';
    count.textContent = `${totalQty} item`;
    document.getElementById('subtotalVal').textContent = `Rp ${fmt(total)}`;
    document.getElementById('totalVal').textContent = `Rp ${fmt(total)}`;
    btn.disabled = false;
}

function clearOrder() {
    Object.keys(cart).forEach(id => {
        const el = document.getElementById(`item-${id}`);
        if (el) { el.classList.remove('selected'); document.getElementById(`qty-${id}`).textContent = '1'; document.getElementById(`badge-${id}`).textContent = '1'; }
    });
    cart = {}; document.getElementById('custName').value = ''; document.getElementById('orderNotes').value = ''; renderCart();
}

async function submitOrder() {
    const name = document.getElementById('custName').value.trim();
    const notes = document.getElementById('orderNotes').value.trim();
    const btn = document.getElementById('btnOrder');
    if (!name) {
        const inp = document.getElementById('custName');
        inp.style.borderColor = '#f87171'; inp.focus();
        setTimeout(() => inp.style.borderColor = '', 2000); return;
    }
    if (!Object.keys(cart).length) return;
    const items = Object.entries(cart).map(([id, item]) => ({ product_id: parseInt(id), qty: item.qty }));
    btn.disabled = true; btn.textContent = '⏳ Memproses...';
    try {
        const res = await fetch('{{ route("admin.kasir.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ customer_name: name, items, notes }),
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('toastSub').textContent = `${data.order_code} · Total: Rp ${fmt(data.total)}`;
            const toast = document.getElementById('toast');
            toast.classList.add('show'); clearOrder();
            setTimeout(() => toast.classList.remove('show'), 3500);
        } else { alert('Gagal: ' + (data.message ?? 'Error')); }
    } catch { alert('Terjadi kesalahan koneksi.'); }
    finally { btn.disabled = false; btn.innerHTML = '☕ Buat Pesanan'; }
}

function filterCat(cat, btn) {
    activeCat = cat;
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active'); filterMenu();
}

function filterMenu() {
    const kw = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.prod-card').forEach(el => {
        const match = (activeCat === 'all' || el.dataset.cat === activeCat) && el.dataset.name.toLowerCase().includes(kw);
        el.style.display = match ? 'block' : 'none';
    });
}
</script>
@endpush