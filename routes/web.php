<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SwapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

Route::get('/', [SwapController::class, 'index'])->name('swap.index');
Route::post('/calculate', [SwapController::class, 'calculate'])->name('swap.calculate');
Route::get('/history', [SwapController::class, 'history'])->name('swap.history');
Route::delete('/history/{id}', [SwapController::class, 'destroy'])->name('swap.destroy');

// Simple language switcher: store locale in session and redirect back
Route::get('/lang/{lang}', function (Request $request, $lang) {
	$allowed = ['en', 'vi'];
	if (!in_array($lang, $allowed)) {
		abort(404);
	}
	$request->session()->put('locale', $lang);
	return Redirect::back();
})->name('lang.switch');
