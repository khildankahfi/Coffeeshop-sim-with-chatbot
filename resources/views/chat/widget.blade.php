<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Karen — AI Coffeeshop Assistant</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #faf7f2; display: flex; justify-content: center; align-items: center; min-height: 100vh; }

        .chat-box { width: 420px; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.10); display: flex; flex-direction: column; height: 620px; }

        .chat-header { background: #3d1f0a; color: #fff; padding: 16px 20px; border-radius: 16px 16px 0 0; display: flex; align-items: center; gap: 10px; }
        .chat-header .avatar { width: 36px; height: 36px; background: #c8a97e; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .chat-header .info .name { font-weight: 600; font-size: 15px; }
        .chat-header .info .status { font-size: 11px; opacity: 0.7; }

        .chat-messages { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 10px; }

        .msg { max-width: 82%; padding: 10px 14px; border-radius: 12px; font-size: 14px; line-height: 1.5; }
        .msg.user      { background: #3d1f0a; color: #fff; align-self: flex-end; border-bottom-right-radius: 3px; }
        .msg.assistant { background: #f0ebe4; color: #1a1a1a; align-self: flex-start; border-bottom-left-radius: 3px; }
        .msg.loading   { color: #aaa; font-style: italic; background: #f0ebe4; align-self: flex-start; }

        /* Quick reply buttons */
        .quick-replies { display: flex; flex-wrap: wrap; gap: 6px; padding: 4px 16px 8px; }
        .qr-btn {
            background: #fff;
            border: 1.5px solid #3d1f0a;
            color: #3d1f0a;
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 13px;
            cursor: pointer;
            transition: all .15s;
        }
        .qr-btn:hover { background: #3d1f0a; color: #fff; }

        .chat-input { display: flex; gap: 8px; padding: 12px 16px; border-top: 1px solid #eee; }
        .chat-input input { flex: 1; border: 1px solid #ddd; border-radius: 8px; padding: 10px 12px; font-size: 14px; outline: none; }
        .chat-input input:focus { border-color: #3d1f0a; }
        .chat-input button { background: #3d1f0a; color: #fff; border: none; border-radius: 8px; padding: 10px 16px; cursor: pointer; font-size: 14px; }
        .chat-input button:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>
<div class="chat-box">
    <div class="chat-header">
        <div class="avatar">☕</div>
        <div class="info">
            <div class="name">Karen</div>
            <div class="status">Asisten AI Coffeeshop</div>
        </div>
    </div>

    <div class="chat-messages" id="messages">
        <div class="msg assistant">Halo! Saya Karen 👋 Selamat datang di coffeeshop kami. Ada yang bisa saya bantu?</div>
    </div>

    <!-- Quick reply buttons -->
    <div class="quick-replies" id="quickReplies">
        <button class="qr-btn" onclick="sendMessage('Lihat semua menu')">📋 Lihat Menu</button>
        <button class="qr-btn" onclick="sendMessage('Rekomendasikan minuman untuk saya')">✨ Rekomendasi</button>
        <button class="qr-btn" onclick="sendMessage('Saya mau pesan')">🛒 Pesan</button>
        <button class="qr-btn" onclick="sendMessage('Berapa total penjualan hari ini?')">📊 Laporan</button>
    </div>

    <div class="chat-input">
        <input type="text" id="userInput" placeholder="Ketik pesan..." autocomplete="off"/>
        <button id="sendBtn" onclick="sendMessage()">Kirim</button>
    </div>
</div>

<script>
const messagesEl    = document.getElementById('messages');
const inputEl       = document.getElementById('userInput');
const sendBtn       = document.getElementById('sendBtn');
const quickRepliesEl = document.getElementById('quickReplies');

// Kirim dengan Enter
inputEl.addEventListener('keydown', e => { if (e.key === 'Enter') sendMessage(); });

// Quick reply berdasarkan konteks
const quickReplySets = {
    default: [
        { label: '📋 Lihat Menu',    msg: 'Lihat semua menu' },
        { label: '✨ Rekomendasi',   msg: 'Rekomendasikan minuman untuk saya' },
        { label: '🛒 Pesan',         msg: 'Saya mau pesan' },
        { label: '📊 Laporan',       msg: 'Berapa total penjualan hari ini?' },
    ],
    order_confirm: [
        { label: '✅ Ya, benar',     msg: 'Ya, benar' },
        { label: '❌ Batal',         msg: 'Batal, saya tidak jadi pesan' },
    ],
    after_menu: [
        { label: '🛒 Mau Pesan',     msg: 'Saya mau pesan' },
        { label: '✨ Rekomendasi',   msg: 'Ada rekomendasi?' },
        { label: '🏠 Menu Utama',    msg: 'Kembali ke menu utama' },
    ],
    greeting: [
        { label: '✅ Iya',           msg: 'Iya' },
        { label: '❌ Tidak',         msg: 'Tidak' },
    ],
};

function setQuickReplies(type = 'default') {
    const set = quickReplySets[type] || quickReplySets.default;
    quickRepliesEl.innerHTML = set.map(btn =>
        `<button class="qr-btn" onclick="sendMessage('${btn.msg}')">${btn.label}</button>`
    ).join('');
}

function detectContext(text) {
    const t = text.toLowerCase();
    if (t.includes('nama') || t.includes('konfirmasi') || t.includes('yakin') || t.includes('sudah benar')) {
        return 'order_confirm';
    }
    if (t.includes('menu') || t.includes('tersedia') || t.includes('daftar')) {
        return 'after_menu';
    }
    if (t.includes('apa') && t.includes('bantu')) {
        return 'greeting';
    }
    return 'default';
}

async function sendMessage(text = null) {
    const msg = text ?? inputEl.value.trim();
    if (!msg) return;

    appendMessage('user', msg);
    inputEl.value   = '';
    sendBtn.disabled = true;
    quickRepliesEl.innerHTML = ''; // sembunyikan quick reply saat loading

    const loadingEl = appendMessage('loading', '...');

    try {
        const res = await fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ message: msg }),
        });

        const data = await res.json();
        loadingEl.remove();

        const reply = data.reply || 'Maaf, tidak ada respons.';
        appendMessage('assistant', reply);

        // Update quick reply berdasarkan konteks jawaban Karen
        setQuickReplies(detectContext(reply));

    } catch (err) {
        loadingEl.remove();
        appendMessage('assistant', 'Maaf, terjadi gangguan koneksi. Silakan coba lagi.');
        setQuickReplies('default');
    } finally {
        sendBtn.disabled = false;
        inputEl.focus();
    }
}

function appendMessage(role, text) {
    const div = document.createElement('div');
    div.className = `msg ${role}`;
    div.textContent = text;
    messagesEl.appendChild(div);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return div;
}
</script>
</body>
</html>