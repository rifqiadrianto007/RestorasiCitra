<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

Route::get('/smooth-form', function () {
    return view('smooth');
});

Route::post('/smooth', [ImageController::class, 'smooth'])->name('smooth.process');
