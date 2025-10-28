<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SwapController;

Route::get('/', [SwapController::class, 'index'])->name('swap.index');
Route::post('/calculate', [SwapController::class, 'calculate'])->name('swap.calculate');
Route::get('/history', [SwapController::class, 'history'])->name('swap.history');
Route::delete('/history/{id}', [SwapController::class, 'destroy'])->name('swap.destroy');
