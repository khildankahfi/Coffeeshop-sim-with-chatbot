@extends('layouts.main')
@section('title', 'Beranda')
@push('styles')
<style>
/* ===== HERO ===== */
.hero {
    min-height: 100vh; background: var(--espresso);
    display: flex; align-items: center;
    position: relative; overflow: hidden; padding: 100px 48px 80px;
}
.hero-bg {
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 60% 70% at 80% 50%, rgba(200,151,58,.1) 0%, transparent 70%),
                radial-gradient(ellipse 40% 50% at 20% 80%, rgba(212,133,74,.07) 0%, transparent 60%);
}
.hero-ring {
    position: absolute; right: -80px; top: 50%; transform: translateY(-50%);
    width: 520px; height: 520px; border-radius: 50%;
    border: 1px solid rgba(200,151,58,.08);
}
.hero-ring::before {
    content: ''; position: absolute; inset: 50px; border-radius: 50%;
    border: 1px solid rgba(200,151,58,.05);
}
.hero-ring::after {
    content: '☕'; position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 130px; opacity: .05;
}
.hero-content { position: relative; z-index: 2; max-width: 580px; }
.hero-tag {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(200,151,58,.08); border: 1px solid rgba(200,151,58,.2);
    color: var(--gold); font-size: 12px; padding: 6px 16px;
    border-radius: 20px; margin-bottom: 28px; letter-spacing: .5px; font-weight: 500;
}
.hero-tag-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--gold); animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.3} }
.hero h1 {
    font-size: 70px; line-height: 1.02; color: var(--cream);
    margin-bottom: 20px; font-weight: 600;
}
.hero h1 em { color: var(--gold); font-style: italic; }
.hero p {
    font-size: 16px; color: rgba(250,240,220,.5); line-height: 1.7;
    margin-bottom: 36px; max-width: 420px; font-weight: 300;
}
.hero-btns { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
.btn-ghost-light {
    background: transparent; color: rgba(250,240,220,.65);
    border: 1px solid rgba(200,151,58,.25); padding: 13px 28px;
    border-radius: 24px; font-size: 14px; cursor: pointer;
    font-family: 'Outfit', sans-serif; transition: all .2s; letter-spacing: .3px;
}
.btn-ghost-light:hover { border-color: var(--gold); color: var(--cream); }

/* ===== STATS ===== */
.stats-bar {
    display: grid; grid-template-columns: repeat(3,1fr);
    background: var(--brown); border-bottom: 1px solid rgba(200,151,58,.1);
}
.stat-item { padding: 28px 40px; border-right: 1px solid rgba(200,151,58,.08); }
.stat-item:last-child { border-right: none; }
.stat-num { font-family: 'Cormorant Garamond', serif; font-size: 36px; color: var(--gold); font-weight: 600; margin-bottom: 4px; }
.stat-lbl { font-size: 11px; color: var(--muted); letter-spacing: .6px; font-weight: 400; text-transform: uppercase; }

/* ===== MENU SECTION ===== */
.menu-section { padding: 80px 48px; background: var(--cream); }
.menu-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 36px; }
.menu-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
.menu-card {
    background: #fff; border: 0.5px solid rgba(200,151,58,.15);
    border-radius: 20px; padding: 24px; cursor: pointer;
    transition: all .3s cubic-bezier(.34,1.56,.64,1);
    position: relative; overflow: hidden;
}
.menu-card::after {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse at top right, rgba(200,151,58,.06), transparent 60%);
    opacity: 0; transition: opacity .3s;
}
.menu-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(44,18,8,.1); border-color: rgba(200,151,58,.4); }
.menu-card:hover::after { opacity: 1; }
.card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; }
.card-emoji { font-size: 36px; }
.card-cat-badge {
    background: rgba(200,151,58,.1); color: var(--gold);
    font-size: 10px; padding: 3px 10px; border-radius: 10px; font-weight: 500; letter-spacing: .3px;
}
.card-name { font-size: 15px; font-weight: 600; color: var(--espresso); margin-bottom: 12px; }
.card-footer { display: flex; align-items: center; justify-content: space-between; }
.card-price { font-size: 15px; font-weight: 600; color: var(--accent); }
.card-btn {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--espresso); border: none; color: var(--cream);
    font-size: 18px; cursor: pointer; display: flex;
    align-items: center; justify-content: center; transition: all .25s;
}
.card-btn:hover { background: var(--gold); color: var(--espresso); transform: rotate(90deg) scale(1.1); }
.view-all { text-align: center; margin-top: 36px; }

