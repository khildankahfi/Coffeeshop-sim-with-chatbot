<?php

namespace App\Services\Agent;

use App\Models\ChatLog;
use App\Services\Agent\GroqClient;
use App\Services\Agent\Tools\GetMenuTool;
use App\Services\Agent\Tools\PlaceOrderTool;
use App\Services\Agent\Tools\RecommendMenuTool;
use App\Services\Agent\Tools\GetSalesSummaryTool;
use Illuminate\Support\Facades\Cache;
use App\Services\Agent\Tools\GetOrderHistoryTool;
use App\Services\Agent\Tools\GetOperationalHoursTool;
use App\Services\Agent\Tools\SubmitFeedbackTool;

class AgentService
{
    private GroqClient $claude;
    private array $tools;

    public function __construct(GroqClient $claude)
    {
        $this->claude = $claude;
        $this->tools  = [
            'get_menu'          => new GetMenuTool(),
            'place_order'       => new PlaceOrderTool(),
            'recommend_menu'    => new RecommendMenuTool(),
            'get_sales_summary' => new GetSalesSummaryTool(),
            'get_order_history' => new GetOrderHistoryTool(),
            'get_operational_hours' => new GetOperationalHoursTool(),
            'submit_feedback'       => new SubmitFeedbackTool(), 
        ];
    }

    public function chat(string $sessionId, string $userMessage): string
    {
        $cacheKey = "chat_history_{$sessionId}";

        // Ambil history dari cache, default array kosong
        $history = Cache::get($cacheKey, []);

        // Tambah pesan user
        $history[] = ['role' => 'user', 'content' => $userMessage];

        $toolDefinitions = array_map(fn($t) => $t->getDefinition(), $this->tools);

        for ($i = 0; $i < 5; $i++) {
            try {
                $response = $this->claude->sendMessage(
                    messages: $history,
                    tools:    $toolDefinitions,
                    system:   $this->systemPrompt()
                );
            } catch (\Exception $e) {
                // Jika error karena history corrupt, reset dan coba ulang tanpa history
                Cache::forget($cacheKey);
                $history  = [['role' => 'user', 'content' => $userMessage]];
                $response = $this->claude->sendMessage(
                    messages: $history,
                    tools:    $toolDefinitions,
                    system:   $this->systemPrompt()
                );
            }

            if ($response['stop_reason'] === 'tool_use') {
                // Simpan assistant turn dengan tool_calls ke history
                $history[] = ['role' => 'assistant', 'content' => $response['content']];

                $toolResults = $this->executeTools($response['content']);
                $history[]   = ['role' => 'user', 'content' => $toolResults];

                // Update cache setiap iterasi
                Cache::put($cacheKey, $history, now()->addHours(2));
                continue;
            }

            $answer = collect($response['content'])
                ->where('type', 'text')
                ->pluck('text')
                ->implode('');

            // Simpan hanya teks bersih ke history untuk giliran berikutnya
            $history[] = ['role' => 'assistant', 'content' => $answer];

            // Batasi history max 20 pesan agar tidak overflow
            if (count($history) > 20) {
                $history = array_slice($history, -20);
            }

            Cache::put($cacheKey, $history, now()->addHours(2));

            // Log ke DB
            ChatLog::create(['session_id' => $sessionId, 'role' => 'user',      'content' => $userMessage]);
            ChatLog::create(['session_id' => $sessionId, 'role' => 'assistant', 'content' => $answer]);

            return $answer;
        }

        return 'Maaf, saya tidak bisa memproses permintaan ini saat ini.';
    }

