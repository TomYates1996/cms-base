<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;


class EventController extends Controller
{
    // Load Event Home
    public function load_events()
    {
        $events = Event::all();

        return Inertia::render('crm/events/EventsHome', [
            'events' => $events,
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
            'openings' => 'array|nullable',
            'start_datetime' => 'required|string',
            'end_datetime' => 'required|string',
            'recurrence' => 'nullable|string|in:none,daily,weekly,monthly,yearly,2weeks',
            'all_day' => 'nullable|boolean',
            'organiser_name' => 'nullable|string|max:255',
            'organiser_email' => 'nullable|email',
            'organiser_phone' => 'nullable|string|max:255',
            'venue_name' => 'nullable|string|max:255',
            'organiser_id' => 'nullable|exists:listings,id',
        ]);

        if ($request->hasFile('thumbnail_image') && $request->file('thumbnail_image')->isValid()) {
            $imagePath = $request->file('thumbnail_image')->store('crm/event/thumbnails', 'public');
            $validated['thumbnail_image'] = $imagePath;
        }
        
        $mediaPaths = [];
        
        if ($request->hasFile('media_gallery')) {
            foreach ($request->file('media_gallery') as $file) {
                $path = $file->store('crm/event/media_gallery', 'public');
                $mediaPaths[] = $path;
            }
            $validated['media_gallery'] = $mediaPaths;
        };

        $event = Event::create($validated);

        if (!empty($request->listing_ids)) {
            $event->relatedListings()->attach($request->listing_ids);
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
    
        $page = Page::where('slug', 'event')
            ->with('widgets.slides.image', 'headers.logo', 'footers.logo', 'footers.socialMedia', 'footers.widgets')
            ->first();

        if (!$page) {
            return Inertia::render('MissingPageNotice',  [
                'message' => 'Please create a page at /event - No page found.',
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

        $data = Event::where('slug', $slug)
         ->with(['listing' => function ($query) {
            $query->select('listings.id', 'title', 'slug', 'short_description', 'thumbnail_image');
        }])
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
        return Event::all();
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