/* ===== PERSONAL SECTION ===== */
.personal-section {
    background: var(--brown); padding: 80px 48px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
}
.personal-form-side {}
.personal-inp-wrap { display: flex; gap: 10px; margin-top: 24px; }
.personal-inp {
    flex: 1; background: rgba(255,255,255,.06); border: 1px solid rgba(200,151,58,.2);
    border-radius: 20px; padding: 12px 18px; font-size: 14px; outline: none;
    color: var(--cream); font-family: 'Outfit', sans-serif;
}
.personal-inp::placeholder { color: rgba(250,240,220,.3); }
.personal-inp:focus { border-color: rgba(200,151,58,.5); }
.personal-result { display: none; margin-top: 24px; }
.personal-card {
    background: rgba(255,255,255,.04); border: 1px solid rgba(200,151,58,.12);
    border-radius: 16px; padding: 20px;
}
.personal-greeting { color: var(--cream); font-size: 15px; font-weight: 600; margin-bottom: 4px; }
.personal-sub { color: rgba(250,240,220,.45); font-size: 13px; margin-bottom: 16px; font-weight: 300; }
.personal-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 16px; }
.personal-item {
    background: rgba(255,255,255,.04); border: 1px solid rgba(200,151,58,.1);
    border-radius: 12px; padding: 14px; text-align: center; cursor: pointer;
    transition: all .2s;
}
.personal-item:hover { background: rgba(200,151,58,.1); border-color: rgba(200,151,58,.3); }
.personal-item .p-emoji { font-size: 24px; margin-bottom: 6px; }
.personal-item .p-name { font-size: 12px; font-weight: 600; color: var(--cream); margin-bottom: 3px; }
.personal-item .p-price { font-size: 11px; color: var(--gold); }
.personal-item .p-count { font-size: 10px; color: var(--muted); margin-top: 3px; }
.personal-new { display: none; margin-top: 20px; }
.new-card {
    background: rgba(255,255,255,.03); border: 1px solid rgba(200,151,58,.1);
    border-radius: 16px; padding: 24px; text-align: center;
}

/* ===== AI SECTION ===== */
.ai-section {
    padding: 80px 48px; background: var(--espresso);
    display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
}
.ai-demo {
    background: rgba(200,151,58,.04); border: 1px solid rgba(200,151,58,.1);
    border-radius: 20px; padding: 24px;
}
.demo-msg { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 12px; }
.demo-msg.user { flex-direction: row-reverse; }
.demo-msg:last-child { margin-bottom: 0; }
.demo-av {
    width: 30px; height: 30px; border-radius: 50%;
    background: rgba(200,151,58,.12); display: flex; align-items: center;
    justify-content: center; font-size: 14px; flex-shrink: 0;
}
.demo-bub { padding: 9px 13px; border-radius: 14px; font-size: 13px; line-height: 1.5; max-width: 80%; }
.demo-msg.assistant .demo-bub { background: rgba(255,255,255,.05); color: rgba(250,240,220,.8); border: 0.5px solid rgba(200,151,58,.1); border-bottom-left-radius: 3px; }
.demo-msg.user .demo-bub { background: linear-gradient(135deg, var(--gold), var(--accent)); color: var(--espresso); font-weight: 500; border-bottom-right-radius: 3px; }

/* ===== FEATURES ===== */
.features-section { padding: 80px 48px; background: var(--cream); }
.features-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; margin-top: 40px; }
.feat-card {
    background: #fff; border: 0.5px solid rgba(200,151,58,.15);
    border-radius: 20px; padding: 28px; transition: all .3s;
}
.feat-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(44,18,8,.08); }
.feat-icon { font-size: 28px; margin-bottom: 14px; display: block; }
.feat-title { font-size: 15px; font-weight: 600; color: var(--espresso); margin-bottom: 8px; }
.feat-desc { font-size: 13px; color: var(--muted); line-height: 1.65; font-weight: 300; }
</style>
@endpush

@section('content')
<!-- HERO -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-ring"></div>
    <div class="hero-content">
        <div class="hero-tag"><span class="hero-tag-dot"></span> AI-Powered Specialty Coffee</div>
        <h1>Kopi sempurna,<br>dipesan <em>lebih mudah</em></h1>
        <p>Karen, asisten AI kami, mengenal selera dan kebiasaan pesan kamu — merekomendasikan menu favorit secara proaktif.</p>
        <div class="hero-btns">
            <a href="{{ route('menu.index') }}"><button class="btn-primary">Lihat Menu →</button></a>
            <button class="btn-ghost-light" onclick="openChatWith('Halo Karen!')">💬 Chat dengan Karen</button>
        </div>
    </div>
</section>

<!-- STATS -->
<div class="stats-bar">
    <div class="stat-item"><div class="stat-num">9+</div><div class="stat-lbl">Menu Pilihan</div></div>
    <div class="stat-item"><div class="stat-num">4.9★</div><div class="stat-lbl">Rating Pelanggan</div></div>
    <div class="stat-item"><div class="stat-num">24/7</div><div class="stat-lbl">AI Siap Melayani</div></div>
