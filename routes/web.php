<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BentController;
use App\Http\Controllers\SboxController;

//Route::get('/', function () {
//    return view('welcome');
//});

// Bosh sahifa
Route::get('/', fn() => redirect()->route('bent.index'));

// Bent funksiyalar
Route::prefix('bent')->name('bent.')->group(function () {
    Route::get('/',           [BentController::class, 'index'])   ->name('index');
    Route::post('/calculate', [BentController::class, 'calculate'])->name('calculate');
    Route::post('/store',     [BentController::class, 'store'])    ->name('store');
    Route::get('/library',    [BentController::class, 'library'])  ->name('library');
    Route::get('/{bentFunction}',      [BentController::class, 'show'])   ->name('show');
    Route::delete('/{bentFunction}',   [BentController::class, 'destroy'])->name('destroy');
});

// S-qutilar
Route::prefix('sbox')->name('sbox.')->group(function () {
    Route::get('/',          [SboxController::class, 'index'])   ->name('index');
    Route::post('/generate', [SboxController::class, 'generate'])->name('generate');
});
