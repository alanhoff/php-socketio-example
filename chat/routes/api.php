<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/internal/invoke', function (Request $request) {
    // You can send any data from Socket.IO, then invoke a
    // Laravel event or broadcast it. For demonstration purposes
    // we'll just forward the payload back into Socket.IO so
    // I can demonstrate a complete round-tripe between both projects
    $payload = [
        'room' => $request->string('room')->trim(),
        'event' => $request->string('event')->trim(),
        'params' => $request->get('params'),
    ];

    $result = Http::post('http://websocket:8080/api/internal/emit', $payload);
    return response()->json($result->json(), $result->status());
});
