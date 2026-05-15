<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BrewNest — @yield('title', 'Specialty Coffeeshop')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400;1,600&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --espresso: #0F0500;
            --brown:    #2C1208;
            --gold:     #C8973A;
            --gold-lt:  #E8C87A;
            --cream:    #FAF0DC;
            --warm:     #F5E6C4;
            --cream-mid:#F0E4C4;
            --muted:    #8A7060;
            --accent:   #D4854A;
            --green:    #2A7A5A;
            --text:     #1A0800;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Outfit', sans-serif; background: var(--cream); color: var(--text); overflow-x: hidden; }
        h1, h2, h3 { font-family: 'Cormorant Garamond', serif; }
        a { text-decoration: none; }

        /* Grain overlay */
        body::before {
            content: ''; position: fixed; inset: 0; pointer-events: none; z-index: 1000; opacity: .35;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 48px; height: 68px;
            background: rgba(15,5,0,.88);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(200,151,58,.12);
        }
        .nav-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px; color: var(--cream); letter-spacing: .5px;
            display: flex; align-items: center; gap: 10px;
        }
        .nav-brand span { color: var(--gold); }
        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-links a {
            color: rgba(250,240,220,.55); font-size: 13px; font-weight: 400;
            padding: 7px 16px; border-radius: 20px; transition: all .2s; letter-spacing: .3px;
        }
        .nav-links a:hover, .nav-links a.active { color: var(--cream); background: rgba(200,151,58,.1); }
        .nav-cta {
            background: var(--gold); color: var(--espresso);
            border: none; padding: 9px 20px; border-radius: 20px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            font-family: 'Outfit', sans-serif; transition: all .2s; letter-spacing: .3px;
        }
        .nav-cta:hover { background: var(--gold-lt); transform: translateY(-1px); }

        /* ===== FLOATING CHAT ===== */
        #chat-fab {
            position: fixed; bottom: 32px; right: 32px; z-index: 999;
            width: 60px; height: 60px; border-radius: 50%;
            background: linear-gradient(135deg, var(--gold) 0%, var(--accent) 100%);
            border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(200,151,58,.4);
            transition: all .3s cubic-bezier(.34,1.56,.64,1);
            font-size: 26px;
        }
        #chat-fab:hover { transform: scale(1.1); box-shadow: 0 12px 32px rgba(200,151,58,.5); }
        #chat-badge {
            position: absolute; top: -2px; right: -2px;
            width: 20px; height: 20px; border-radius: 50%;
            background: var(--green); border: 2px solid var(--cream);
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; color: #fff; font-weight: 700;
        }

        /* ===== CHAT PANEL ===== */
        #chat-panel {
            position: fixed; bottom: 108px; right: 32px; z-index: 998;
            width: 360px; height: 530px;
            background: var(--espresso);
            border-radius: 24px; border: 1px solid rgba(200,151,58,.15);
            box-shadow: 0 24px 60px rgba(0,0,0,.5);
            display: flex; flex-direction: column; overflow: hidden;
            transform: scale(0.85) translateY(20px); opacity: 0; pointer-events: none;
            transform-origin: bottom right;
            transition: transform .3s cubic-bezier(.34,1.56,.64,1), opacity .2s;
        }
        #chat-panel.open { transform: scale(1) translateY(0); opacity: 1; pointer-events: all; }

        .cp-header {
            padding: 16px 18px;
            background: rgba(200,151,58,.06);
            border-bottom: 1px solid rgba(200,151,58,.08);
            display: flex; align-items: center; gap: 12px;
        }
        .cp-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--accent));
            display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;
        }
        .cp-name { color: var(--cream); font-size: 14px; font-weight: 600; letter-spacing: .3px; }
        .cp-status { color: rgba(250,240,220,.4); font-size: 11px; display: flex; align-items: center; gap: 4px; }
        .cp-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); display: inline-block; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
        .cp-close {
            margin-left: auto; background: rgba(200,151,58,.08); border: none;
            color: rgba(250,240,220,.5); width: 28px; height: 28px; border-radius: 50%;
            cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center;
            transition: all .2s;
        }
        .cp-close:hover { background: rgba(200,151,58,.2); color: var(--cream); }

        .cp-messages {
            flex: 1; overflow-y: auto; padding: 14px;
            display: flex; flex-direction: column; gap: 10px;
            scroll-behavior: smooth;
        }
        .cp-messages::-webkit-scrollbar { width: 3px; }
        .cp-messages::-webkit-scrollbar-thumb { background: rgba(200,151,58,.2); border-radius: 2px; }

        .cp-msg { display: flex; gap: 8px; align-items: flex-end; animation: msgIn .25s ease; }
        @keyframes msgIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }
        .cp-msg.user { flex-direction: row-reverse; }
        .msg-avatar { width: 28px; height: 28px; border-radius: 50%; background: rgba(200,151,58,.12); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
        .msg-content {
            max-width: 78%; padding: 10px 14px; border-radius: 16px;
            font-size: 13px; line-height: 1.55; word-break: break-word;
        }
        .cp-msg.assistant .msg-content {
            background: rgba(255,255,255,.06); color: var(--cream);
            border: 0.5px solid rgba(200,151,58,.1); border-bottom-left-radius: 3px;
        }
        .cp-msg.user .msg-content {
            background: linear-gradient(135deg, var(--gold), var(--accent));
            color: var(--espresso); font-weight: 500; border-bottom-right-radius: 3px;
        }
        .cp-msg.loading .msg-content { color: rgba(250,240,220,.4); font-style: italic; }

        /* Typing indicator */
        #cp-typing { display: none; padding: 8px 14px; }
        .typing-bubble {
            display: flex; gap: 4px; align-items: center;
            background: rgba(255,255,255,.06); border: 0.5px solid rgba(200,151,58,.1);
            padding: 10px 14px; border-radius: 16px; border-bottom-left-radius: 3px;
            width: fit-content;
        }
        .typing-bubble span {
            width: 6px; height: 6px; background: rgba(200,151,58,.5);
            border-radius: 50%; animation: typing 1.2s infinite;
        }
        .typing-bubble span:nth-child(2) { animation-delay: .2s; }
        .typing-bubble span:nth-child(3) { animation-delay: .4s; }
        @keyframes typing { 0%,60%,100%{transform:translateY(0);opacity:.4} 30%{transform:translateY(-5px);opacity:1} }

        /* Quick replies */
        .cp-quick {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding: 8px 14px 4px; background: rgba(0,0,0,.15);
        }
        .cp-qr {
            background: rgba(200,151,58,.08); border: 1px solid rgba(200,151,58,.18);
            color: rgba(250,240,220,.75); border-radius: 14px;
            padding: 5px 12px; font-size: 11px; cursor: pointer;
            font-family: 'Outfit', sans-serif; transition: all .15s; letter-spacing: .2px;
        }
        .cp-qr:hover { background: var(--gold); color: var(--espresso); border-color: var(--gold); }

        /* Input */
        .cp-input-wrap {
            display: flex; gap: 8px; padding: 12px 14px;
            background: rgba(0,0,0,.2); border-top: 1px solid rgba(200,151,58,.06);
        }
        .cp-input {
            flex: 1; background: rgba(255,255,255,.06); border: 1px solid rgba(200,151,58,.15);
            border-radius: 20px; padding: 9px 14px; font-size: 13px; outline: none;
            color: var(--cream); font-family: 'Outfit', sans-serif;
        }
        .cp-input::placeholder { color: rgba(250,240,220,.25); }
        .cp-input:focus { border-color: rgba(200,151,58,.35); }
        .cp-send {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), var(--accent));
            border: none; cursor: pointer; font-size: 14px;
            display: flex; align-items: center; justify-content: center;
            transition: all .2s; flex-shrink: 0;
        }
        .cp-send:hover { transform: scale(1.1); }
        .cp-send:disabled { opacity: .4; cursor: not-allowed; transform: none; }

        /* Chat badges */
        .chat-badge-pending    { background: rgba(251,191,36,.15); color: #fbbf24; padding: 1px 8px; border-radius: 8px; font-size: 11px; font-weight: 600; }
        .chat-badge-processing { background: rgba(96,165,250,.15); color: #60a5fa; padding: 1px 8px; border-radius: 8px; font-size: 11px; font-weight: 600; }
        .chat-badge-done       { background: rgba(52,211,153,.15); color: #34d399; padding: 1px 8px; border-radius: 8px; font-size: 11px; font-weight: 600; }
        .chat-badge-cancelled  { background: rgba(248,113,113,.15); color: #f87171; padding: 1px 8px; border-radius: 8px; font-size: 11px; font-weight: 600; }

        /* ===== FOOTER ===== */
        .site-footer {
            background: var(--brown); padding: 28px 48px;
            display: flex; align-items: center; justify-content: space-between;
            border-top: 1px solid rgba(200,151,58,.1); margin-top: 0;
        }
        .footer-brand { font-family: 'Cormorant Garamond', serif; font-size: 18px; color: var(--cream); }
        .footer-copy { font-size: 11px; color: rgba(138,112,96,.5); margin-top: 3px; letter-spacing: .3px; }
        .footer-links a { color: var(--muted); font-size: 13px; margin-left: 20px; transition: color .2s; }
        .footer-links a:hover { color: var(--gold); }

        /* ===== UTILITIES ===== */
        .sec-tag {
            display: inline-block; background: rgba(200,151,58,.1); color: var(--gold);
            font-size: 11px; padding: 4px 14px; border-radius: 10px;
            margin-bottom: 12px; font-weight: 500; letter-spacing: .5px;
        }
        .sec-title { font-size: 38px; color: var(--espresso); margin-bottom: 8px; line-height: 1.1; }
        .sec-sub { font-size: 14px; color: var(--muted); margin-bottom: 36px; font-weight: 300; }
        .btn-primary {
            background: var(--gold); color: var(--espresso);
            border: none; padding: 13px 30px; border-radius: 24px;
            font-size: 14px; font-weight: 600; cursor: pointer;
            font-family: 'Outfit', sans-serif; transition: all .25s; letter-spacing: .3px;
        }
        .btn-primary:hover { background: var(--gold-lt); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(200,151,58,.3); }
        .btn-outline {
            background: transparent; border: 1.5px solid var(--espresso);
            color: var(--espresso); padding: 12px 28px; border-radius: 24px;
            font-size: 14px; cursor: pointer; font-family: 'Outfit', sans-serif;
            font-weight: 500; transition: all .2s;
        }
        .btn-outline:hover { background: var(--espresso); color: var(--cream); }
    </style>
    @stack('styles')
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="{{ route('home') }}" class="nav-brand">☕ <span>Brew</span>Nest</a>
    <div class="nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('menu.index') }}" class="{{ request()->routeIs('menu.*') ? 'active' : '' }}">Menu</a>
        @auth
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">📋 Orders</a>
        @endauth
        <button class="nav-cta" onclick="toggleChat()">💬 Chat Karen</button>
    </div>
</nav>

@yield('content')

<!-- FOOTER -->
<footer class="site-footer">
    <div>
        <div class="footer-brand">☕ BrewNest</div>
        <div class="footer-copy">2025 · POWERED BY AGENTIC AI</div>
    </div>
    <div class="footer-links">
        <a href="{{ route('home') }}">Beranda</a>
        <a href="{{ route('menu.index') }}">Menu</a>
        <a href="#" onclick="toggleChat(); return false;">Chat Karen</a>
    </div>
</footer>

<!-- FLOATING CHAT -->
<button id="chat-fab" onclick="toggleChat()" aria-label="Chat dengan Karen">
    <span id="chat-fab-icon">💬</span>
    <div id="chat-badge">1</div>
</button>

<!-- CHAT PANEL -->
<div id="chat-panel">
    <div class="cp-header">
        <div class="cp-avatar">☕</div>
        <div>
            <div class="cp-name">Karen</div>
            <div class="cp-status"><span class="cp-dot"></span> Online · Siap melayani</div>
        </div>
        <button class="cp-close" onclick="toggleChat()">×</button>
    </div>
    <div id="cp-typing"><div class="typing-bubble"><span></span><span></span><span></span></div></div>
    <div class="cp-messages" id="cpMessages">
        <div class="cp-msg assistant">
            <div class="msg-avatar">☕</div>
            <div class="msg-content">Halo! Saya Karen 👋<br>Selamat datang di BrewNest.<br>Mau lihat menu, rekomendasi, atau langsung pesan?</div>
        </div>
    </div>
    <div class="cp-quick" id="cpQuick">
        <button class="cp-qr" onclick="sendChat('Lihat semua menu')">📋 Menu</button>
        <button class="cp-qr" onclick="sendChat('Rekomendasikan minuman untuk saya')">✨ Rekomendasi</button>
        <button class="cp-qr" onclick="sendChat('Saya mau pesan')">🛒 Pesan</button>
        <button class="cp-qr" onclick="sendChat('Saya ingin cek riwayat pesanan saya')">📦 Cek Pesanan</button>
        <button class="cp-qr" onclick="sendChat('Jam berapa BrewNest buka hari ini?')">🕐 Jam Buka</button>
    </div>
    <div class="cp-input-wrap">
        <input class="cp-input" id="cpInput" type="text" placeholder="Ketik pesan..."
               autocomplete="off" onkeydown="if(event.key==='Enter') sendChat()"/>
        <button class="cp-send" id="cpSend" onclick="sendChat()">➤</button>
    </div>
</div>

<script>
const cpMessages = document.getElementById('cpMessages');
const cpInput    = document.getElementById('cpInput');
const cpSend     = document.getElementById('cpSend');
const cpQuick    = document.getElementById('cpQuick');
const chatPanel  = document.getElementById('chat-panel');
const chatBadge  = document.getElementById('chat-badge');
const fabIcon    = document.getElementById('chat-fab-icon');
const cpTyping   = document.getElementById('cp-typing');
let isOpen = false, retryCount = 0;

const qrSets = {
    default:     [
        { label:'📋 Menu',              msg:'Lihat semua menu' },
        { label:'✨ Rekomendasi',        msg:'Rekomendasikan minuman untuk saya' },
        { label:'🛒 Pesan',             msg:'Saya mau pesan' },
        { label:'📦 Cek Pesanan',       msg:'Saya ingin cek riwayat pesanan saya' },
        { label:'🕐 Jam Buka',          msg:'Jam berapa BrewNest buka hari ini?' },
    ],
    after_menu:  [
        { label:'🛒 Mau Pesan',         msg:'Saya mau pesan' },
        { label:'💰 Budget 25rb',       msg:'Ada menu di bawah 25 ribu?' },
        { label:'☕ Espresso',          msg:'Tampilkan menu espresso' },
        { label:'🍵 Non Coffee',        msg:'Tampilkan menu non coffee' },
    ],
    confirm:     [
        { label:'✅ Ya, benar',          msg:'Ya, benar' },
        { label:'✏️ Ubah',              msg:'Saya ingin mengubah pesanan' },
        { label:'❌ Batal',              msg:'Batal, tidak jadi pesan' },
    ],
    after_order: [
        { label:'⭐ Beri Rating',        msg:'Saya ingin memberi rating pesanan saya' },
        { label:'📦 Cek Status',        msg:'Saya ingin cek riwayat pesanan saya' },
        { label:'📋 Menu Lagi',         msg:'Lihat semua menu' },
        { label:'➕ Tambah',            msg:'Saya mau tambah pesanan' },
    ],
    rating:      [
        { label:'⭐⭐⭐⭐⭐ 5 Bintang',  msg:'Saya beri rating 5 bintang, sangat puas!' },
        { label:'⭐⭐⭐⭐ 4 Bintang',   msg:'Saya beri rating 4 bintang, puas' },
        { label:'⭐⭐⭐ 3 Bintang',    msg:'Saya beri rating 3 bintang, cukup' },
    ],
    jam:         [
        { label:'📅 Jadwal Seminggu',   msg:'Tampilkan jadwal buka seminggu' },
        { label:'🛒 Pesan Sekarang',    msg:'Saya mau pesan' },
        { label:'📋 Lihat Menu',        msg:'Lihat semua menu' },
    ],
    name: [],
};

function detectQR(text) {
    const t = text.toLowerCase();
    if (t.includes('nama') && (t.includes('anda') || t.includes('kamu') || t.includes('boleh') || t.includes('siapa'))) return 'name';
    if (t.includes('konfirmasi') || t.includes('sudah benar') || t.includes('apakah benar')) return 'confirm';
    if (t.includes('berhasil') || t.includes('ord-') || t.includes('pesanan berhasil')) return 'after_order';
    if (t.includes('rating') || t.includes('bintang') || t.includes('ketik bintang')) return 'rating';
    if (t.includes('riwayat') || t.includes('status pesanan')) return 'after_order';
    if (t.includes('jam') || t.includes('buka') || t.includes('tutup') || t.includes('operasional')) return 'jam';
    if (t.includes('menu') || t.includes('tersedia') || t.includes('daftar') || t.includes('berikut')) return 'after_menu';
    return 'default';
}

function setQR(type) {
    const set = qrSets[type] || qrSets.default;
    if (!set.length) { cpQuick.innerHTML = ''; return; }
    cpQuick.innerHTML = set.map(b =>
        `<button class="cp-qr" onclick="sendChat('${b.msg}')">${b.label}</button>`
    ).join('');
}

function getTime() {
    return new Date().toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
}

function formatText(text) {
    return text
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>')
        .replace(/(ORD-[\w-]+)/g,'<strong style="color:var(--gold-lt)">$1</strong>')
        .replace(/(💰 Total: Rp[\d.,]+)/g,'<strong>$1</strong>')
        .replace(/⏳ Menunggu/g,'<span class="chat-badge-pending">⏳ Menunggu</span>')
        .replace(/🔄 Diproses/g,'<span class="chat-badge-processing">🔄 Diproses</span>')
        .replace(/✅ Selesai/g,'<span class="chat-badge-done">✅ Selesai</span>')
        .replace(/❌ Dibatalkan/g,'<span class="chat-badge-cancelled">❌ Dibatalkan</span>')
        .replace(/\n/g,'<br>');
}

function toggleChat() {
    isOpen = !isOpen;
    chatPanel.classList.toggle('open', isOpen);
    chatBadge.style.display = 'none';
    fabIcon.textContent = isOpen ? '✕' : '💬';
    if (isOpen) setTimeout(() => cpInput.focus(), 300);
}

function openChatWith(msg) {
    if (!isOpen) { isOpen = true; chatPanel.classList.add('open'); chatBadge.style.display = 'none'; fabIcon.textContent = '✕'; }
    setTimeout(() => sendChat(msg), 400);
}

function appendMsg(role, text, isError = false) {
    const wrapper = document.createElement('div');
    wrapper.className = `cp-msg ${role}${isError ? ' error' : ''}`;
    const av = document.createElement('div');
    av.className = 'msg-avatar';
    av.textContent = role === 'user' ? '👤' : '☕';
    const content = document.createElement('div');
    content.className = 'msg-content';
    content.innerHTML = role === 'assistant' ? formatText(text) : text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    if (role === 'user') { wrapper.appendChild(content); wrapper.appendChild(av); }
    else { wrapper.appendChild(av); wrapper.appendChild(content); }
    cpMessages.appendChild(wrapper);
    const time = document.createElement('div');
    time.style.cssText = `font-size:10px;color:rgba(200,151,58,.35);padding:2px ${role==='user'?'42px 4px 0':'0 4px 0 42px'};text-align:${role==='user'?'right':'left'}`;
    time.textContent = getTime();
    cpMessages.appendChild(time);
    cpMessages.scrollTop = cpMessages.scrollHeight;
    return wrapper;
}

function showTyping() { cpTyping.style.display = 'block'; cpMessages.scrollTop = cpMessages.scrollHeight; }
function hideTyping() { cpTyping.style.display = 'none'; }

async function sendChat(text = null) {
    const msg = text ?? cpInput.value.trim();
    if (!msg || cpSend.disabled) return;
    appendMsg('user', msg);
    cpInput.value = ''; cpSend.disabled = true; cpQuick.innerHTML = '';
    showTyping();
    try {
        const res = await fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ message: msg }),
        });
        hideTyping();
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        retryCount = 0;
        appendMsg('assistant', data.reply || 'Maaf, tidak ada respons.');
        setQR(detectQR(data.reply || ''));
    } catch (err) {
        hideTyping(); retryCount++;
        if (retryCount <= 1) {
            appendMsg('assistant', '⚠️ Mencoba menghubungi Karen...');
            setTimeout(() => { cpMessages.lastElementChild?.remove(); cpMessages.lastElementChild?.remove(); sendChat(msg); }, 2000);
        } else {
            retryCount = 0;
            appendMsg('assistant', '❌ Gagal terhubung. Silakan coba lagi.', true);
            setQR('default');
        }
    } finally { cpSend.disabled = false; cpInput.focus(); }
}
</script>

@stack('scripts')
</body>
</html>