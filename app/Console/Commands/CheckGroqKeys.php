<?php
// app/Console/Commands/CheckGroqKeys.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CheckGroqKeys extends Command
{
    protected $signature   = 'groq:check-keys';
    protected $description = 'Cek status semua Groq API key';

    public function handle(): void
    {
        $keys = array_filter(
            array_map('trim', explode(',', config('services.groq.api_keys')))
        );

        $this->info('=== Status Groq API Keys ===');

        foreach (array_values($keys) as $i => $key) {
            $masked   = substr($key, 0, 8) . '...' . substr($key, -4);
            $limited  = Cache::get("groq_key_limited_{$i}", false);
            $status   = $limited ? '🔴 Rate Limited' : '🟢 Available';
            $this->line("Key #{$i} ({$masked}): {$status}");
        }

        $currentIndex = Cache::get('groq_key_index', 0);
        $this->info("\nKey aktif saat ini: #{$currentIndex}");
    }
}