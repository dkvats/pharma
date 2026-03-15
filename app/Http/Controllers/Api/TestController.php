<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * Test API status endpoint.
     * Returns a simple JSON response to verify API is working.
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'API is working',
            'timestamp' => now()->toIso8601String(),
            'version' => '1.0.0',
        ]);
    }
}
