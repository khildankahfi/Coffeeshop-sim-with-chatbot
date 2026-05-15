@extends('layouts.main')
@section('title', 'Menu')
@push('styles')
<style>
.menu-page-header {
    background: var(--espresso); padding: 120px 48px 60px;
    position: relative; overflow: hidden;
}
.menu-page-header::before {
    content: 'MENU'; position: absolute; right: -20px; top: 50%; transform: translateY(-50%);
    font-family: 'Cormorant Garamond', serif; font-size: 180px; color: rgba(200,151,58,.04);
    font-weight: 600; pointer-events: none; white-space: nowrap;
}
.menu-page-header h1 { font-size: 52px; color: var(--cream); margin-bottom: 10px; }
.menu-page-header p { font-size: 15px; color: rgba(250,240,220,.45); font-weight: 300; }

.menu-page-body { padding: 48px; }

.filter-wrap {
    display: flex; gap: 8px; margin-bottom: 40px; flex-wrap: wrap;
    padding-bottom: 20px; border-bottom: 1px solid rgba(200,151,58,.1);
}
.filter-label { font-size: 12px; color: var(--muted); font-weight: 500; letter-spacing: .5px; text-transform: uppercase; align-self: center; margin-right: 4px; }
.filter-btn {
    padding: 7px 18px; border-radius: 20px; font-size: 13px; cursor: pointer;
    border: 1px solid rgba(200,151,58,.2); color: var(--muted);
    background: transparent; font-family: 'Outfit', sans-serif; transition: all .2s;
}
.filter-btn.active, .filter-btn:hover {
    background: var(--espresso); color: var(--cream); border-color: var(--espresso);
}

.cat-section { margin-bottom: 48px; }
.cat-heading {
    display: flex; align-items: center; gap: 14px; margin-bottom: 20px;
}
.cat-heading h3 { font-family: 'Cormorant Garamond', serif; font-size: 26px; color: var(--espresso); }
.cat-line { flex: 1; height: 1px; background: rgba(200,151,58,.15); }
.cat-count { font-size: 12px; color: var(--muted); background: rgba(200,151,58,.08); padding: 3px 12px; border-radius: 10px; }

.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap: 16px; }
.prod-card {
    background: #fff; border: 0.5px solid rgba(200,151,58,.15);
    border-radius: 20px; padding: 22px; cursor: pointer;
    transition: all .3s cubic-bezier(.34,1.56,.64,1); display: flex; flex-direction: column;
    position: relative; overflow: hidden;
}
.prod-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(44,18,8,.1); border-color: rgba(200,151,58,.35); }
.prod-emoji { font-size: 36px; margin-bottom: 14px; display: block; }
.prod-name { font-size: 14px; font-weight: 600; color: var(--espresso); margin-bottom: 4px; }
.prod-desc { font-size: 12px; color: var(--muted); line-height: 1.55; margin-bottom: 14px; flex: 1; font-weight: 300; }
.prod-footer { display: flex; align-items: center; justify-content: space-between; margin-top: auto; }
.prod-price { font-size: 15px; font-weight: 600; color: var(--accent); }
.prod-order {
    background: var(--espresso); color: var(--cream);
    border: none; padding: 7px 16px; border-radius: 16px;
    font-size: 12px; cursor: pointer; font-family: 'Outfit', sans-serif;
    font-weight: 500; transition: all .2s;
}
.prod-order:hover { background: var(--gold); color: var(--espresso); }
</style>
@endpush

@section('content')
<div class="menu-page-header">
    <div class="sec-tag">MENU KAMI</div>
    <h1>Semua Menu<br><em style="color:var(--gold)">BrewNest</em></h1>
    <p>Klik menu untuk langsung pesan via Karen</p>
</div>

<div class="menu-page-body">
    <div class="filter-wrap">
        <span class="filter-label">Filter:</span>
        <button class="filter-btn active" data-cat="all" onclick="filterCat('all',this)">Semua</button>
        @foreach($categories as $cat)
        <button class="filter-btn" data-cat="{{ $cat->slug }}" onclick="filterCat('{{ $cat->slug }}',this)">{{ $cat->name }}</button>
        @endforeach
    </div>

    @php
    $emojis = ['Americano'=>'☕','Cappuccino'=>'☕','Latte'=>'🥛','V60 Ethiopia'=>'🍵','Aeropress'=>'🫗','Matcha Latte'=>'🍃','Coklat Panas'=>'🍫','Croissant'=>'🥐','Banana Bread'=>'🍌'];
    @endphp

    @foreach($categories as $category)
    <div class="cat-section" data-section="{{ $category->slug }}">
        <div class="cat-heading">
            <h3>{{ $category->name }}</h3>
            <div class="cat-line"></div>
            <span class="cat-count">{{ $category->products->count() }} item</span>
        </div>
        <div class="products-grid">
            @foreach($category->products->where('is_available', true) as $product)
            <div class="prod-card">
                <span class="prod-emoji">{{ $emojis[$product->name] ?? '☕' }}</span>
                <div class="prod-name">{{ $product->name }}</div>
                <div class="prod-desc">{{ $product->description ?? 'Menu pilihan dari barista kami.' }}</div>
                <div class="prod-footer">
                    <span class="prod-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <button class="prod-order" onclick="openChatWith('Saya mau pesan {{ $product->name }} 1')">+ Pesan</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
let activeCat = 'all';
function filterCat(cat, btn) {
    activeCat = cat;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.cat-section').forEach(s => {
        s.style.display = (cat === 'all' || s.dataset.section === cat) ? 'block' : 'none';
    });
}
</script>
@endpush