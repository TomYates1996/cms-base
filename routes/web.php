<?php

use Inertia\Inertia;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\FooterController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HeaderController;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\JpegEncoder;


Route::get('/test-image', function () {
    $path = public_path('storage/slides/bypm6BMolmFMWrKsiqcoSV6Q6yzT11vcEfrWGs93.jpg');

    $img = Image::read($path)->resize(300, 200);

    return response($img->encode(new JpegEncoder()))
        ->header('Content-Type', 'image/jpeg');
});

Route::get('/resize/{path}', function ($path) {
    $width = request('w');
    $height = request('h');

    $storagePath = public_path('storage/' . $path);

    if (!file_exists($storagePath)) {
        abort(404, 'Image not found: ' . $path);
    }

    $img = Image::read($storagePath);
    $originalWidth = $img->width();
    $originalHeight = $img->height();

    if ($width && $height) {
        $targetRatio = $width / $height;
        $originalRatio = $originalWidth / $originalHeight;

        if ($originalRatio > $targetRatio) {
            $img->scale(height: $height);
            $cropX = intval(($img->width() - $width) / 2);
            $img->crop($width, $height, $cropX, 0);
        } else {
            $img->scale(width: $width);
            $cropY = intval(($img->height() - $height) / 2);
            $img->crop($width, $height, 0, $cropY);
        }
    } elseif ($width) {
        $img->scale(width: $width);
    } elseif ($height) {
        $img->scale(height: $height);
    }

    return response($img->encode(new JpegEncoder()))
        ->header('Content-Type', 'image/jpeg');
})->where('path', '.*');

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

Route::get('api/pages', [PageController::class, 'index']);
Route::get('api/pages', [PageController::class, 'index']);

