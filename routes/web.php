<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

////////////// Routes for non authenticated users //////////////


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Pages
Route::get('api/pages', [PageController::class, 'index']);
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');

// CMS Page Actions
Route::middleware('auth')->group(function () {
    Route::put('/pages/update/{page}', [PageController::class, 'update'])->name('api.pages.update');
    Route::post('/pages/store', [PageController::class, 'store'])->name('api.pages.store');
});
