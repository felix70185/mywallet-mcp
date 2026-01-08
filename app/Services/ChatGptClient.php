<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ChatGptClient
{
    public function ask(string $text): array
    {
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Ты помощник, который отвечает на русском.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $text
                    ]
                ],
            ])
            ->throw()
            ->json();

        return $response;
    }
}
