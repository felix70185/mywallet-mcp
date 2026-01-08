<?php

namespace App\Http\Controllers\Mcp;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\YandexGptClient;

class StoreCostController extends Controller
{
    public function store(Request $request, YandexGptClient $client)
    {
        $text = $request->input('text');

        $result = $client->ask($text);

        return response()->json([
            'raw'   => $result['choices'][0]['message']['content'] ?? null,
            'model' => $result['model'] ?? null,
            'usage' => $result['usage'] ?? null,
        ]);
    }
}
