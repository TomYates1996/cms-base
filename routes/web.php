<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\WidgetController;

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
Route::get('api/pages', [PageController::class, 'index']);
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');

// CMS Actions
Route::middleware('auth')->group(function () {
    Route::put('/pages/update/{page}', [PageController::class, 'update'])->name('api.pages.update');
    Route::post('/pages/store', [PageController::class, 'store'])->name('api.pages.store');
    Route::get('/api/slides', [SlideController::class, 'index'])->name('api.slides.index');
    Route::post('/api/slides', [SlideController::class, 'store'])->name('api.slides.store');
    Route::get('/cms/slides', [SlideController::class, 'load'])->name('slides.load');
    Route::get('/cms/pages', [PageController::class, 'load'])->name('pages.load');
    Route::get('/cms/pages/edit/{page_id}', [PageController::class, 'load_edit'])->name('pages.load.edit');
    Route::get('/cms/pages/edit-content/{page_id}', [PageController::class, 'load_edit_content'])->name('pages.load.edit.content');
    Route::get('api/slides', [SlideController::class, 'index']);
    
    Route::prefix('cms')->group(function () {
        // Display all widgets for a page
        Route::get('/pages/{pageId}/widgets', [WidgetController::class, 'index'])->name('widgets.index');
        
        // Show form to create a new widget
        Route::get('/pages/{pageId}/widgets/create', [WidgetController::class, 'create'])->name('widgets.create');
        
        // Store a new widget
        Route::post('/pages/{pageId}/widgets', [WidgetController::class, 'store'])->name('widgets.store');
        
        // Show form to edit a widget
        Route::get('/widgets/{id}/edit', [WidgetController::class, 'edit'])->name('widgets.edit');
        
        Route::post('/pages/save', [WidgetController::class, 'save'])->name('page.save');

    });

});
