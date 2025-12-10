<?php

use Illuminate\Support\Facades\Route;

// Redirect root to the floorplan dashboard for convenience
Route::redirect('/', '/floorplan');

Route::get('/floorplan', function () {
    return view('floorplan');
});
