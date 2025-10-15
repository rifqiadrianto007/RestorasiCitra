<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/smooth-form', function () {
    return view('smooth');
});

Route::get('/', function () {
    return response()->json(['message' => 'Image Restoration API ready']);
});

Route::post('/smooth', [ImageController::class, 'smooth'])->name('smooth.process');
Route::post('/remove-background', [ImageController::class, 'removeBackground']);
