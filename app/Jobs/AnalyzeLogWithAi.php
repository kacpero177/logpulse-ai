<?php

namespace App\Jobs;

use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class AnalyzeLogWithAi implements ShouldQueue
{
    use Queueable;

    public function __construct(public Log $log) {}

    public function handle(): void
    {
        $this->log->update(['status' => 'processing']);

        $apiKey = env('GROQ_API_KEY');

        if (!$apiKey) {
            $this->log->update([
                'status' => 'failed',
                'ai_summary' => 'Missing GROQ_API_KEY in the .env file'
            ]);
            return;
        }

        // Tłumaczenie promptu na angielski, żeby AI odpowiadało po angielsku
        $prompt = "Analyze the following application error. Provide a concise response (max 3 sentences): 1. What is the root cause, 2. How to fix it.\n\n" .
                  "Service: {$this->log->service_name}\n" .
                  "Message: {$this->log->message}\n" .
                  "Stack Trace: " . ($this->log->stack_trace ?? 'None');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.1-8b-instant',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a DevOps and backend expert.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.2,
        ]);

        if ($response->successful()) {
            $aiResponse = $response->json('choices.0.message.content');
            $this->log->update([
                'ai_summary' => $aiResponse,
                'status'     => 'analyzed',
            ]);

            // Wysyłamy powiadomienie na Discorda
            $this->sendDiscordNotification($aiResponse);
        } else {
            $this->log->update([
                'status'     => 'failed',
                'ai_summary' => 'Error communicating with AI API: ' . $response->body(),
            ]);
        }
    }

    private function sendDiscordNotification(string $aiSummary): void
    {
        $webhookUrl = env('DISCORD_WEBHOOK_URL');

        if (!$webhookUrl) {
            return;
        }

        // Kolor paska w powiadomieniu: Czerwony dla CRITICAL, Pomarańczowy dla ERROR
        $color = $this->log->level === 'critical' ? 15158332 : 15105570;

        Http::post($webhookUrl, [
            'username' => 'LogPulse AI Bot',
            'embeds' => [
                [
                    'title' => "Error detected: {$this->log->service_name} [" . strtoupper($this->log->level) . "]",
                    'color' => $color,
                    'fields' => [
                        [
                            'name' => 'Error Message',
                            'value' => "```" . substr($this->log->message, 0, 1000) . "```",
                            'inline' => false,
                        ],
                        [
                            'name' => 'AI Analysis (Llama 3)',
                            'value' => substr($aiSummary, 0, 1000),
                            'inline' => false,
                        ],
                    ],
                    'timestamp' => now()->toIso8601String(),
                ]
            ]
        ]);
    }
}