<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

Route::post('/update-room', function (Request $request) {

    // Validate input
    $request->validate([
        'room' => 'required|string',
        'status' => 'required|string'
    ]);

    $room = strtoupper($request->room);
    $status = strtoupper($request->status);

    // Whitelist allowed rooms
    $allowedRooms = ['ROOM1', 'ROOM2', 'ROOM3'];
    if (!in_array($room, $allowedRooms)) {
        return response()->json(['error' => 'Invalid room'], 400);
    }

    // Whitelist allowed states
    $allowedStates = ['FIRE', 'SAFE'];
    if (!in_array($status, $allowedStates)) {
        return response()->json(['error' => 'Invalid status'], 400);
    }

    // Save to cache with timestamp
    Cache::put($room, $status);
    Cache::put($room . '_TIME', now()->format('H:i:s'));
    
    // If status is FIRE, also set buzzer to true
    if ($status === 'FIRE') {
        Cache::put($room . '_BUZZER', true);
    } else {
        Cache::put($room . '_BUZZER', false);
    }
    
    // Update overall last update time
    Cache::put('LAST_UPDATE', now()->format('H:i:s'));

    return response()->json(['success' => true]);
});

Route::get('/rooms', function () {
    return [
        'Room1' => Cache::get('ROOM1', 'SAFE'),
        'Room2' => Cache::get('ROOM2', 'SAFE'),
        'Room3' => Cache::get('ROOM3', 'SAFE'),
        'lastUpdate' => Cache::get('LAST_UPDATE', 'Never'),
    ];
});

// Add a test endpoint to check if API is working
Route::get('/test', function () {
    return response()->json([
        'message' => 'Fire Detection System API is working!',
        'status' => 'online',
        'cache' => [
            'ROOM1' => Cache::get('ROOM1', 'Not set'),
            'ROOM2' => Cache::get('ROOM2', 'Not set'),
            'ROOM3' => Cache::get('ROOM3', 'Not set'),
        ]
    ]);
});

// Clear cache for testing
Route::post('/clear-cache', function () {
    Cache::forget('ROOM1');
    Cache::forget('ROOM2');
    Cache::forget('ROOM3');
    Cache::forget('ROOM1_TIME');
    Cache::forget('ROOM2_TIME');
    Cache::forget('ROOM3_TIME');
    Cache::forget('LAST_UPDATE');
    return response()->json(['success' => true, 'message' => 'Cache cleared']);
});