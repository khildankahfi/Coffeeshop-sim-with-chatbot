<?php

namespace App\Services\Agent;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GroqClient
{
    private array  $apiKeys;
    private string $model;
    private int    $maxTokens;

    public function __construct()
    {
        // Parse semua key dari .env
        $this->apiKeys   = array_filter(
            array_map('trim', explode(',', config('services.groq.api_keys')))
        );
        $this->model     = config('services.groq.model');
        $this->maxTokens = config('services.groq.max_tokens', 1024);

        if (empty($this->apiKeys)) {
            throw new \RuntimeException('Tidak ada GROQ_API_KEYS yang dikonfigurasi.');
        }
    }

    /**
     * Ambil API key yang aktif (round-robin + skip yang sedang rate-limited)
     */
    private function getActiveKey(): ?string
    {
        $totalKeys = count($this->apiKeys);

        for ($i = 0; $i < $totalKeys; $i++) {
            // Ambil index saat ini dari cache (round-robin)
            $currentIndex = Cache::get('groq_key_index', 0) % $totalKeys;
            $key = array_values($this->apiKeys)[$currentIndex];

            // Cek apakah key ini sedang kena rate limit
            $isLimited = Cache::get("groq_key_limited_{$currentIndex}", false);

            if (!$isLimited) {
                return $key;
            }

            // Key ini limited, coba next
            $nextIndex = ($currentIndex + 1) % $totalKeys;
            Cache::put('groq_key_index', $nextIndex, 300);
        }

        // Semua key kena limit — return key pertama dan harap sudah reset
        Cache::put('groq_key_index', 0, 300);
        return array_values($this->apiKeys)[0];
    }

    /**
     * Tandai key sebagai rate-limited selama 60 detik
     */
    private function markKeyAsLimited(int $keyIndex): void
    {
        Cache::put("groq_key_limited_{$keyIndex}", true, 60); // block 60 detik
        \Log::warning("Groq key #{$keyIndex} kena rate limit, pindah ke key berikutnya.");

        // Pindah ke key berikutnya
        $nextIndex = ($keyIndex + 1) % count($this->apiKeys);
        Cache::put('groq_key_index', $nextIndex, 300);
    }

    public function sendMessage(array $messages, array $tools = [], string $system = ''): array
    {
        $totalKeys   = count($this->apiKeys);
        $lastException = null;

        // Coba semua key sampai berhasil
        for ($attempt = 0; $attempt < $totalKeys; $attempt++) {
            $currentIndex = Cache::get('groq_key_index', 0) % $totalKeys;
            $apiKey       = array_values($this->apiKeys)[$currentIndex];

            try {
                $response = $this->callApi($apiKey, $messages, $tools, $system);
                return $response;

            } catch (\Illuminate\Http\Client\RequestException $e) {
                if ($e->response->status() === 429) {
                    // Rate limit — tandai key ini dan coba key berikutnya
                    $this->markKeyAsLimited($currentIndex);
                    $lastException = $e;
                    sleep(1); // tunggu sebentar sebelum retry
                    continue;
                }
                // Error lain — langsung throw
                throw $e;
            }
        }

        // Semua key gagal
        throw $lastException ?? new \RuntimeException('Semua Groq API key kena rate limit.');
    }

    private function callApi(string $apiKey, array $messages, array $tools, string $system): array
    {
        $formattedMessages = [];

        if ($system) {
            $formattedMessages[] = ['role' => 'system', 'content' => $system];
        }

        foreach ($messages as $msg) {
            if (is_array($msg['content'])) {
                foreach ($msg['content'] as $block) {
                    if (isset($block['type'])) {
                        if ($block['type'] === 'tool_result') {
                            $formattedMessages[] = [
                                'role'         => 'tool',
                                'tool_call_id' => $block['tool_use_id'],
                                'content'      => $block['content'],
                            ];
                        } elseif ($block['type'] === 'tool_use') {
                            // Skip — sudah dihandle di assistant message
                        }
                    }
                }
                // Tambahkan assistant message dengan tool_calls jika ada
                $toolCalls = collect($msg['content'])->where('type', 'tool_use');
                if ($toolCalls->isNotEmpty()) {
                    $formattedMessages[] = [
                        'role'       => 'assistant',
                        'content'    => null,
                        'tool_calls' => $toolCalls->map(fn($b) => [
                            'id'       => $b['id'],
                            'type'     => 'function',
                            'function' => [
                                'name'      => $b['name'],
                                'arguments' => json_encode($b['input']),
                            ],
                        ])->values()->toArray(),
                    ];
                }
                continue;
            }

            $formattedMessages[] = [
                'role'    => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        $payload = [
            'model'      => $this->model,
            'max_tokens' => $this->maxTokens,
            'messages'   => $formattedMessages,
        ];

        if (!empty($tools)) {
            $payload['tools'] = array_values(array_map(fn($tool) => [
                'type'     => 'function',
                'function' => [
                    'name'        => $tool['name'],
                    'description' => $tool['description'],
                    'parameters'  => $tool['input_schema'],
                ],
            ], $tools));
            $payload['tool_choice'] = 'auto';
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', $payload);

        $response->throw();

        return $this->normalizeResponse($response->json());
    }

    private function normalizeResponse(array $groqResponse): array
    {
        $choice  = $groqResponse['choices'][0];
        $message = $choice['message'];

        $content    = [];
        $stopReason = 'end_turn';

        if (!empty($message['tool_calls'])) {
            $stopReason = 'tool_use';
            foreach ($message['tool_calls'] as $toolCall) {
                $content[] = [
                    'type'  => 'tool_use',
                    'id'    => $toolCall['id'],
                    'name'  => $toolCall['function']['name'],
                    'input' => json_decode($toolCall['function']['arguments'], true) ?? [],
                ];
            }
        }

        if (!empty($message['content'])) {
            // Bersihkan tag function yang mungkin muncul
            $cleanText = preg_replace('/<function=\w+>.*?<\/function>/s', '', $message['content']);
            $cleanText = preg_replace('/<function=\w+>.*$/s', '', $cleanText);
            $cleanText = trim($cleanText);

            if (!empty($cleanText)) {
                $content[] = ['type' => 'text', 'text' => $cleanText];
            }
        }

        if (empty($content)) {
            $content[] = ['type' => 'text', 'text' => ''];
        }

        return ['content' => $content, 'stop_reason' => $stopReason];
    }
}