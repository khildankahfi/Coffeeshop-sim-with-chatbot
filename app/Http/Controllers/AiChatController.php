<?php

namespace App\Http\Controllers;

use App\Services\Agent\AgentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AiChatController extends Controller
{
    public function __construct(private AgentService $agent) {}

    public function send(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:500']);

        $sessionId = $request->session()->getId();

        try {
            $reply = $this->agent->chat($sessionId, $request->message);

            if (empty(trim($reply))) {
                return response()->json(['reply' => 'Maaf, saya tidak mengerti. Bisa ulangi pertanyaanmu?']);
            }

            return response()->json(['reply' => $reply]);

        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Log::error('Groq API error: ' . $e->getMessage());

            // Reset cache session jika error API
            \Cache::forget("chat_history_{$sessionId}");

            return response()->json([
                'reply' => 'Maaf, asisten sedang sibuk. Silakan coba lagi dalam beberapa detik.'
            ]);

        } catch (\Exception $e) {
            \Log::error('AgentService error: ' . $e->getMessage());

            return response()->json([
                'reply' => 'Terjadi kesalahan. Silakan refresh halaman dan coba lagi.'
            ]);
        }
    }

    public function index()
    {
        return view('chat.widget');
    }
}