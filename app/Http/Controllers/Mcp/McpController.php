<?php

namespace App\Http\Controllers\Mcp;

use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;

class McpController extends Controller
{
    public function parse(Request $request)
    {
        return response()->json([
            'ok' => true,
            'input' => $request->input('text'),
            'meta' => [
                'source' => 'mcp',
                'version' => '1.0'
            ]
        ]);
    }
}
