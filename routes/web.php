<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\ImageController;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::redirect('/cms', 'cms/dashboard', 301);

Route::get('cms/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('cms.dashboard');


////////////// Routes for non authenticated users //////////////


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Pages
Route::get('api/pages', [PageController::class, 'index']);
Route::get('api/pages', [PageController::class, 'index']);
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');

Route::get('cms/dashboard', [PageController::class, 'load_dashboard'])->name('cms.dashboard');

// CMS Actions
Route::middleware('auth')->group(function () {
    Route::put('/pages/update/{slug}', [PageController::class, 'update'])->name('api.pages.update');
    Route::post('/pages/store', [PageController::class, 'store'])->name('api.pages.store');
    Route::get('/api/slides', [SlideController::class, 'index'])->name('api.slides.index');
    Route::post('/api/slides', [SlideController::class, 'store'])->name('api.slides.store');
    Route::put('/api/slides', [SlideController::class, 'update'])->name('api.slides.update');
    Route::get('/cms/slides/edit/{slide_slug}', [SlideController::class, 'load_edit'])->name('api.slides.load.edit');
    Route::get('/cms/slides', [SlideController::class, 'load'])->name('slides.load');
    Route::get('/cms/pages', [PageController::class, 'load'])->name('pages.load');
    Route::delete('/cms/pages/delete/{page_id}', [PageController::class, 'destroy'])->name('pages.delete');
    Route::get('/cms/pages/children/{page_slug}', [PageController::class, 'children'])->name('pages.children');
    Route::get('/cms/pages/edit/{page_slug}', [PageController::class, 'load_edit'])->name('pages.load.edit');
    Route::get('/cms/pages/edit-content/{page_slug}', [PageController::class, 'load_edit_content'])->name('pages.load.edit.content');
    Route::get('api/slides', [SlideController::class, 'index']);
    Route::post('/create/new-image', [ImageController::class, 'store'])->name('cms.image.store');
    Route::get('/api/images/all', [ImageController::class, 'index'])->name('cms.image.index');
    Route::get('/api/pages/all', [PageController::class, 'index_all'])->name('cms.page.index');
    Route::put('/widgets/{widget}/save', [WidgetController::class, 'save_widget'])->name('widgets.save');
    Route::put('/headers/{header}/save', [WidgetController::class, 'save_header'])->name('header.save');
    Route::put('/footers/{footer}/save', [WidgetController::class, 'save_footer'])->name('footer.save');
    Route::post('/item/delete-save', [WidgetController::class, 'delete_save_item'])->name('widgets.delete-save');
    Route::post('/item/update-save', [WidgetController::class, 'update_save_item'])->name('widgets.update-save');
    Route::post('/widgets/create-save', [WidgetController::class, 'create_save_widget'])->name('widgets.create-save');
    Route::post('/headers/create-save', [WidgetController::class, 'create_save_header'])->name('header.create-save');
    Route::post('/footers/create-save', [WidgetController::class, 'create_save_footer'])->name('footer.create-save');
    
    Route::prefix('cms')->group(function () {
        Route::get('/pages/primary', [PageController::class, 'load_primary'])->name('pages.load.primary');
        Route::get('/pages/secondary', [PageController::class, 'load_secondary'])->name('pages.load.secondary');
        Route::get('/pages/footer', [PageController::class, 'load_footer'])->name('pages.load.footer');
        Route::get('/images', [ImageController::class, 'load'])->name('images.load');
        Route::get('/images/edit', [ImageController::class, 'load_edit'])->name('images.load.edit');
        Route::post('/images/update', [ImageController::class, 'update'])->name('images.update');
        Route::get('/slides/new', [SlideController::class, 'load_create'])->name('slides.load.create');
        Route::delete('/slides/delete/{id}', [SlideController::class, 'delete'])->name('slides.delete');


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
