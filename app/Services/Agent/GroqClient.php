<?php

namespace App\Services\Agent;

use Illuminate\Support\Facades\Http;

class GroqClient
{
    private string $apiKey;
    private string $model;
    private int    $maxTokens;

    public function __construct()
    {
        $this->apiKey    = config('services.groq.api_key');
        $this->model     = config('services.groq.model');
        $this->maxTokens = config('services.groq.max_tokens', 1024);
    }

    public function sendMessage(array $messages, array $tools = [], string $system = ''): array
    {
        $formattedMessages = [];

        // System prompt sebagai message pertama
        if ($system) {
            $formattedMessages[] = ['role' => 'system', 'content' => $system];
        }

        foreach ($messages as $msg) {
            // Handle tool_result (array content dari AgentService)
            if (is_array($msg['content'])) {
                foreach ($msg['content'] as $block) {
                    if (isset($block['type']) && $block['type'] === 'tool_result') {
                        $formattedMessages[] = [
                            'role'         => 'tool',
                            'tool_call_id' => $block['tool_use_id'],
                            'content'      => $block['content'],
                        ];
                    }
                }
                continue;
            }

            // Handle assistant message yang punya tool_use block
            if ($msg['role'] === 'assistant' && is_array($msg['content'])) {
                $toolCalls  = [];
                $textContent = null;

                foreach ($msg['content'] as $block) {
                    if ($block['type'] === 'tool_use') {
                        $toolCalls[] = [
                            'id'   => $block['id'],
                            'type' => 'function',
                            'function' => [
                                'name'      => $block['name'],
                                'arguments' => json_encode($block['input']),
                            ],
                        ];
                    } elseif ($block['type'] === 'text') {
                        $textContent = $block['text'];
                    }
                }

                $assistantMsg = ['role' => 'assistant', 'content' => $textContent ?? ''];
                if (!empty($toolCalls)) {
                    $assistantMsg['tool_calls'] = $toolCalls;
                }
                $formattedMessages[] = $assistantMsg;
                continue;
            }

            // Pesan biasa user/assistant
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

        // Format tools ke OpenAI schema
        if (!empty($tools)) {
            $formattedTools = [];
            foreach ($tools as $tool) {
                $formattedTools[] = [
                    'type'     => 'function',
                    'function' => [
                        'name'        => $tool['name'],
                        'description' => $tool['description'],
                        'parameters'  => $tool['input_schema'],
                    ],
                ];
            }
            $payload['tools']       = $formattedTools;
            $payload['tool_choice'] = 'auto';
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', $payload);

        $response->throw();

        return $this->normalizeResponse($response->json());
    }

    /**
     * Normalisasi response Groq (OpenAI format) → format Anthropic
     * Supaya AgentService tidak perlu diubah sama sekali
     */
    private function normalizeResponse(array $groqResponse): array
    {
        $choice  = $groqResponse['choices'][0];
        $message = $choice['message'];

        $content    = [];
        $stopReason = 'end_turn';

        // Jika ada tool calls → trigger agentic loop
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

        // Tambahkan teks jika ada (Groq kadang null saat tool_calls)
        if (!empty($message['content'])) {
            $content[] = [
                'type' => 'text',
                'text' => $message['content'],
            ];
        }

        // Fallback agar content tidak pernah kosong
        if (empty($content)) {
            $content[] = ['type' => 'text', 'text' => ''];
        }

        return [
            'content'     => $content,
            'stop_reason' => $stopReason,
        ];
    }
}