Route::middleware('auth')->prefix('cms')->group(function () {
    Route::get('/', fn() => redirect()->route('cms.dashboard'));
    Route::get('/dashboard', [PageController::class, 'load_dashboard'])->name('cms.dashboard');
    Route::get('/slides/edit/{slide_slug}', [SlideController::class, 'load_edit'])->name('api.slides.load.edit');
    Route::get('/slides', [SlideController::class, 'load'])->name('slides.load');
    Route::get('/pages', [PageController::class, 'load'])->name('pages.load');
    Route::delete('/pages/delete/{page_id}', [PageController::class, 'destroy'])->name('pages.delete');
    Route::delete('/layouts/delete/{layout_id}', [LayoutController::class, 'destroy'])->name('layouts.delete');
    Route::delete('/blog/delete/{blog_id}', [BlogController::class, 'destroy'])->name('blog.delete');
    Route::get('/pages/children/{page_slug}', [PageController::class, 'children'])->where('page_slug', '.*')->name('pages.children');
    Route::get('/pages/edit/{page_slug}', [PageController::class, 'load_edit'])->name('pages.load.edit');
    Route::get('/pages/edit-content/{page_slug}', [PageController::class, 'load_edit_content'])
        ->where('page_slug', '.*') 
        ->name('pages.load.edit.content');
    Route::get('/layouts/edit-content/{id}', [LayoutController::class, 'load_edit_content'])->name('layouts.load.edit.content');
    Route::get('/layouts', [PageController::class, 'load_layouts'])->name('pages.load.layouts');
    Route::get('/layouts/index', [LayoutController::class, 'index'])->name('layouts.index');
    Route::get('/blog/edit-content/{id}', [BlogController::class, 'load_edit_content'])->name('blogs.load.edit.content');
    Route::get('/blog', [PageController::class, 'load_blogs'])->name('pages.load.blogs');
    Route::get('/blog/index', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/pages/primary', [PageController::class, 'load_primary'])->name('pages.load.primary');
    Route::get('/pages/secondary', [PageController::class, 'load_secondary'])->name('pages.load.secondary');
    Route::get('/pages/footer', [PageController::class, 'load_footer'])->name('pages.load.footer');
    Route::post('/pages/save', [PageController::class, 'save'])->name('page.save');
    Route::post('/layouts/save', [LayoutController::class, 'save'])->name('layout.save');
    Route::post('/blog/post/save', [BlogController::class, 'save'])->name('blog.save');
    Route::get('/images', [ImageController::class, 'load'])->name('images.load');
    Route::get('/images/edit', [ImageController::class, 'load_edit'])->name('images.load.edit');
    Route::post('/images/update', [ImageController::class, 'update'])->name('images.update');
    Route::get('/slides/new', [SlideController::class, 'load_create'])->name('slides.load.create');
    Route::delete('/slides/delete/{id}', [SlideController::class, 'delete'])->name('slides.delete');
    Route::get('/pages/{pageId}/widgets', [WidgetController::class, 'index'])->name('widgets.index');
    Route::get('/pages/{pageId}/widgets/create', [WidgetController::class, 'create'])->name('widgets.create');
    Route::post('/pages/{pageId}/widgets', [WidgetController::class, 'store'])->name('widgets.store');
    Route::get('/widgets/{id}/edit', [WidgetController::class, 'edit'])->name('widgets.edit');
    Route::get('/crm/listings', [ListingController::class, 'load_listings'])->name('pages.load.listings');
    Route::get('/crm/listings/index', [ListingController::class, 'index'])->name('crm.listings.index');
    Route::get('/crm/events/index', [EventController::class, 'index'])->name('crm.events.index');
    Route::get('/crm/events', [EventController::class, 'load_events'])->name('pages.load.events');
});

Route::middleware('auth')->group(function () {
    Route::put('/pages/update/{slug}', [PageController::class, 'update'])->name('api.pages.update');
    Route::post('/pages/store', [PageController::class, 'store'])->name('api.pages.store');
    Route::post('/layout/store', [LayoutController::class, 'store'])->name('api.layout.store');
    Route::post('/blog/store', [BlogController::class, 'store'])->name('api.blogs.store');
    Route::post('/cms/crm/listing/store', [ListingController::class, 'store'])->name('api.listing.store');
    Route::post('/cms/crm/event/store', [EventController::class, 'store'])->name('api.event.store');
    Route::post('/pages/link/store', [PageController::class, 'store_link'])->name('api.pages.link.store');
    Route::get('/api/slides', [SlideController::class, 'index'])->name('api.slides.index');
    Route::post('/api/slides', [SlideController::class, 'store'])->name('api.slides.store');
    Route::put('/api/slides', [SlideController::class, 'update'])->name('api.slides.update');
    Route::post('/create/new-image', [ImageController::class, 'store'])->name('cms.image.store');
    Route::get('/api/images/all', [ImageController::class, 'index'])->name('cms.image.index');
    Route::get('/api/pages/all', [PageController::class, 'index_all'])->name('cms.page.index');
    Route::put('/widgets/{widget}/save', [WidgetController::class, 'save_widget'])->name('widgets.save');
    Route::put('/header/{header}/save', [HeaderController::class, 'save_header'])->name('header.save');
    Route::put('/footer/{footer}/save', [FooterController::class, 'save_footer'])->name('footer.save');
    Route::post('/item/delete-save', [WidgetController::class, 'delete_save_item'])->name('widgets.delete-save');
    Route::post('/item/update-save', [WidgetController::class, 'update_save_item'])->name('widgets.update-save');
    Route::post('/widgets/create-save', [WidgetController::class, 'create_save_widget'])->name('widgets.create-save');
    Route::delete('/widget/delete/{widget}', [WidgetController::class, 'delete_saved_widget'])->name('widgets.delete.save');
    Route::post('/widget/unsave/{widget}', [WidgetController::class, 'unsave_widget'])->name('widgets.unsave');
    Route::post('/header/create-save', [WidgetController::class, 'create_save_header'])->name('header.create-save');
    Route::post('/footer/create-save', [FooterController::class, 'create_save'])->name('footer.create-save');
    Route::get('/api/social-media', [SocialMediaController::class, 'index'])->name('api.social.index');
    Route::post('/api/social-media/store', [SocialMediaController::class, 'store'])->name('api.social.store');
    Route::post('/api/store/cta', [WidgetController::class, 'cta_test'])->name('api.create.cta.test');
});



Route::get('/event/{slug}', [EventController::class, 'load_page'])->where('slug', '.*')->name('event.load');
Route::get('/listing/{slug}', [ListingController::class, 'load_page'])->where('slug', '.*')->name('listing.load');
Route::get('/blog/post/{slug}', [PageController::class, 'blog_post'])->where('slug', '.*')->name('page.blog.post');
Route::get('/{slug}', [PageController::class, 'show'])->where('slug', '.*')->name('page.show');
