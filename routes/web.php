<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumniTrackerController;

Route::get('/', [AlumniTrackerController::class, 'index']);
Route::post('/track', [AlumniTrackerController::class, 'track'])->name('track.alumni');