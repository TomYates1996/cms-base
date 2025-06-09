<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    // Load Product Home
    public function load_products()
    {
        $products = Product::all();

        return Inertia::render('crm/products/ProductsHome', [
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string',
            'slug' => 'required|string|max:255|unique:products,slug',
            'category' => 'required|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'sub_category' => 'nullable|string',
            'variants' => 'required|array',
            'variants.*.label' => 'required|string|max:255',
            'variants.*.description' => 'nullable|string',
            'variants.*.short_description' => 'nullable|string|max:300',
            'variants.*.thumbnail_image' => 'nullable|image|max:10240',
            'variants.*.media_gallery' => 'nullable|array',
            'variants.*.media_gallery.*' => 'image|max:2048',
            'variants.*.items' => 'required|array|min:1',
            'variants.*.items.*.label' => 'required|string|max:255',
            'variants.*.items.*.sku' => 'required|string|distinct',
            'variants.*.items.*.price' => 'required|numeric|min:0',
            'variants.*.items.*.stock_quantity' => 'required|integer|min:0',
        ]);
        
        DB::transaction(function () use ($request, $validated) {
            // Handle product images
            $thumbnailPath = $request->hasFile('thumbnail_image')
            ? $request->file('thumbnail_image')->store('products/thumbnails', 'public')
            : null;
            
            $mediaGalleryPaths = [];
            if ($request->hasFile('media_gallery')) {
                foreach ($request->file('media_gallery') as $file) {
                    $mediaGalleryPaths[] = $file->store('products/media_gallery', 'public');
                }
            }
            
            // Create Product
            $product = Product::create([
                'label' => $validated['label'],
                'category' => $validated['category'],
                'sub_category' => $validated['sub_category'] ?? null,
                'slug' => $validated['slug'],
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
            ]);

            // Create Product Variants
            foreach ($validated['variants'] as $index => $variantData) {
                $variantThumbnail = null;
                $variantGallery = [];

                if (isset($request->variants[$index]['thumbnail_image'])) {
                    $variantThumbnail = $request->variants[$index]['thumbnail_image']->store('products/variant_thumbnails', 'public');
                }

                if (!empty($request->variants[$index]['media_gallery'])) {
                    foreach ($request->variants[$index]['media_gallery'] as $file) {
                        $variantGallery[] = $file->store('products/variant_gallery', 'public');
                    }
                }

                $variant = $product->variants()->create([
                    'name' => $variantData['label'],
                    'description' => $variantData['description'] ?? null,
                    'short_description' => $variantData['short_description'] ?? null,
                    'thumbnail_image' => $variantThumbnail,
                    'media_gallery' => $variantGallery,
                ]);

                // Create Variant Items
                foreach ($variantData['items'] as $item) {
                    $variant->items()->create([
                        'size_label' => $item['label'],
                        'sku' => $item['sku'],
                        'price' => $item['price'],
                        'stock_quantity' => $item['stock_quantity'],
                    ]);
                }
            }
        });

        return;
    }


    public function load_page($slug)
    {
        $slug = ltrim($slug, '/');
    
        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');
    
        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }
    
        $page = Page::where('slug', 'listing')
            ->with('widgets.slides.image', 'headers.logo', 'footers.logo', 'footers.socialMedia', 'footers.widgets')
            ->first();

        if (!$page) {
            return Inertia::render('MissingPageNotice',  [
                'message' => 'Please create a page at /product - No page found.',
            ]);
        };

        $pageWidgets = $page->widgets;

        $finalWidgets = collect();

        // foreach ($pageWidgets as $widget) {
        //     if ($widget->variant === 'blog_post') {
        //         foreach ($blogWidgets as $blogWidget) {
        //             $finalWidgets->push($blogWidget);
        //         }
        //     } else {
        //         $finalWidgets->push($widget);
        //     }
        // }

        $header = $page->headers->first();
        $footer = $page->footers->first();
    
        if ($header) {
            $header->pages = $formattedPages[$header->section] ?? collect();
            $header->hamburger_pages = $formattedPages[$header->section_hamburger] ?? collect();
        }
    
        // $page->increment('views');

        $data = Product::where('slug', $slug)
        ->with('variants.items')
        ->first();

        return Inertia::render('ShopProductPage', [
            'data' => $data,
            'header' => $header,
            'footer' => $footer,
            'pages' => $formattedPages,
        ]);
    }

    public function basket()
    {
        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');
    
        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }
    
        $page = Page::where('slug', 'basket')
            ->with('widgets.slides.image', 'headers.logo', 'footers.logo', 'footers.socialMedia', 'footers.widgets')
            ->first();

        if (!$page) {
            return Inertia::render('MissingPageNotice',  [
                'message' => 'Please create a page at /basket - No page found.',
            ]);
        };

        $pageWidgets = $page->widgets;

        $finalWidgets = collect();

        $header = $page->headers->first();
        $footer = $page->footers->first();
    
        if ($header) {
            $header->pages = $formattedPages[$header->section] ?? collect();
            $header->hamburger_pages = $formattedPages[$header->section_hamburger] ?? collect();
        }
    
        return Inertia::render('BasketPage', [
            'header' => $header,
            'footer' => $footer,
            'pages' => $formattedPages,
        ]);
    }

    public function checkout()
    {
        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');
    
        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }
    
        $page = Page::where('slug', 'checkout')
            ->with('widgets.slides.image', 'headers.logo', 'footers.logo', 'footers.socialMedia', 'footers.widgets')
            ->first();

        if (!$page) {
            return Inertia::render('MissingPageNotice',  [
                'message' => 'Please create a page at /checkout - No page found.',
            ]);
        };

        $pageWidgets = $page->widgets;

        $finalWidgets = collect();

        $header = $page->headers->first();
        $footer = $page->footers->first();
    
        if ($header) {
            $header->pages = $formattedPages[$header->section] ?? collect();
            $header->hamburger_pages = $formattedPages[$header->section_hamburger] ?? collect();
        }
    
        return Inertia::render('CheckoutPage', [
            'header' => $header,
            'footer' => $footer,
            'pages' => $formattedPages,
        ]);
    }

    private function buildTree($pages, $parentId = null) {
        $branch = [];
    
        foreach ($pages as $page) {
            // If parent_id is null or matches, treat as an array
            if ($page['parent_id'] === $parentId) {
                $children = $this->buildTree($pages, $page['id']);
                if ($children) {
                    $page['children'] = $children;
                }
                $branch[] = $page;
            }
        }
    
        return $branch;
    }

    public function destroy(Request $request, $id) 
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $product = Product::findOrFail($id);
    
        $product->delete();
    
        return;
    }

}
