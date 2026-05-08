<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BrewNest — @yield('title', 'Specialty Coffeeshop')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --brown-dark:   #2d1508;
            --brown-main:   #3d1f0a;
            --brown-mid:    #6b3a1f;
            --brown-light:  #c8a97e;
            --cream:        #f5e6c8;
            --cream-light:  #faf5ee;
            --cream-mid:    #f0e8d8;
            --text-dark:    #1a0c04;
            --text-muted:   #8a6040;
            --accent:       #c8813e;
            --green:        #1D9E75;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream-light); color: var(--text-dark); }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }
        a { text-decoration: none; }

        /* ===== NAVBAR ===== */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: var(--brown-main);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 40px; height: 64px;
            border-bottom: 1px solid #5a3020;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 10px;
            color: var(--cream); font-family: 'Playfair Display', serif;
            font-size: 20px; font-weight: 600;
        }
        .nav-brand .logo { font-size: 22px; }
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-links a {
            color: var(--brown-light); font-size: 13px; font-weight: 500;
            padding: 6px 14px; border-radius: 20px;
            transition: all .2s;
        }
        .nav-links a:hover, .nav-links a.active {
            background: #ffffff15; color: var(--cream);
        }
        .nav-chat-btn {
            background: var(--brown-light); color: var(--brown-main);
            border: none; padding: 8px 18px; border-radius: 20px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            display: flex; align-items: center; gap: 6px;
            transition: background .2s;
        }
        .nav-chat-btn:hover { background: var(--cream); }

        /* ===== FLOATING CHAT ===== */
        #chat-fab {
            position: fixed; bottom: 28px; right: 28px; z-index: 999;
            width: 58px; height: 58px; border-radius: 50%;
            background: var(--brown-main); border: 2px solid var(--brown-light);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: transform .2s; box-shadow: 0 4px 20px rgba(61,31,10,.4);
        }
        #chat-fab:hover { transform: scale(1.1); }
        #chat-fab-icon { font-size: 24px; transition: all .2s; }
        #chat-badge {
            position: absolute; top: -3px; right: -3px;
            width: 20px; height: 20px; border-radius: 50%;
            background: var(--green); border: 2px solid var(--cream-light);
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; color: #fff; font-weight: 700;
        }

        /* ===== CHAT PANEL ===== */
        #chat-panel {
            position: fixed; bottom: 100px; right: 28px; z-index: 998;
            width: 360px; height: 530px;
            background: #fff; border-radius: 18px;
            box-shadow: 0 12px 40px rgba(61,31,10,.22);
            display: flex; flex-direction: column;
            transform: scale(0.85) translateY(20px);
            opacity: 0; pointer-events: none;
            transform-origin: bottom right;
            transition: transform .25s cubic-bezier(.34,1.56,.64,1), opacity .2s;
        }
        #chat-panel.open {
            transform: scale(1) translateY(0);
            opacity: 1; pointer-events: all;
        }
        .cp-header {
            background: var(--brown-main); padding: 14px 16px;
            border-radius: 18px 18px 0 0;
            display: flex; align-items: center; gap: 10px;
        }
        .cp-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: #c8a97e22; border: 1.5px solid #c8a97e55;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .cp-name { color: var(--cream); font-size: 14px; font-weight: 600; }
        .cp-status { color: var(--brown-light); font-size: 11px; display: flex; align-items: center; gap: 4px; }
        .cp-dot { width: 6px; height: 6px; background: var(--green); border-radius: 50%; }
        .cp-close {
            margin-left: auto; background: none; border: none;
            color: var(--brown-light); cursor: pointer; font-size: 22px;
            padding: 2px 8px; border-radius: 6px; line-height: 1;
        }
        .cp-close:hover { background: #ffffff15; color: var(--cream); }
        .cp-messages {
            flex: 1; overflow-y: auto; padding: 14px;
            display: flex; flex-direction: column; gap: 8px;
            scroll-behavior: smooth; background: var(--cream-light);
        }
        .cp-msg {
            max-width: 86%; padding: 9px 13px;
            border-radius: 14px; font-size: 13px; line-height: 1.55;
            word-break: break-word; animation: msgIn .2s ease;
        }
        @keyframes msgIn { from { opacity:0; transform: translateY(6px); } to { opacity:1; transform:none; } }
        .cp-msg.user {
            background: var(--brown-main); color: var(--cream);
            align-self: flex-end; border-bottom-right-radius: 3px;
        }
        .cp-msg.assistant {
            background: #fff; color: var(--text-dark);
            align-self: flex-start; border-bottom-left-radius: 3px;
            border: 0.5px solid #e8d8c4;
        }
        .cp-msg.loading { color: #a08060; font-style: italic; background: #fff; border: 0.5px solid #e8d8c4; align-self: flex-start; }
        .cp-quick {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding: 8px 12px 4px; background: var(--cream-light);
        }
        .cp-qr {
            background: #fff; border: 1.5px solid var(--brown-light);
            color: var(--brown-main); border-radius: 16px;
            padding: 5px 12px; font-size: 12px; cursor: pointer;
            transition: all .15s; font-family: 'DM Sans', sans-serif;
        }
        .cp-qr:hover { background: var(--brown-main); color: var(--cream); border-color: var(--brown-main); }
        .cp-input-wrap {
            display: flex; gap: 8px; padding: 10px 12px;
            border-top: 0.5px solid #e8d8c4; background: #fff;
            border-radius: 0 0 18px 18px;
        }
        .cp-input {
            flex: 1; border: 1px solid #e8d8c4; border-radius: 20px;
            padding: 8px 14px; font-size: 13px; outline: none;
            font-family: 'DM Sans', sans-serif; background: var(--cream-light);
            color: var(--text-dark);
        }
        .cp-input:focus { border-color: var(--brown-main); }
        .cp-send {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--brown-main); border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 16px; transition: opacity .15s;
        }
        .cp-send:disabled { opacity: 0.4; cursor: not-allowed; }

        /* ===== FOOTER ===== */
        .site-footer {
            background: var(--brown-dark); padding: 28px 40px;
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 60px;
        }
        .footer-brand { color: var(--cream); font-family: 'Playfair Display', serif; font-size: 16px; }
        .footer-links a { color: var(--text-muted); font-size: 13px; margin-left: 20px; }
        .footer-links a:hover { color: var(--brown-light); }
        .footer-copy { color: #5a3a22; font-size: 12px; margin-top: 4px; }
    </style>
    @stack('styles')
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="{{ route('home') }}" class="nav-brand">
        <span class="logo">☕</span> BrewNest
    </a>
    <div class="nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('menu.index') }}" class="{{ request()->routeIs('menu.*') ? 'active' : '' }}">Menu</a>
        <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">Orders</a>
        <button class="nav-chat-btn" onclick="toggleChat()">💬 Chat Karen</button>
    </div>
</nav>

@yield('content')

<!-- FOOTER -->
<footer class="site-footer">
    <div>
        <div class="footer-brand">☕ BrewNest</div>
        <div class="footer-copy">2025 · Powered by Agentic AI</div>
    </div>
    <div class="footer-links">
        <a href="{{ route('home') }}">Beranda</a>
        <a href="{{ route('menu.index') }}">Menu</a>
        <a href="#" onclick="toggleChat(); return false;">Chat Karen</a>
    </div>
</footer>

<!-- FLOATING CHAT BUTTON -->
<button id="chat-fab" onclick="toggleChat()" aria-label="Chat dengan Karen">
    <span id="chat-fab-icon">💬</span>
    <div id="chat-badge">1</div>
</button>

<!-- CHAT PANEL -->
<div id="chat-panel" role="dialog" aria-label="Chat Karen">
    <div class="cp-header">
        <div class="cp-avatar">☕</div>
        <div>
            <div class="cp-name">Karen</div>
            <div class="cp-status"><span class="cp-dot"></span> Online · Siap melayani</div>
        </div>
        <button class="cp-close" onclick="toggleChat()" aria-label="Tutup">×</button>
    </div>
    <div class="cp-messages" id="cpMessages">
        <div class="cp-msg assistant">Halo! Saya Karen 👋 Selamat datang di BrewNest. Mau lihat menu, rekomendasi, atau langsung pesan?</div>
    </div>
    <div class="cp-quick" id="cpQuick">
        <button class="cp-qr" onclick="sendChat('Lihat semua menu')">📋 Menu</button>
        <button class="cp-qr" onclick="sendChat('Rekomendasikan minuman untuk saya')">✨ Rekomendasi</button>
        <button class="cp-qr" onclick="sendChat('Saya mau pesan')">🛒 Pesan</button>
        <button class="cp-qr" onclick="sendChat('Berapa total penjualan hari ini?')">📊 Laporan</button>
    </div>
    <div class="cp-input-wrap">
        <input class="cp-input" id="cpInput" type="text" placeholder="Ketik pesan..."
               autocomplete="off" onkeydown="if(event.key==='Enter') sendChat()"/>
        <button class="cp-send" id="cpSend" onclick="sendChat()" aria-label="Kirim">➤</button>
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
let isOpen = false;

const qrSets = {
    default:     [{ label:'📋 Menu', msg:'Lihat semua menu' }, { label:'✨ Rekomendasi', msg:'Rekomendasikan minuman untuk saya' }, { label:'🛒 Pesan', msg:'Saya mau pesan' }],
    after_menu:  [{ label:'🛒 Mau Pesan', msg:'Saya mau pesan' }, { label:'💰 Budget 25rb', msg:'Ada menu di bawah 25 ribu?' }, { label:'☕ Espresso', msg:'Tampilkan menu espresso' }],
    confirm:     [{ label:'✅ Ya, benar', msg:'Ya, benar' }, { label:'✏️ Ubah', msg:'Saya ingin mengubah pesanan' }, { label:'❌ Batal', msg:'Batal' }],
    after_order: [{ label:'📋 Menu Lagi', msg:'Lihat semua menu' }, { label:'➕ Tambah', msg:'Saya mau tambah pesanan' }],
    name:        [],
};

function detectQR(text) {
    const t = text.toLowerCase();
    if (t.includes('nama') && (t.includes('anda') || t.includes('kamu'))) return 'name';
    if (t.includes('konfirmasi') || t.includes('yakin') || t.includes('sudah benar') || t.includes('apakah benar')) return 'confirm';
    if (t.includes('berhasil') || t.includes('ord-') || t.includes('pesanan') && t.includes('masuk')) return 'after_order';
    if (t.includes('menu') || t.includes('tersedia') || t.includes('daftar')) return 'after_menu';
    return 'default';
}

function setQR(type) {
    const set = qrSets[type] || qrSets.default;
    cpQuick.innerHTML = set.map(b =>
        `<button class="cp-qr" onclick="sendChat('${b.msg}')">${b.label}</button>`
    ).join('');
}

function toggleChat() {
    isOpen = !isOpen;
    chatPanel.classList.toggle('open', isOpen);
    chatBadge.style.display = 'none';
    fabIcon.textContent = isOpen ? '✕' : '💬';
    if (isOpen) cpInput.focus();
}

function openChatWith(msg) {
    if (!isOpen) { isOpen = true; chatPanel.classList.add('open'); chatBadge.style.display = 'none'; fabIcon.textContent = '✕'; }
    setTimeout(() => sendChat(msg), 300);
}

async function sendChat(text = null) {
    const msg = text ?? cpInput.value.trim();
    if (!msg) return;
    appendMsg('user', msg);
    cpInput.value = ''; cpSend.disabled = true; cpQuick.innerHTML = '';
    const loader = appendMsg('loading', 'Karen sedang mengetik...');
    try {
        const res  = await fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ message: msg }),
        });
        const data = await res.json();
        loader.remove();
        const reply = data.reply || 'Maaf, tidak ada respons.';
        appendMsg('assistant', reply);
        setQR(detectQR(reply));
    } catch {
        loader.remove();
        appendMsg('assistant', 'Maaf, terjadi gangguan. Silakan coba lagi.');
        setQR('default');
    } finally {
        cpSend.disabled = false; cpInput.focus();
    }
}

function appendMsg(role, text) {
    const div = document.createElement('div');
    div.className = `cp-msg ${role}`;
    div.textContent = text;
    cpMessages.appendChild(div);
    cpMessages.scrollTop = cpMessages.scrollHeight;
    return div;
}
</script>

@stack('scripts')
</body>
</html>