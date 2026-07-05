# ☕ CoffeeShop POS System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

> Sistem Point-of-Sale (POS) berbasis web untuk kedai kopi, dilengkapi dengan fitur manajemen menu, pencatatan pesanan, laporan penjualan, dan **AI Chatbot** berbasis Groq API untuk membantu pelanggan dan kasir.

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|---|---|
| 🧾 **Kasir / POS** | Input pesanan pelanggan secara real-time dengan kalkulasi total otomatis |
| 📋 **Manajemen Menu** | CRUD produk dan kategori dengan status ketersediaan |
| 📊 **Laporan Penjualan** | Dashboard statistik dengan grafik revenue harian, top menu terlaris, dan distribusi status pesanan |
| 🤖 **AI Chatbot** | Asisten virtual berbasis Groq LLM untuk membantu pelanggan memilih menu atau tanya-jawab |
| ⭐ **Feedback Pelanggan** | Sistem rating dan ulasan dari pelanggan beserta analisis distribusi bintang |
| 🔐 **Autentikasi** | Login & registrasi menggunakan Laravel Breeze |

---

## 🏗️ Arsitektur & Tech Stack

```
coffeeshop3/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── KasirController.php       # POS / transaksi
│   │       ├── MenuController.php        # Manajemen menu & kategori
│   │       ├── OrderController.php       # Status & detail pesanan
│   │       ├── LaporanController.php     # Dashboard laporan & statistik
│   │       ├── AiChatController.php      # Endpoint AI chatbot
│   │       └── ProfileController.php     # Manajemen profil pengguna
│   ├── Models/
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── ChatLog.php
│   │   └── Feedback.php
│   └── Services/
│       └── Agent/
│           ├── AgentService.php          # Orkestrasi AI agent & tool calling
│           ├── GroqClient.php            # HTTP client ke Groq API
│           └── Tools/                   # Tool definitions untuk AI
├── database/
│   └── migrations/                      # Skema tabel: users, products, orders, dsb.
└── resources/
    └── views/                           # Blade templates
```

**Backend:** Laravel 12, PHP 8.2+, SQLite  
**Frontend:** Blade + Tailwind CSS v4, Alpine.js, Vite  
**AI Integration:** Groq API (LLM) via HTTP Client  
**Auth:** Laravel Breeze  

---

## ⚙️ Cara Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js >= 18 & npm

### Langkah Instalasi

**1. Clone repository**
```bash
git clone https://github.com/khildankahfi/Coffeeshop-sim.git
cd Coffeeshop-sim
```

**2. Install dependensi**
```bash
composer install
npm install
```

**3. Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Konfigurasi database & Groq API**

Buka file `.env` dan sesuaikan:
```env
DB_CONNECTION=sqlite

# Groq API Key untuk fitur AI Chatbot
GROQ_API_KEY=your_groq_api_key_here
```

**5. Jalankan migrasi & seeder**
```bash
php artisan migrate --seed
```

**6. Build assets**
```bash
npm run build
```

**7. Jalankan server**
```bash
php artisan serve
```

Atau gunakan perintah dev lengkap (server + queue + logs + vite) sekaligus:
```bash
composer dev
```

Aplikasi akan berjalan di `http://localhost:8000`

---

## 🗄️ Skema Database

```
users          → Data akun kasir / admin
categories     → Kategori menu (kopi, non-kopi, makanan, dll.)
products       → Produk/menu beserta harga dan ketersediaan
orders         → Transaksi pesanan pelanggan
order_items    → Detail item dalam setiap pesanan
ai_chat_logs   → Riwayat percakapan dengan AI chatbot
feedbacks      → Rating dan ulasan dari pelanggan
```

---

## 🤖 Fitur AI Chatbot

AI Chatbot ditenagai oleh **Groq API** dengan arsitektur **agentic (tool calling)**:

- Pelanggan / kasir dapat berinteraksi via chat
- AI dapat menjawab pertanyaan seputar menu, harga, dan ketersediaan produk secara dinamis
- Histori percakapan disimpan per sesi menggunakan **Laravel Cache**
- Fallback otomatis jika API tidak merespons

---

## 📊 Dashboard Laporan

Dashboard laporan menyediakan data berdasarkan filter periode:

- **Hari ini** / **7 Hari** / **30 Hari** / **Semua**
- Total revenue & jumlah pesanan
- Grafik revenue harian (line chart)
- Top 5 menu terlaris
- Distribusi status pesanan (pending / processing / done / cancelled)
- Rata-rata rating & distribusi bintang pelanggan

---

## 🧪 Testing

```bash
composer test
# atau
php artisan test
```

---

## 📄 Lisensi

Project ini dibuat sebagai proyek akhir UAS Sistem Informasi Manajemen.
