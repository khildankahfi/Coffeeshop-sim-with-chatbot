@extends('layouts.main')
@section('title', 'Beranda')
@push('styles')
<style>
.hero {
    background: var(--brown-main); padding: 80px 40px;
    text-align: center; position: relative; overflow: hidden;
}
.hero::before {
    content: '☕'; position: absolute; font-size: 300px;
    opacity: .04; top: -60px; right: -40px; pointer-events: none;
}
.hero-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #c8a97e22; border: 1px solid #c8a97e44;
    color: var(--brown-light); font-size: 12px; padding: 5px 16px;
    border-radius: 20px; margin-bottom: 20px;
}
.hero h1 { color: var(--cream); font-size: 44px; line-height: 1.2; margin-bottom: 16px; }
.hero h1 span { color: var(--brown-light); }
.hero p { color: #a08060; font-size: 15px; max-width: 440px; margin: 0 auto 32px; line-height: 1.7; }
.hero-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
.btn-primary { background: var(--brown-light); color: var(--brown-main); border: none; padding: 12px 28px; border-radius: 24px; font-size: 14px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background .2s; }
.btn-primary:hover { background: var(--cream); }
.btn-ghost { background: transparent; color: var(--brown-light); border: 1.5px solid #c8a97e55; padding: 12px 28px; border-radius: 24px; font-size: 14px; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all .2s; }
.btn-ghost:hover { border-color: var(--brown-light); color: var(--cream); }

.stats-bar { display: grid; grid-template-columns: repeat(3,1fr); background: var(--brown-dark); }
.stat-item { padding: 22px; text-align: center; border-right: 0.5px solid #4a2510; }
.stat-item:last-child { border-right: none; }
.stat-num { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--brown-light); }
.stat-lbl { font-size: 12px; color: #6a4030; margin-top: 3px; }

.section { padding: 60px 40px; }
.section-tag { display: inline-block; background: var(--cream-mid); color: var(--text-muted); font-size: 11px; padding: 4px 14px; border-radius: 12px; margin-bottom: 10px; font-weight: 500; }
.section-title { font-size: 30px; color: var(--brown-main); margin-bottom: 8px; }
.section-desc { font-size: 14px; color: var(--text-muted); line-height: 1.7; margin-bottom: 32px; }

.menu-tabs { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
.tab-btn { padding: 7px 18px; border-radius: 20px; font-size: 13px; cursor: pointer; border: 1px solid #d4b896; color: var(--brown-mid); background: transparent; font-family: 'DM Sans', sans-serif; transition: all .2s; }
.tab-btn.active, .tab-btn:hover { background: var(--brown-main); color: var(--cream); border-color: var(--brown-main); }

.menu-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
.menu-card { background: #fff; border: 0.5px solid #e8d8c4; border-radius: 14px; padding: 18px; cursor: pointer; transition: transform .2s, box-shadow .2s; }
.menu-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(61,31,10,.12); }
.menu-emoji { font-size: 30px; margin-bottom: 12px; }
.menu-name { font-size: 14px; font-weight: 600; color: var(--brown-main); margin-bottom: 3px; }
.menu-cat { font-size: 11px; color: var(--text-muted); margin-bottom: 12px; }
.menu-footer { display: flex; align-items: center; justify-content: space-between; }
.menu-price { font-size: 14px; font-weight: 600; color: var(--accent); }
.menu-add { width: 30px; height: 30px; border-radius: 50%; background: var(--brown-main); border: none; color: var(--cream); font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .2s; }
.menu-add:hover { background: var(--accent); }

.view-all { text-align: center; margin-top: 32px; }
.btn-outline { background: transparent; border: 1.5px solid var(--brown-main); color: var(--brown-main); padding: 11px 30px; border-radius: 24px; font-size: 13px; cursor: pointer; font-family: 'DM Sans', sans-serif; font-weight: 500; transition: all .2s; }
.btn-outline:hover { background: var(--brown-main); color: var(--cream); }

.features-section { background: var(--cream-mid); }
.features-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; }
.feature-card { background: #fff; border-radius: 14px; padding: 26px; border: 0.5px solid #e8d8c4; }
.feature-icon { font-size: 28px; margin-bottom: 14px; }
.feature-title { font-size: 15px; font-weight: 600; color: var(--brown-main); margin-bottom: 6px; }
.feature-desc { font-size: 13px; color: var(--text-muted); line-height: 1.65; }

.ai-cta { background: var(--brown-main); padding: 60px 40px; text-align: center; }
.ai-cta h2 { color: var(--cream); font-size: 30px; margin-bottom: 12px; }
.ai-cta p { color: #a08060; font-size: 14px; max-width: 400px; margin: 0 auto 28px; line-height: 1.7; }
.chat-demo { display: flex; flex-direction: column; gap: 8px; max-width: 300px; margin: 0 auto 28px; }
.demo-bubble { padding: 10px 16px; border-radius: 12px; font-size: 13px; line-height: 1.5; }
.demo-bubble.user { background: #c8a97e22; color: var(--brown-light); border: 1px solid #c8a97e33; align-self: flex-end; border-bottom-right-radius: 3px; }
.demo-bubble.ai { background: #ffffff11; color: var(--cream); align-self: flex-start; border-bottom-left-radius: 3px; }
</style>
@endpush

@section('content')
<!-- Hero -->
<section class="hero">
    <div class="hero-badge">✨ AI-Powered Coffeeshop</div>
    <h1>Kopi sempurna,<br>dipesan <span>lebih mudah</span></h1>
    <p>Nikmati specialty coffee dengan bantuan Karen, asisten AI kami yang siap rekomendasikan menu terbaik untukmu.</p>
    <div class="hero-btns">
        <a href="{{ route('menu.index') }}"><button class="btn-primary">Lihat Menu →</button></a>
        <button class="btn-ghost" onclick="openChatWith('Halo Karen!')">💬 Chat dengan Karen</button>
    </div>
</section>

<!-- Stats -->
<div class="stats-bar">
    <div class="stat-item"><div class="stat-num">9+</div><div class="stat-lbl">Menu pilihan</div></div>
    <div class="stat-item"><div class="stat-num">4.9★</div><div class="stat-lbl">Rating pelanggan</div></div>
    <div class="stat-item"><div class="stat-num">24/7</div><div class="stat-lbl">AI siap melayani</div></div>
</div>

<!-- Menu Preview -->
<section class="section">
    <div class="section-tag">Menu Unggulan</div>
    <h2 class="section-title">Pilihan terbaik hari ini</h2>
    <p class="section-desc">Klik menu untuk langsung pesan via Karen</p>

    <div class="menu-grid">
        @php
        $items = [
            ['emoji'=>'☕','name'=>'Americano',    'cat'=>'Espresso Based','price'=>'Rp 25.000','id'=>1],
            ['emoji'=>'🥛','name'=>'Latte',        'cat'=>'Espresso Based','price'=>'Rp 30.000','id'=>3],
            ['emoji'=>'🍵','name'=>'V60 Ethiopia', 'cat'=>'Manual Brew',   'price'=>'Rp 35.000','id'=>4],
            ['emoji'=>'🍃','name'=>'Matcha Latte', 'cat'=>'Non Coffee',    'price'=>'Rp 28.000','id'=>6],
            ['emoji'=>'🍫','name'=>'Coklat Panas', 'cat'=>'Non Coffee',    'price'=>'Rp 22.000','id'=>7],
            ['emoji'=>'🥐','name'=>'Croissant',    'cat'=>'Food',          'price'=>'Rp 20.000','id'=>8],
        ];
        @endphp
        @foreach($items as $item)
        <div class="menu-card" onclick="openChatWith('Saya mau pesan {{ $item['name'] }} 1')">
            <div class="menu-emoji">{{ $item['emoji'] }}</div>
            <div class="menu-name">{{ $item['name'] }}</div>
            <div class="menu-cat">{{ $item['cat'] }}</div>
            <div class="menu-footer">
                <span class="menu-price">{{ $item['price'] }}</span>
                <button class="menu-add" aria-label="Pesan {{ $item['name'] }}">+</button>
            </div>
        </div>
        @endforeach
    </div>
    <div class="view-all">
        <a href="{{ route('menu.index') }}"><button class="btn-outline">Lihat Semua Menu →</button></a>
    </div>
</section>

<!-- Features -->
<section class="section features-section">
    <div class="section-tag">Kenapa BrewNest?</div>
    <h2 class="section-title">Lebih dari sekadar kopi</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🤖</div>
            <div class="feature-title">AI Asisten Karen</div>
            <div class="feature-desc">Karen siap merekomendasikan menu, menerima pesanan, dan menjawab pertanyaanmu kapan saja.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <div class="feature-title">Order Super Cepat</div>
            <div class="feature-desc">Cukup chat dengan Karen, pesananmu langsung tercatat tanpa perlu antri atau isi form.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon">☕</div>
            <div class="feature-title">Specialty Coffee</div>
            <div class="feature-desc">Biji kopi single origin pilihan, diseduh dengan standar specialty untuk pengalaman terbaik.</div>
        </div>
    </div>
</section>

<!-- AI CTA -->
<section class="ai-cta">
    <h2>Pesan lewat chat, semudah itu</h2>
    <p>Karen memahami pesananmu secara natural — tidak perlu klik form panjang.</p>
    <div class="chat-demo">
        <div class="demo-bubble user">Ada kopi yang tidak terlalu pahit?</div>
        <div class="demo-bubble ai">Coba Latte atau Cappuccino! Creamy dan smooth. Mau saya pesankan? 😊</div>
        <div class="demo-bubble user">Latte satu, nama saya Dika</div>
        <div class="demo-bubble ai">Siap! 1x Latte untuk Dika — Rp 30.000 ☕ Pesanan masuk!</div>
    </div>
    <button class="btn-primary" onclick="openChatWith('Halo Karen, saya mau pesan!')">Coba Sekarang →</button>
</section>
@endsection