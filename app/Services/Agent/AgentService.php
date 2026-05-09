<?php

namespace App\Services\Agent;

use App\Models\ChatLog;
use App\Services\Agent\GroqClient;
use App\Services\Agent\Tools\GetMenuTool;
use App\Services\Agent\Tools\PlaceOrderTool;
use App\Services\Agent\Tools\RecommendMenuTool;
use App\Services\Agent\Tools\GetSalesSummaryTool;
use Illuminate\Support\Facades\Cache;

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

        ALUR PEMESANAN YANG BENAR:
        1. Pelanggan menyebut item yang ingin dipesan
        2. Kamu WAJIB panggil get_menu() dulu untuk cek ID produk yang benar
        3. Tanya nama pelanggan jika belum disebutkan
        4. Konfirmasi detail order: nama pelanggan, item, qty, dan total harga
        5. Setelah pelanggan jawab "iya/ya/benar/oke/lanjut", LANGSUNG panggil place_order()

        ATURAN PENTING:
        - JANGAN tanya "berapa" qty jika sudah disebutkan (contoh: "latte 1" = qty 1)
        - JANGAN tanya konfirmasi lebih dari 1 kali
        - Untuk multiple item: "latte 1 dan americano 2" = [{latte, qty:1}, {americano, qty:2}]
        - Selalu tampilkan total harga saat konfirmasi
        - JANGAN tulis <function=...> dalam response
        - Gunakan tool calling untuk semua data real-time
        - Jawab singkat, ramah, dan profesional dalam Bahasa Indonesia

        CONTOH ALUR BENAR:
        User: "pesan latte 1 dan americano 2"
        Karen: [panggil get_menu()] → "Siap! Pesanan kamu:\n- 1x Latte Rp 30.000\n- 2x Americano Rp 50.000\nTotal: Rp 80.000\n\nNama kamu siapa?"
        User: "Rio"
        Karen: "Konfirmasi untuk Rio:\n- 1x Latte\n- 2x Americano\nTotal: Rp 80.000\n\nSudah benar?"
        User: "iya"
        Karen: [panggil place_order()] → "Pesanan berhasil! Kode: ORD-xxx ☕"
        PROMPT;
    }
}