</div>

<!-- MENU PREVIEW -->
<section class="menu-section">
    <div class="menu-header">
        <div>
            <div class="sec-tag">MENU UNGGULAN</div>
            <h2 class="sec-title">Pilihan terbaik hari ini</h2>
            <p class="sec-sub" style="margin-bottom:0">Klik menu untuk langsung pesan via Karen</p>
        </div>
        <a href="{{ route('menu.index') }}"><button class="btn-outline">Lihat Semua →</button></a>
    </div>
    @php
    $items = [
        ['emoji'=>'☕','name'=>'Americano',    'cat'=>'ESPRESSO','price'=>'Rp 25.000'],
        ['emoji'=>'🥛','name'=>'Latte',        'cat'=>'ESPRESSO','price'=>'Rp 30.000'],
        ['emoji'=>'🍵','name'=>'V60 Ethiopia', 'cat'=>'MANUAL BREW','price'=>'Rp 35.000'],
        ['emoji'=>'🍃','name'=>'Matcha Latte', 'cat'=>'NON COFFEE','price'=>'Rp 28.000'],
        ['emoji'=>'🍫','name'=>'Coklat Panas', 'cat'=>'NON COFFEE','price'=>'Rp 22.000'],
        ['emoji'=>'🥐','name'=>'Croissant',    'cat'=>'FOOD','price'=>'Rp 20.000'],
    ];
    @endphp
    <div class="menu-grid">
        @foreach($items as $item)
        <div class="menu-card" onclick="openChatWith('Saya mau pesan {{ $item['name'] }} 1')">
            <div class="card-top">
                <span class="card-emoji">{{ $item['emoji'] }}</span>
                <span class="card-cat-badge">{{ $item['cat'] }}</span>
            </div>
            <div class="card-name">{{ $item['name'] }}</div>
            <div class="card-footer">
                <span class="card-price">{{ $item['price'] }}</span>
                <button class="card-btn">+</button>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- PERSONAL RECOMMENDATION -->
<section class="personal-section">
    <div class="personal-form-side">
        <div class="sec-tag" style="background:rgba(200,151,58,.1);color:var(--gold)">PERSONALISASI</div>
        <h2 style="font-size:38px;color:var(--cream);margin-bottom:10px;line-height:1.1">Pesanan <em style="color:var(--gold)">favoritmu</em><br>menunggu</h2>
        <p style="font-size:14px;color:rgba(250,240,220,.45);line-height:1.7;font-weight:300">Masukkan namamu dan Karen akan tampilkan rekomendasi berdasarkan riwayat pesananmu — tanpa kamu harus minta.</p>
        <div class="personal-inp-wrap">
            <input type="text" id="personalName" class="personal-inp" placeholder="Masukkan namamu..."
                   onkeydown="if(event.key==='Enter') loadPersonal()"/>
            <button class="btn-primary" onclick="loadPersonal()">Cek →</button>
        </div>
        <div id="personal-loading" style="display:none;margin-top:16px;color:rgba(250,240,220,.4);font-size:13px;">⏳ Karen sedang mencari pesanan favoritmu...</div>
    </div>

    <div>
        <div class="personal-result" id="personal-result">
            <div class="personal-card">
                <div class="personal-greeting" id="personal-greeting"></div>
                <div class="personal-sub" id="personal-sub"></div>
                <div class="personal-grid" id="personal-grid"></div>
                <button class="btn-primary" style="width:100%" onclick="openChatWithPersonal()">☕ Pesan Sekarang via Karen</button>
            </div>
        </div>
        <div class="personal-new" id="personal-new">
            <div class="new-card">
                <div style="font-size:36px;margin-bottom:12px">👋</div>
                <div style="font-size:15px;font-weight:600;color:var(--cream);margin-bottom:6px" id="new-greeting"></div>
                <div style="font-size:13px;color:rgba(250,240,220,.4);margin-bottom:16px;font-weight:300">Yuk mulai pesan dan biarkan Karen merekomendasikan menu terbaik untukmu!</div>
                <button class="btn-primary" onclick="openChatWith('Halo Karen! Saya mau lihat menu')">Mulai Chat →</button>
            </div>
        </div>
        <div id="personal-placeholder" style="text-align:center;padding:40px 20px">
            <div style="font-size:48px;margin-bottom:12px;opacity:.3">☕</div>
            <div style="font-size:13px;color:rgba(250,240,220,.3);font-weight:300">Masukkan namamu untuk melihat<br>rekomendasi personal dari Karen</div>
        </div>
    </div>
</section>

