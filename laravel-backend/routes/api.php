<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

// ======== POST: Receive data from Python bridge ========
Route::post('/update-room', function (Request $request) {

    $request->validate([
        'room'    => 'required|string',
        'status'  => 'required|string',
        'reading' => 'nullable',
    ]);

    $room   = strtoupper(trim($request->room));
    $status = strtoupper(trim($request->status));

    $allowedRooms    = ['ROOM1', 'ROOM2', 'ROOM3'];
    $allowedStatuses = ['FIRE', 'SAFE'];

    if (!in_array($room, $allowedRooms)) {
        return response()->json(['error' => 'Invalid room'], 400);
    }
    
    if (!in_array($status, $allowedStatuses)) {
        return response()->json(['error' => 'Invalid status'], 400);
    }

    $reading = $request->reading ?? '--';
    if (is_numeric($reading)) {
        $reading = round((float) $reading, 1);
    }

    Cache::put($room . '_DATA', [
        'status'  => $status,
        'reading' => $reading,
        'buzzer'  => $status === 'FIRE',
        'time'    => now()->format('H:i:s'),
    ]);

    Cache::put('LAST_UPDATE', now()->format('H:i:s'));

    return response()->json(['success' => true]);
});

// ======== GET: Return all room data to the UI ========
Route::get('/rooms', function () {
    $default = ['status' => 'SAFE', 'reading' => '--', 'buzzer' => false, 'time' => '--:--'];

    return response()->json([
        'Room1'      => Cache::get('ROOM1_DATA', $default),
        'Room2'      => Cache::get('ROOM2_DATA', $default),
        'Room3'      => Cache::get('ROOM3_DATA', $default),
        'lastUpdate' => Cache::get('LAST_UPDATE', 'Never'),
    ]);
});

// ======== GET: Health check ========
Route::get('/test', function () {
    $default = ['status' => 'not set', 'reading' => '--', 'buzzer' => false, 'time' => '--'];
    return response()->json([
        'message' => 'Fire Detection API is online',
        'cache'   => [
            'ROOM1' => Cache::get('ROOM1_DATA', $default),
            'ROOM2' => Cache::get('ROOM2_DATA', $default),
            'ROOM3' => Cache::get('ROOM3_DATA', $default),
        ],
    ]);
});

// ======== POST: Clear cache for testing ========
Route::post('/clear-cache', function () {
    Cache::forget('ROOM1_DATA');
    Cache::forget('ROOM2_DATA');
    Cache::forget('ROOM3_DATA');
    Cache::forget('LAST_UPDATE');
    return response()->json(['success' => true, 'message' => 'Cache cleared']);
});