<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;


class ListingController extends Controller
{
    // Load Listings Home
    public function load_listings()
    {
        $listings = Listing::all();

        return Inertia::render('crm/listings/ListingsHome', [
            'listings' => $listings,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:400',
            'category' => 'nullable|string|max:255',
            'sub_category' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'region' => 'nullable|string',
            'country' => 'nullable|string',
            'postcode' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'media_gallery' => 'nullable|array',
            'media_gallery.*' => 'image|max:2048',
            'thumbnail_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'opening_hours' => 'nullable|array',
            'prices' => 'nullable|array',
            'booking_url' => 'nullable|url',
            'reservation_email' => 'nullable|email',
            'featured' => 'boolean',
            'owner_id' => 'nullable|integer|exists:users,id',
            'published_at' => 'nullable|date',
            'social_links' => 'nullable|array',
            'amenities' => 'nullable|array',
            'accessibility_info' => 'nullable|array',
            'listing_ids' => 'nullable|array',
            'listing_ids.*' => 'integer|exists:listings,id',
        ]);
        
        if ($request->hasFile('thumbnail_image') && $request->file('thumbnail_image')->isValid()) {
            $imagePath = $request->file('thumbnail_image')->store('crm/listing/thumbnails', 'public');
            $validated['thumbnail_image'] = $imagePath;
        }
        
        $mediaPaths = [];
        
        if ($request->hasFile('media_gallery')) {
            foreach ($request->file('media_gallery') as $file) {
                $path = $file->store('crm/listing/media_gallery', 'public');
                $mediaPaths[] = $path;
            }
            $validated['media_gallery'] = $mediaPaths;
        };
        
        $listing = Listing::create($validated);

        if (!empty($request->listing_ids)) {
            $listing->relatedListings()->attach($request->listing_ids);
        }

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
            ->firstOrFail();

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

        $data = Listing::where('slug', $slug)
        ->with(['relatedListings' => function ($query) {
            $query->select('listings.id', 'title', 'slug', 'short_description', 'thumbnail_image');
        }, 
        'events'
        ])
        ->first();

        return Inertia::render('ProductPage', [
            'data' => $data,
            'header' => $header,
            'footer' => $footer,
            'pages' => $formattedPages,
        ]);
    }

    public function index()
    {
        return Listing::all();
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
}
