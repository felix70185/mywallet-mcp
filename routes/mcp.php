<?php

/** @var \Laravel\Lumen\Routing\Router $router */

//$router->post('/parse', 'Mcp\McpController@parse');

$router->get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'mcp',
        'time' => 'test',
    ]);
});

$router->post('/parse', function (\Illuminate\Http\Request $request) {
    $text = mb_strtolower($request->input('text', ''));

    $response = [
        'ok' => true,
        'intent' => 'unknown',
        'confidence' => 0.2,
        'data' => [
            'amount' => null,
            'currency' => 'RUB',
            'type' => null,
            'category' => null,
            'datetime' => null,
            'comment' => null,
        ],
        'errors' => [],
    ];

    if (preg_match('/(\d+)/', $text, $m)) {
        $response['intent'] = 'transaction.create';
        $response['confidence'] = 0.6;
        $response['data']['amount'] = (int)$m[1];
        $response['data']['type'] = 'expense';
        $response['data']['comment'] = $text;
    }

    return response()->json($response);
});