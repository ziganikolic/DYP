<?php

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [TournamentController::class, 'index'])->name('home');

Route::prefix('tournaments')->name('tournaments.')->group(function () {
    Route::get('/', [TournamentController::class, 'index'])->name('index');
    Route::post('/', [TournamentController::class, 'store'])->name('store');
    Route::get('/{tournament}', [TournamentController::class, 'show'])->name('show');
    Route::post('/{tournament}/matches/{match}/winner', [TournamentController::class, 'selectWinner'])->name('select-winner');
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
