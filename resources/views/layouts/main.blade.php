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
        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-links a {
            color: var(--brown-light); font-size: 13px; font-weight: 500;
            padding: 6px 14px; border-radius: 20px; transition: all .2s;
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
        .cp-dot { width: 6px; height: 6px; background: var(--green); border-radius: 50%; display: inline-block; }
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
        @keyframes msgIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }
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

        /* ===== TYPING INDICATOR ===== */
        .typing-bubble {
            display: flex; gap: 4px; align-items: center;
            padding: 8px 12px; background: #fff;
            border-radius: 14px; border-bottom-left-radius: 3px;
            border: 0.5px solid #e8d8c4;
            width: fit-content;
        }
        .typing-bubble span {
            width: 7px; height: 7px; background: var(--brown-light);
            border-radius: 50%; animation: typing 1.2s infinite;
        }
        .typing-bubble span:nth-child(2) { animation-delay: .2s; }
        .typing-bubble span:nth-child(3) { animation-delay: .4s; }
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); opacity: .4; }
            30% { transform: translateY(-6px); opacity: 1; }
        }

        /* ===== PESAN ===== */
        .cp-msg {
            display: flex; gap: 8px; align-items: flex-start;
            animation: msgIn .25s ease;
            max-width: 100%;
        }
        @keyframes msgIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }

        .cp-msg.user {
            flex-direction: row-reverse;
        }
        .msg-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: var(--cream-mid); display: flex;
            align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0; margin-top: 2px;
        }
        .msg-content {
            max-width: 78%; padding: 10px 14px;
            border-radius: 16px; font-size: 13px; line-height: 1.6;
            word-break: break-word;
        }
        .cp-msg.assistant .msg-content {
            background: #fff; color: var(--text-dark);
            border-bottom-left-radius: 3px;
            border: 0.5px solid #e8d8c4;
        }
        .cp-msg.user .msg-content {
            background: var(--brown-main); color: var(--cream);
            border-bottom-right-radius: 3px;
        }
        .cp-msg.user .msg-avatar {
            background: var(--brown-light); color: var(--brown-main);
            font-size: 12px; font-weight: 700;
        }

        /* Timestamp */
        .msg-time {
            font-size: 10px; color: var(--text-muted);
            margin-top: 4px; text-align: right;
        }
        .cp-msg.assistant .msg-time { text-align: left; padding-left: 36px; }
        .cp-msg.user .msg-time { text-align: right; padding-right: 36px; }

        /* Error message */
        .cp-msg.error .msg-content {
            background: #fee2e2; color: #991b1b;
            border: 0.5px solid #fecaca;
        }

        /* Tambahan */
        .chat-badge-pending    { background:#fef3c7; color:#92400e; padding:1px 8px; border-radius:8px; font-size:11px; font-weight:600; }
        .chat-badge-processing { background:#dbeafe; color:#1e40af; padding:1px 8px; border-radius:8px; font-size:11px; font-weight:600; }
        .chat-badge-done       { background:#d1fae5; color:#065f46; padding:1px 8px; border-radius:8px; font-size:11px; font-weight:600; }
        .chat-badge-cancelled  { background:#fee2e2; color:#991b1b; padding:1px 8px; border-radius:8px; font-size:11px; font-weight:600; }
    </style>
    @stack('styles')
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="{{ route('home') }}" class="nav-brand">☕ BrewNest</a>
    <div class="nav-links">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('menu.index') }}" class="{{ request()->routeIs('menu.*') ? 'active' : '' }}">Menu</a>
        @auth
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">📋 Orders</a>
        @endauth
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
        <div style="flex:1">
            <div class="cp-name">Karen</div>
            <div class="cp-status"><span class="cp-dot"></span> Online · Siap melayani</div>
        </div>
        <button class="cp-close" onclick="toggleChat()" aria-label="Tutup">×</button>
    </div>

    <!-- Typing indicator -->
    <div id="cp-typing" style="display:none; padding:8px 14px; background:var(--cream-light);">
        <div class="typing-bubble">
            <span></span><span></span><span></span>
        </div>
    </div>

    <div class="cp-messages" id="cpMessages">
        <div class="cp-msg assistant">
            <div class="msg-avatar">☕</div>
            <div class="msg-content">
                Halo! Saya <strong>Karen</strong> 👋<br>
                Selamat datang di BrewNest. Saya bisa bantu kamu:<br><br>
                ☕ Lihat menu & harga<br>
                ✨ Rekomendasi menu<br>
                🛒 Proses pesanan
            </div>
        </div>
    </div>

    <div class="cp-quick" id="cpQuick">
        <button class="cp-qr" onclick="sendChat('Lihat semua menu')">📋 Menu</button>
        <button class="cp-qr" onclick="sendChat('Rekomendasikan minuman untuk saya')">✨ Rekomendasi</button>
        <button class="cp-qr" onclick="sendChat('Saya mau pesan')">🛒 Pesan</button>
    </div>

    <div class="cp-input-wrap">
        <input class="cp-input" id="cpInput" type="text"
            placeholder="Ketik pesan..."
            autocomplete="off"
            onkeydown="if(event.key==='Enter') sendChat()"/>
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
const cpTyping   = document.getElementById('cp-typing');
let isOpen = false;
let retryCount = 0;

const qrSets = {
    default: [
        { label: '📋 Lihat Menu',       msg: 'Lihat semua menu' },
        { label: '✨ Rekomendasi',       msg: 'Rekomendasikan minuman untuk saya' },
        { label: '🛒 Saya mau pesan',   msg: 'Saya mau pesan' },
        { label: '📦 Cek Pesanan',      msg: 'Saya ingin cek riwayat pesanan saya' },
    ],
    after_menu: [
        { label: '🛒 Mau Pesan',        msg: 'Saya mau pesan' },
        { label: '💰 Budget 25rb',      msg: 'Ada menu di bawah 25 ribu?' },
        { label: '☕ Espresso',         msg: 'Tampilkan menu espresso' },
        { label: '🍵 Non Coffee',       msg: 'Tampilkan menu non coffee' },
    ],
    confirm: [
        { label: '✅ Ya, benar',         msg: 'Ya, benar' },
        { label: '✏️ Ubah',             msg: 'Saya ingin mengubah pesanan' },
        { label: '❌ Batal',             msg: 'Batal, tidak jadi pesan' },
    ],
    after_order: [
        { label: '⭐ Beri Rating',      msg: 'Saya ingin memberi rating pesanan saya' },
        { label: '📦 Cek Status',       msg: 'Saya ingin cek riwayat pesanan saya' },
        { label: '📋 Lihat Menu',       msg: 'Lihat semua menu' },
        { label: '➕ Tambah Pesanan',   msg: 'Saya mau tambah pesanan' },
    ],
    rating: [
        { label: '⭐⭐⭐⭐⭐ 5 Bintang', msg: 'Saya beri rating 5 bintang, sangat puas!' },
        { label: '⭐⭐⭐⭐ 4 Bintang',  msg: 'Saya beri rating 4 bintang, puas' },
        { label: '⭐⭐⭐ 3 Bintang',   msg: 'Saya beri rating 3 bintang, cukup' },
    ],
    name: [],
};

function detectQR(text) {
    const t = text.toLowerCase();
    if (t.includes('nama') && (t.includes('anda') || t.includes('kamu') || t.includes('boleh') || t.includes('siapa'))) return 'name';
    if (t.includes('konfirmasi') || t.includes('sudah benar') || t.includes('apakah benar')) return 'confirm';
    if (t.includes('berhasil') || t.includes('ord-') || t.includes('pesanan berhasil')) return 'after_order';
    if (t.includes('rating') || t.includes('bintang') || t.includes('ulasan') || t.includes('ketik bintang')) return 'rating';
    if (t.includes('riwayat') || t.includes('status pesanan')) return 'after_order';
    if (t.includes('jam') || t.includes('buka') || t.includes('tutup')) return 'jam';
    if (t.includes('menu') || t.includes('tersedia') || t.includes('daftar')) return 'after_menu';
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
    return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

// Format teks Karen jadi HTML yang rapi
function formatText(text) {
    return text
        // Escape HTML dulu
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        // Bold **teks**
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        // Kode order bold
        .replace(/(ORD-[\w-]+)/g, '<strong style="color:#3d1f0a">$1</strong>')
        // Total bold + warna
        .replace(/(💰 Total: Rp[\d.,]+)/g, '<strong>$1</strong>')
        // Status badges
        .replace(/⏳ Menunggu/g, '<span class="chat-badge-pending">⏳ Menunggu</span>')
        .replace(/🔄 Diproses/g, '<span class="chat-badge-processing">🔄 Diproses</span>')
        .replace(/✅ Selesai/g, '<span class="chat-badge-done">✅ Selesai</span>')
        .replace(/❌ Dibatalkan/g, '<span class="chat-badge-cancelled">❌ Dibatalkan</span>')
        // Newline jadi <br>
        .replace(/\n/g, '<br>');
}

function toggleChat() {
    isOpen = !isOpen;
    chatPanel.classList.toggle('open', isOpen);
    chatBadge.style.display = 'none';
    fabIcon.textContent = isOpen ? '✕' : '💬';
    if (isOpen) setTimeout(() => cpInput.focus(), 300);
}

function openChatWith(msg) {
    if (!isOpen) {
        isOpen = true;
        chatPanel.classList.add('open');
        chatBadge.style.display = 'none';
        fabIcon.textContent = '✕';
    }
    setTimeout(() => sendChat(msg), 400);
}

function appendMsg(role, text, isError = false) {
    const wrapper = document.createElement('div');
    wrapper.className = `cp-msg ${role}${isError ? ' error' : ''}`;

    const avatarEl = document.createElement('div');
    avatarEl.className = 'msg-avatar';
    avatarEl.textContent = role === 'user' ? '👤' : '☕';

    const contentEl = document.createElement('div');
    contentEl.className = 'msg-content';
    contentEl.innerHTML = role === 'assistant' ? formatText(text) : text;

    if (role === 'user') {
        wrapper.appendChild(contentEl);
        wrapper.appendChild(avatarEl);
    } else {
        wrapper.appendChild(avatarEl);
        wrapper.appendChild(contentEl);
    }

    cpMessages.appendChild(wrapper);

    // Timestamp
    const timeEl = document.createElement('div');
    timeEl.style.cssText = `font-size:10px;color:#a08060;padding:2px ${role === 'user' ? '42px 6px 0' : '0 6px 0 42px'};text-align:${role === 'user' ? 'right' : 'left'}`;
    timeEl.textContent = getTime();
    cpMessages.appendChild(timeEl);

    cpMessages.scrollTop = cpMessages.scrollHeight;
    return wrapper;
}

function showTyping() {
    cpTyping.style.display = 'block';
    cpMessages.scrollTop = cpMessages.scrollHeight;
}

function hideTyping() {
    cpTyping.style.display = 'none';
}

async function sendChat(text = null) {
    const msg = text ?? cpInput.value.trim();
    if (!msg || cpSend.disabled) return;

    appendMsg('user', msg);
    cpInput.value = '';
    cpSend.disabled = true;
    cpQuick.innerHTML = '';
    showTyping();

    try {
        const res = await fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ message: msg }),
        });

        hideTyping();
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const data = await res.json();
        const reply = data.reply || 'Maaf, tidak ada respons.';

        retryCount = 0;
        appendMsg('assistant', reply);
        setQR(detectQR(reply));

    } catch (err) {
        hideTyping();
        retryCount++;

        if (retryCount <= 1) {
            appendMsg('assistant', '⚠️ Mencoba menghubungi Karen...');
            setTimeout(() => {
                cpMessages.lastElementChild?.remove();
                cpMessages.lastElementChild?.remove();
                sendChat(msg);
            }, 2000);
        } else {
            retryCount = 0;
            appendMsg('assistant', '❌ Gagal terhubung. Silakan coba lagi.', true);
            setQR('default');
        }
    } finally {
        cpSend.disabled = false;
        cpInput.focus();
    }
}
</script>

@stack('scripts')
</body>
</html>