<!-- AI DEMO SECTION -->
<section class="ai-section">
    <div>
        <div class="sec-tag">AGENTIC AI</div>
        <h2 style="font-size:40px;color:var(--cream);margin-bottom:12px;line-height:1.1">Karen mengenal<br><em style="color:var(--gold)">selera kamu</em></h2>
        <p style="font-size:14px;color:rgba(250,240,220,.45);line-height:1.7;max-width:380px;margin-bottom:28px;font-weight:300">Karen belajar dari riwayat pesananmu dan secara proaktif merekomendasikan menu favoritmu — tanpa kamu harus minta.</p>
        <button class="btn-primary" onclick="openChatWith('Halo Karen, saya mau pesan!')">Coba Sekarang →</button>
    </div>
    <div class="ai-demo">
        <div class="demo-msg assistant"><div class="demo-av">☕</div><div class="demo-bub">Halo Rio! Senang ketemu lagi 😊<br><br>Menu favoritmu: Americano (12x dipesan)<br>Mau pesan seperti biasa?</div></div>
        <div class="demo-msg user"><div class="demo-av">👤</div><div class="demo-bub">Iya, tambah Croissant 1 ya</div></div>
        <div class="demo-msg assistant"><div class="demo-av">☕</div><div class="demo-bub">✅ Pesanan berhasil!<br>🧾 ORD-20260515-001<br>💰 Total: Rp 45.000 ☕</div></div>
    </div>
</section>

<!-- FEATURES -->
<section class="features-section">
    <div class="sec-tag">KENAPA BREWNEST?</div>
    <h2 class="sec-title">Lebih dari sekadar kopi</h2>
    <div class="features-grid">
        <div class="feat-card"><span class="feat-icon">🤖</span><div class="feat-title">AI Asisten Karen</div><div class="feat-desc">Karen mengenal kamu, merekomendasikan menu favorit, dan proses pesanan secara otomatis.</div></div>
        <div class="feat-card"><span class="feat-icon">⚡</span><div class="feat-title">Order Super Cepat</div><div class="feat-desc">Cukup chat, pesananmu langsung tercatat tanpa perlu antri atau isi form yang panjang.</div></div>
        <div class="feat-card"><span class="feat-icon">☕</span><div class="feat-title">Specialty Coffee</div><div class="feat-desc">Biji kopi single origin pilihan, diseduh dengan standar specialty terbaik setiap harinya.</div></div>
    </div>
</section>
@endsection

@push('scripts')
<script>
let personalName = '', personalFavorites = [];

const emojis = { 'Americano':'☕','Cappuccino':'☕','Latte':'🥛','V60 Ethiopia':'🍵','Aeropress':'🫗','Matcha Latte':'🍃','Coklat Panas':'🍫','Croissant':'🥐','Banana Bread':'🍌' };

async function loadPersonal() {
    const name = document.getElementById('personalName').value.trim();
    if (!name) return;
    personalName = name;
    document.getElementById('personal-loading').style.display = 'block';
    document.getElementById('personal-result').style.display  = 'none';
    document.getElementById('personal-new').style.display     = 'none';
    document.getElementById('personal-placeholder').style.display = 'none';
    try {
        const res  = await fetch('{{ route("api.personal.recommendation") }}', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ name }),
        });
        const data = await res.json();
        document.getElementById('personal-loading').style.display = 'none';
        if (data.is_returning) {
            personalFavorites = data.favorites;
            document.getElementById('personal-greeting').textContent = `Halo ${data.customer_name}! Senang ketemu lagi 😊`;
            document.getElementById('personal-sub').textContent = `Kamu sudah ${data.total_orders}x pesan di BrewNest. Menu favoritmu:`;
            document.getElementById('personal-grid').innerHTML = data.favorites.map(item => `
                <div class="personal-item" onclick="openChatWith('Saya mau pesan ${item.name} 1')">
                    <div class="p-emoji">${emojis[item.name] || '☕'}</div>
                    <div class="p-name">${item.name}</div>
                    <div class="p-price">${item.price_formatted}</div>
                    <div class="p-count">${item.total_ordered}x dipesan</div>
                </div>
            `).join('');
            document.getElementById('personal-result').style.display = 'block';
        } else {
            document.getElementById('new-greeting').textContent = `Halo ${data.customer_name}! Selamat datang di BrewNest ☕`;
            document.getElementById('personal-new').style.display = 'block';
        }
    } catch {
        document.getElementById('personal-loading').style.display = 'none';
        document.getElementById('personal-placeholder').style.display = 'block';
    }
}

function openChatWithPersonal() {
    const msg = personalFavorites.length > 0
        ? `Halo Karen! Saya ${personalName}, mau pesan ${personalFavorites[0].name} seperti biasa`
        : `Halo Karen! Saya ${personalName}`;
    openChatWith(msg);
}
</script>
@endpush