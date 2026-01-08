<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class YandexGptClient
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = env('YANDEX_API_KEY');
        $this->model  = env('YANDEX_GPT_MODEL', 'YandexGPT');
    }

    // public function ask(string $text): array
    // {
    //     $response = Http::withToken(env('YANDEX_API_KEY'))
    //         ->post('POST https://api.ai.yandexcloud.net/v1/models/YandexGPT:generate', [
    //             'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    //             'messages' => [
    //                 [
    //                     'role' => 'system',
    //                     'content' => 'Ты помощник, который отвечает на русском.'
    //                 ],
    //                 [
    //                     'role' => 'user',
    //                     'content' => $text
    //                 ]
    //             ],
    //         ])
    //         ->throw()
    //         ->json();

    //     return $response;
    // }

    public function ask(string $text): array
    {
        $prompt = $this->buildPrompt($text);

        $response = Http::withHeaders([
            'Authorization' => 'Api-Key ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post(
            "https://api.ai.yandexcloud.net/v1/models/{$this->model}:generate",
            [
                'input' => $prompt,
                'parameters' => [
                    'temperature' => 0.1,
                    'max_output_tokens' => 300,
                ]
            ]
        );

        if (!$response->successful()) {
            throw new \RuntimeException(
                'YandexGPT error: ' . $response->body()
            );
        }

        return $this->extractJson($response->json());
    }

    private function buildPrompt(string $text): string
    {
        return <<<PROMPT
Ты парсер финансовых операций.
На входе — текст пользователя.
На выходе — ТОЛЬКО JSON строго по схеме.

Схема:
{
  "amount": number,
  "currency": "RUB",
  "category": string,
  "account": string,
  "type": "expense" | "income",
  "description": string
}

Правила:
- если сумма не указана — amount = null
- если категория не ясна — category = "other"
- никаких пояснений
- никаких комментариев
- только JSON

Текст:
{$text}
PROMPT;
    }

    private function extractJson(array $response): array
    {
        // YandexGPT возвращает текст — достаём его
        $text = $response['result']['alternatives'][0]['text'] ?? '';

        $json = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(
                'Invalid JSON from YandexGPT: ' . $text
            );
        }

        return $json;
    }
}
