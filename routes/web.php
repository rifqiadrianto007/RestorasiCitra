<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/smooth-form', function () {
    return view('smooth');
});

Route::get('/remove-form', function () {
    return view('remove');
});

Route::post('/smooth', [ImageController::class, 'smooth'])->name('smooth.process');
Route::post('/remove-background', [ImageController::class, 'removeBackground'])->name('remove.process');
