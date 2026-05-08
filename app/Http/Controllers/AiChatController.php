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

        // Pakai session ID Laravel yang persisten per browser
        $sessionId = $request->session()->getId();

        try {
            $reply = $this->agent->chat($sessionId, $request->message);
            return response()->json(['reply' => $reply]);
        } catch (\Exception $e) {
            \Log::error('AgentService error: ' . $e->getMessage());
            return response()->json(['reply' => 'Maaf, terjadi kesalahan. Silakan coba lagi.'], 500);
        }
    }

    public function index()
    {
        return view('chat.widget');
    }
}