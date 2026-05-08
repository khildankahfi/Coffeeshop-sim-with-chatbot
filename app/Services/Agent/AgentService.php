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
        Kamu adalah Karen, asisten AI coffeeshop kami yang ramah dan helpful.
        Aturan penting:
        - Gunakan tool calling yang tersedia untuk data real-time
        - JANGAN tulis <function=...> dalam teks responmu
        - Tanya nama pelanggan jika belum diketahui sebelum place_order
        - Setelah pelanggan konfirmasi pesanan dengan "iya/ya/benar/oke", LANGSUNG panggil place_order
        - JANGAN tanya konfirmasi lebih dari sekali
        - JANGAN mengarang menu atau harga
        Jawab dalam Bahasa Indonesia yang singkat, hangat, dan profesional.
        PROMPT;
    }
}