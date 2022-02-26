<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PosterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'titles' => cache()->rememberForever('titles', function () {
            return number_format(\App\Models\Title::count());
        }),
        'movies' => cache()->rememberForever('movies', function () {
            return number_format(\App\Models\Title::where('title_type', 'movie')->count());
        }),
        'series' => cache()->rememberForever('series', function () {
            return number_format(\App\Models\Title::where('title_type', 'tvSeries')->count());
        }),
        'episodes' => cache()->rememberForever('episodes', function () {
            return number_format(\App\Models\Title::where('title_type', 'tvEpisode')->count());
        }),
    ]);
})->name('welcome');

Route::middleware(['auth:sanctum'])->get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');
Route::resource('/movies', MovieController::class);
Route::resource('/series', SeriesController::class);
Route::middleware(['auth:sanctum'])->get('/search', [SearchController::class, 'search']);
Route::middleware(['auth:sanctum'])->put('/token', [TokenController::class, 'update'])->name('token');
Route::middleware(['auth:sanctum'])->post('/watch', \App\Http\Controllers\WatchController::class);
Route::post('/check-poster', [PosterController::class, 'check']);
