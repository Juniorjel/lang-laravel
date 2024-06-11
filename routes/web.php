<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranslationController;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
Route::post('/translations/update', [TranslationController::class, 'update'])->name('translations.update');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
