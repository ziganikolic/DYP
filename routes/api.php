<?php

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::post('/tournaments', [TournamentController::class, 'create']);
Route::get('/tournaments/{code}', [TournamentController::class, 'data']);
Route::post('/tournaments/{code}/matches/{matchId}/winner', [TournamentController::class, 'selectWinner']);
