@extends('layouts.app')
@section('title', 'Menu')
@push('styles')
<style>
.page-header { background: var(--brown-main); padding: 40px; }
.page-header h1 { color: var(--cream); font-size: 32px; margin-bottom: 6px; }
.page-header p { color: #a08060; font-size: 14px; }

.menu-page { padding: 40px; }
.filter-bar { display: flex; gap: 10px; margin-bottom: 32px; flex-wrap: wrap; align-items: center; }
.filter-label { font-size: 13px; color: var(--text-muted); font-weight: 500; margin-right: 4px; }
.filter-btn { padding: 7px 18px; border-radius: 20px; font-size: 13px; cursor: pointer; border: 1px solid #d4b896; color: var(--brown-mid); background: transparent; font-family: 'DM Sans', sans-serif; transition: all .2s; }
.filter-btn.active, .filter-btn:hover { background: var(--brown-main); color: var(--cream); border-color: var(--brown-main); }

.category-section { margin-bottom: 40px; }
.cat-title { font-family: 'Playfair Display', serif; font-size: 20px; color: var(--brown-main); margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid var(--cream-mid); display: flex; align-items: center; gap: 10px; }
.cat-badge { background: var(--cream-mid); color: var(--text-muted); font-family: 'DM Sans', sans-serif; font-size: 12px; padding: 2px 10px; border-radius: 10px; }

.products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
.product-card { background: #fff; border: 0.5px solid #e8d8c4; border-radius: 14px; padding: 20px; cursor: pointer; transition: transform .2s, box-shadow .2s; display: flex; flex-direction: column; }
.product-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(61,31,10,.12); }
.product-emoji { font-size: 32px; margin-bottom: 12px; }
.product-name { font-size: 14px; font-weight: 600; color: var(--brown-main); margin-bottom: 4px; }
.product-desc { font-size: 12px; color: var(--text-muted); line-height: 1.5; margin-bottom: 12px; flex: 1; }
.product-footer { display: flex; align-items: center; justify-content: space-between; margin-top: auto; }
.product-price { font-size: 15px; font-weight: 600; color: var(--accent); }
.product-order {
    background: var(--brown-main); color: var(--cream);
    border: none; padding: 7px 14px; border-radius: 16px;
    font-size: 12px; cursor: pointer; font-family: 'DM Sans', sans-serif;
    font-weight: 500; transition: background .2s;
}
.product-order:hover { background: var(--accent); }

.empty-state { text-align: center; padding: 60px 20px; color: var(--text-muted); }
.empty-state .emoji { font-size: 48px; margin-bottom: 16px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>Menu BrewNest</h1>
    <p>Semua pilihan menu kami — klik untuk langsung pesan via Karen</p>
</div>

<div class="menu-page">
    <!-- Filter -->
    <div class="filter-bar">
        <span class="filter-label">Kategori:</span>
        <button class="filter-btn active" data-cat="all">Semua</button>
        @foreach($categories as $cat)
        <button class="filter-btn" data-cat="{{ $cat->slug }}">{{ $cat->name }}</button>
        @endforeach
    </div>

    <!-- Products per kategori -->
    @foreach($categories as $category)
    <div class="category-section" data-section="{{ $category->slug }}">
        <div class="cat-title">
            {{ $category->name }}
            <span class="cat-badge">{{ $category->products->count() }} item</span>
        </div>
        <div class="products-grid">
            @foreach($category->products->where('is_available', true) as $product)
            @php
            $emojis = ['Americano'=>'☕','Cappuccino'=>'☕','Latte'=>'🥛','V60 Ethiopia'=>'🍵','Aeropress'=>'🫗','Matcha Latte'=>'🍃','Coklat Panas'=>'🍫','Croissant'=>'🥐','Banana Bread'=>'🍌'];
            $emoji = $emojis[$product->name] ?? '☕';
            @endphp
            <div class="product-card">
                <div class="product-emoji">{{ $emoji }}</div>
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-desc">{{ $product->description ?? 'Menu pilihan dari barista kami.' }}</div>
                <div class="product-footer">
                    <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <button class="product-order"
                        onclick="openChatWith('Saya mau pesan {{ $product->name }} 1')">
                        + Pesan
                    </button>
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
// Filter kategori
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const cat = btn.dataset.cat;
        document.querySelectorAll('.category-section').forEach(sec => {
            sec.style.display = (cat === 'all' || sec.dataset.section === cat) ? 'block' : 'none';
        });
    });
});
</script>
@endpush