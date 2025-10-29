<?php

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TournamentController::class, 'index'])->name('home');
Route::get('/tournament/{code}', [TournamentController::class, 'show'])->name('tournament.show');