    private function executeTools(array $contentBlocks): array
    {
        return collect($contentBlocks)
            ->where('type', 'tool_use')
            ->map(function ($block) {
                $result = isset($this->tools[$block['name']])
                    ? $this->tools[$block['name']]->execute($block['input'])
                    : ['error' => "Tool '{$block['name']}' tidak dikenal."];

                return [
                    'type'        => 'tool_result',
                    'tool_use_id' => $block['id'],
                    'content'     => json_encode($result, JSON_UNESCAPED_UNICODE),
                ];
            })->values()->toArray();
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
        Kamu adalah Karen, asisten AI coffeeshop BrewNest yang ramah dan efisien.

        FORMAT RESPONSE WAJIB:
        - Gunakan emoji di awal setiap poin
        - Pisahkan setiap item dengan baris baru (\n)
        - Jangan tulis semua dalam satu paragraf panjang
        - Maksimal 2 kalimat per paragraf

        ALUR PEMESANAN:
        1. Pelanggan sebut item → panggil get_menu() untuk ID & harga
        2. Tanya nama jika belum ada
        3. Konfirmasi dengan format rapi
        4. Setelah pelanggan konfirmasi → panggil place_order()

        ALUR CEK RIWAYAT ORDER:
        1. Tanya nama pelanggan jika belum ada
        2. Panggil get_order_history() dengan nama pelanggan
        3. Tampilkan riwayat dengan format rapi

        ALUR JAM OPERASIONAL:
        - Jika pelanggan tanya "buka jam berapa", "masih buka?", "jam tutup" → panggil get_operational_hours(check_type: "today")
        - Jika tanya jadwal seminggu → panggil get_operational_hours(check_type: "all")
        - Jika tanya apakah sekarang buka → panggil get_operational_hours(check_type: "status")
        - JANGAN jawab jam operasional tanpa panggil tool ini terlebih dahulu

        FORMAT TAMPILKAN MENU:
        "☕ Menu yang tersedia:\n\n🍵 Espresso Based:\n• Americano — Rp 25.000\n• Latte — Rp 30.000\n\n🌿 Non Coffee:\n• Matcha Latte — Rp 28.000"

        FORMAT KONFIRMASI ORDER:
        "🛒 Konfirmasi pesanan untuk [nama]:\n\n• [qty]x [menu] = Rp[subtotal]\n\n💰 Total: Rp[total]\n\nSudah benar?"

        FORMAT JAM OPERASIONAL:
        "🕐 Info BrewNest hari ini ([hari]):\n\n🟢 Status: BUKA / 🔴 Status: TUTUP\n⏰ Jam buka: [jam_buka] - [jam_tutup]\n⌛ [status_detail]\n\nKami tunggu kunjunganmu! ☕"

        FORMAT JADWAL MINGGUAN:
        "📅 Jadwal BrewNest:\n\n• Senin - Kamis: 07.00 - 22.00\n• Jumat - Sabtu: 07.00 - 23.00\n• Minggu: 08.00 - 21.00"

        FORMAT RIWAYAT ORDER:
        "📦 Riwayat pesanan [nama]:\n\n1️⃣ [kode] — [status]\n   📋 [qty]x [menu]\n   💰 Total: [total]\n   🕐 [tanggal]"

        FORMAT ORDER BERHASIL:
        "✅ Pesanan berhasil!\n\n🧾 Kode: [kode]\n👤 Nama: [nama]\n💰 Total: Rp[total]\n\nPesananmu sedang diproses ☕"

        ATURAN PERHITUNGAN:
        - Total = SUM(harga × qty) semua item
        - JANGAN tambahkan biaya lain

        ATURAN LAIN:
        - JANGAN tulis <function=...> dalam response
        - JANGAN konfirmasi lebih dari 1 kali
        - SELALU pakai ID dari get_menu() saat place_order()
        - Jawab dalam Bahasa Indonesia yang hangat

        ALUR RATING & FEEDBACK:
        - Jika pelanggan ingin beri rating/ulasan → tanya kode order (opsional) dan rating 1-5
        - Jika pelanggan sebutkan rating langsung → langsung panggil submit_feedback()
        - Setelah order berhasil → tawarkan untuk beri rating

        FORMAT MINTA RATING:
        "Senang bisa melayani kamu! 😊\nMau kasih rating untuk pesanan tadi?\nKetik bintang 1-5 atau pilih di bawah:"

        FORMAT KONFIRMASI RATING:
        "⭐ Rating [X]/5 berhasil disimpan!\nTerima kasih atas ulasanmu, [nama]! 🙏"
        PROMPT;
    }
}