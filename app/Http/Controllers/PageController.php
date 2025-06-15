<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Widget;
use App\Models\Header;
use App\Models\Slide;
use App\Models\Listing;
use App\Models\Event;
use App\Models\Image;
use App\Models\Footer;
use App\Models\Layout;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use App\Models\Blog;
use Inertia\Inertia;

class PageController extends Controller
{
    // Load the CMS Page Page
    public function load()
    {

        $pages = Page::where('level', 1)->get();

        return Inertia::render('cms/pages/PageSections', [
        ]);
    }
    // Load the CMS Primary Page
    public function load_primary()
    {

        $pages = Page::where('level', 1)->where('section', 'primary')->get();

        return Inertia::render('cms/pages/Pages', [
            'pages' => $pages,
            'parent' => null,
            'section' => 'primary',
        ]);
    }
    // Load the CMS Secondary Page
    public function load_secondary()
    {

        $pages = Page::where('level', 1)->where('section', 'secondary')->get();

        return Inertia::render('cms/pages/Pages', [
            'pages' => $pages,
            'parent' => null,
            'section' => 'secondary',
        ]);
    }
    // Load the CMS Footer Page
    public function load_footer()
    {

        $pages = Page::where('level', 1)->where('section', 'footer')->get();

        return Inertia::render('cms/pages/Pages', [
            'pages' => $pages,
            'parent' => null,
            'section' => 'footer',
        ]);
    }

    // Load Layouts
    public function load_layouts()
    {
        $layouts = Layout::all();

        return Inertia::render('cms/pages/Layouts', [
            'layouts' => $layouts,
        ]);
    }

    // Load Blog
    public function load_blogs()
    {
        $blogs = Blog::all();

        return Inertia::render('cms/pages/Blogs', [
            'blogPosts' => $blogs,
        ]);
    }





    // Load the CMS Page Children page page
    public function children(Request $request, $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $children = $page->children;

        return Inertia::render('cms/pages/Pages', [
            'pages' => $children,
            'parent' => $page,
            'section' => $page['section'],
        ]);
    }
    // Load the CMS Page edit Page
    public function load_edit(Request $request, $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return Inertia::render('cms/pages/Edit', [
            'page' => $page,
        ]);
    }
    // Load the CMS Page Edit Content Page
    public function load_edit_content(Request $request, $slug)
    {
        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');

        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }

        $page = Page::where('slug', $slug)
            ->with('widgets.slides.image', 'headers.logo', 'footers.logo', 'footers.socialMedia', 'footers.widgets')
            ->firstOrFail();

        $header = $page->headers->first();
        $footer = $page->footers->first();

        // Load the saved widgets
        $savedWidgets = Widget::with('slides.image')
            ->where('is_saved', true)
            ->whereNull('page_id')
            ->get();


        $savedHeaders = Header::with('logo')
            ->where('is_saved', true)
            ->whereNull('page_id')
            ->get()
            ->map(function ($header) use ($formattedPages) {
                $header->pages = $formattedPages[$header->section] ?? collect();
                return $header;
            });

        $savedFooters = Footer::with('logo', 'socialMedia', 'widgets.slides.image')
            ->where('is_saved', true)
            ->whereNull('page_id')
            ->get()
            ->map(function ($footer) use ($formattedPages) {
                $footer->pages = $formattedPages[$footer->section] ?? collect();
                return $footer;
            });

        if ($header) {
            $header->pages = $formattedPages[$header->section] ?? collect();
            $header->hamburger_pages = $formattedPages[$header->section_hamburger] ?? collect();
        }

        return Inertia::render('cms/pages/EditContent', [
            'pages' => $formattedPages,
            'page' => $page,
            'widgets' => $page->widgets,
            'savedWidgets' => $savedWidgets,
            'savedHeaders' => $savedHeaders,
            'savedFooters' => $savedFooters,
            'header' => $header,
            'footer' => $footer,
            'layout' => false,
        ]);
    }
    
    // Grab all the pages that exist on the database
    public function index_all()
    {
        $pages = Page::all();
        
        return response()->json([
            'pages' => $pages,
        ]);
    }
    // Grab all the pages that have show on nav
    public function index()
    {
        $pages = Page::where('show_in_nav', true)->get();
        
        return response()->json([
            'pages' => $pages,
        ]);
    }
    
    public function show($slug)
    {
        $slug = ltrim($slug, '/');
    
        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');
    
        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }
    
        $page = Page::where('slug', $slug)
            ->with('widgets.categories.subcategories', 'widgets.slides.image', 'headers.logo', 'footers.logo', 'footers.socialMedia', 'footers.widgets')
            ->firstOrFail();

        foreach ($page->widgets as $widget) {
            if ($widget->feed_type === 'blog') {
                $blogs = Blog::latest()->take($widget->to_show ?? 4)->get();
                
                $virtualSlides = $blogs->map(function ($blog) {
                    $slide = new Slide();
                    $slide->title = $blog->title;
                    $slide->description = $blog->excerpt ?? '';
                    $basePath = config('cms.blog_page', 'blog');
                    $slide->link = url("/{$basePath}/post/{$blog->slug}");
                    $slide->image_id = $blog->image_id;
                    $slide->setRelation('image', $blog->image); 
                    return $slide;
                });

                $widget->setRelation('slides', $virtualSlides);
            } else if ($widget->feed_type === 'listings') {
                $listings = Listing::inRandomOrder()->take($widget->to_show ?? 4)->get();

                $virtualSlides = $listings->map(function ($listing) {
                    $slide = new Slide();
                    $slide->title = $listing->title;
                    $slide->description = $listing->short_description ?? '';
                    $slide->link = url("/listing/{$listing->slug}");

                    $image = new Image();
                    $image->image_path = $listing->thumbnail_image ?? config('global.placeholder_image.path');
                    $image->image_alt = $listing->title ?? config('global.placeholder_image.alt');
                    $slide->setRelation('image', $image); 
                    return $slide;
                });

                $widget->setRelation('slides', $virtualSlides);
            } else if ($widget->feed_type === 'events') {
                $events = Event::inRandomOrder()->take($widget->to_show ?? 4)->get();

                $virtualSlides = $events->map(function ($event) {
                    $slide = new Slide();
                    $slide->title = $event->title;
                    $slide->description = $event->short_description ?? '';
                    $slide->link = url("/event/{$event->slug}");
                    $slide->startDate = $event->start_datetime;
                    $slide->endDate = $event->end_datetime;

                    $image = new Image();
                    $image->image_path = $event->thumbnail_image ?? config('global.placeholder_image.path');
                    $image->image_alt = $event->title ?? config('global.placeholder_image.alt');
                    $slide->setRelation('image', $image); 
                    return $slide;
                });

                $widget->setRelation('slides', $virtualSlides);
            } else if ($widget->feed_type === 'products') {
                // Load variant items with their parent product variant

                $productVariants = ProductVariant::with('items')->inRandomOrder()->take($widget->to_show ?? 4)->get();

                $virtualSlides = $productVariants->map(function ($variant) {
                    $slide = new Slide();
                    $slide->title = $variant->name;
                    $slide->description = $variant->short_description ?? '';

                    // Price range
                    $prices = $variant->items->pluck('price');
                    $minPrice = $prices->min();
                    $maxPrice = $prices->max();
                    $slide->price_range = $minPrice == $maxPrice ? "£$minPrice" : "£$minPrice - £$maxPrice";

                    $slide->link = url("/product/{$variant->id}");

                    $image = new Image();
                    $image->image_path = $variant->thumbnail_image ?? config('global.placeholder_image.path');
                    $image->image_alt = $variant->name ?? config('global.placeholder_image.alt');
                    $slide->setRelation('image', $image);

                    return $slide;
                });


                $widget->setRelation('slides', $virtualSlides);
            }
        }
    
        $header = $page->headers->first();
        $footer = $page->footers->first();
    
        if ($header) {
            $header->pages = $formattedPages[$header->section] ?? collect();
            $header->hamburger_pages = $formattedPages[$header->section_hamburger] ?? collect();
        }
    
        $page->increment('views');

        return Inertia::render('Welcome', [
            'page' => $page,
            'pages' => $formattedPages,
            'widgets' => $page->widgets,
            'header' => $header,
            'footer' => $footer,
        ]);
    }

    public function blog_post($slug)
    {
        $slug = ltrim($slug, '/');
    
        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');
    
        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }
    
        $page = Page::where('slug', 'blog/post')
            ->with('widgets.slides.image', 'headers.logo', 'footers.logo', 'footers.socialMedia', 'footers.widgets')
            ->firstOrFail();

        $blog = Blog::where('slug', $slug)
            ->with('widgets.slides.image')
            ->firstOrFail();

        $pageWidgets = $page->widgets;
        $blogWidgets = $blog->widgets;

        $finalWidgets = collect();

        foreach ($pageWidgets as $widget) {
            if ($widget->variant === 'blog_post') {
                foreach ($blogWidgets as $blogWidget) {
                    $finalWidgets->push($blogWidget);
                }
            } else {
                $finalWidgets->push($widget);
            }
        }

        $header = $page->headers->first();
        $footer = $page->footers->first();
    
        if ($header) {
            $header->pages = $formattedPages[$header->section] ?? collect();
            $header->hamburger_pages = $formattedPages[$header->section_hamburger] ?? collect();
        }
    
        $page->increment('views');
    
        return Inertia::render('Welcome', [
            'page' => $page,
            'pages' => $formattedPages,
            'widgets' => $finalWidgets,
            'header' => $header,
            'footer' => $footer,
        ]);
    }
    
    // Update a page
    public function update(Request $request, $slug)
    {
        
        $page = Page::where('slug', $slug)->firstOrFail();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string',
            'show_in_nav' => 'boolean',
        ]);
        
        $validated['slug'] = ltrim($validated['slug'], '/');
        
        
        $page->update($validated);
        
        return Inertia::render('cms/pages/Pages', [
        ]);
    }
    
    // Create a new page
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string',
            'show_in_nav' => 'boolean',
            'parent' => 'nullable|array',
            'section' => 'required|in:primary,secondary,footer',
        ]);
        
        $parent = $validated['parent'] ?? null;
        $validated['parent_id'] = $parent['id'] ?? null;
        
        $childSlug = trim($validated['slug'], '/');
        
        if ($parent) {
            $parentSlug = $parent['slug'];
            $validated['slug'] = $parentSlug . '/' . $childSlug;
            $validated['level'] = ($parent['level'] ?? 0) + 1;
            $validated['section'] = $parent['section'];
        } else {
            $validated['slug'] = $childSlug;
            $validated['level'] = 1;
        }
        
        unset($validated['parent']);
        $validated['created_by'] = $user->id;

        $page = Page::create($validated);

        if ($request->filled('layout')) {
            $layout = Layout::with('widgets.slides', 'header', 'footer')->find($request->layout);

        if ($layout) {
            // Clone or attach layout widgets
            foreach ($layout->widgets as $index => $layoutWidget) {
                if ($layoutWidget->is_saved) {
                    $page->widgets()->attach($layoutWidget->id, ['position' => $index + 1]);
                } else {
                    $clonedWidget = $layoutWidget->replicate();
                    $clonedWidget->is_saved = false;
                    $clonedWidget->save();

                    // Attach slides to the cloned widget
                    $slideIds = $layoutWidget->slides->pluck('id')->toArray();
                    if (!empty($slideIds)) {
                        $clonedWidget->slides()->sync($slideIds);
                    }

                    $page->widgets()->attach($clonedWidget->id, ['position' => $index + 1]);
                }
            }

            // Attach layout's header if it exists
            if ($layout->header) {
                $page->headers()->attach($layout->header->id);
            }

            // Attach layout's footer if it exists
            if ($layout->footer) {
                $page->footers()->attach($layout->footer->id);
            }

            return; // End here if layout was used
        }
    }

    // No layout used — create empty header/footer
    $header = Header::create(); 
    $footer = Footer::create();

    $page->headers()->attach($header->id);
    $page->footers()->attach($footer->id);

    return;
    }
    // Create a new page link
    public function store_link(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'id' => 'required|integer',
            'title' => 'required|string|max:255',
            'section' => 'required|in:primary,secondary,footer',
        ]);
        
        $pageToCopy = Page::findOrFail($validated['id']);
        
        $newPage = new Page([
            'is_link' => true,
            'linked_page_id' => $pageToCopy['id'],
            'slug' => $pageToCopy['slug'],
            'title' => $validated['title'],
            'section' => $validated['section'],
            'created_by' => $user->id,
        ]);
        
        $newPage->save();
        
        return;
    }
    
    public function destroy(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user) {
            return;
        }

        $page = Page::findOrFail($id);

        // Get children and grandchildren
        $children = Page::where('parent_id', $page->id)->get();

        foreach ($children as $child) {
            $grandchildren = Page::where('parent_id', $child->id)->get();

            foreach ($grandchildren as $grandchild) {
                $this->detachAndDeleteItems($grandchild);
                $grandchild->delete();
            }

            $this->detachAndDeleteItems($child);
            $child->delete();
        }

        $this->detachAndDeleteItems($page);
        $page->delete();
    }
    
    // Load CMS Dashboard
    public function load_dashboard()
    {
        $totalViews = Page::sum('views');
        
        $topPages = \App\Models\Page::orderByDesc('views')
        ->take(5)
        ->get(['title', 'slug', 'views']);
        
        return Inertia::render('Dashboard', [
            'topPages' => $topPages,
            'totalViews' => $totalViews,
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

    public function save(Request $request)
    {
        $request->validate([
            'page_id' => 'required|integer',
            'widgets' => 'nullable|array',
            'widgets.*.title' => 'nullable|string',
            'widgets.*.subtitle' => 'nullable|string',
            'widgets.*.description' => 'nullable|string',
            'widgets.*.selected_categories' => 'nullable|array',
            'widgets.*.type' => 'required|string',
            'widgets.*.variant' => 'required|string',
            'widgets.*.slides' => 'nullable|array', 
            'widgets.*.slides.*.id' => 'integer|exists:slides,id',

            'header.logo.id' => 'nullable|exists:images,id',
            'header.link' => 'nullable|string',
            'header.section' => 'required|in:primary,secondary,footer',
            'header.section_hamburger' => 'required|in:primary,secondary,footer',
            'header.menu_type' => 'required|in:dropdown,hamburger',
            
            'footer.logo.id' => 'nullable|exists:images,id',
            'footer.section' => 'required|in:primary,secondary,footer',
            'footer.social_media' => 'nullable|array',
            'footer.social_media.*.id' => 'integer|exists:social_media_links,id',
        ]);

        $widgets = $request->input('widgets');
        $headerInput = $request->input('header');
        $headerData = Header::find($headerInput['id']);
        $page_id = $request->input('page_id');
        $page = Page::with('headers', 'footers')->find($page_id);

        $incomingWidgetIds = collect($widgets)->pluck('id')->filter()->toArray(); 
        $currentWidgetIds = $page->widgets()->pluck('widgets.id')->toArray();
        $widgetsToDetach = array_diff($currentWidgetIds, $incomingWidgetIds);

        if (!empty($widgetsToDetach)) {
            $page->widgets()->detach($widgetsToDetach);
        }

        Widget::whereIn('id', $widgetsToDetach)
            ->where('is_saved', false)
            ->get()
            ->each(function ($widget) {
                if ($widget->pages()->count() === 0) {
                    $widget->delete();
                }
            });

        foreach ($widgets as $index => $widgetData) {
            if (isset($widgetData['id'])) {
                $existingWidget = Widget::find($widgetData['id']);
                $existingWidget->update([
                    'title' => $widgetData['title'] ?? null,
                    'description' => $widgetData['description'] ?? null,
                    'subtitle' => $widgetData['subtitle'] ?? null,
                    'link' => $widgetData['link'] ?? null,
                    'link_text' => $widgetData['link_text'] ?? null,
                    'slide_link_text' => $widgetData['slide_link_text'] ?? null,
                    'content' => $widgetData['content'] ?? null,
                    'feed_type' => $widgetData['feed_type'] ?? null,
                    'to_show' => $widgetData['to_show'] ?? null,
                ]);
                $widget = $existingWidget;
            } else {
                $widget = Widget::create($widgetData);
            }

            $widget->pages()->syncWithoutDetaching([$page_id => ['position' => $index + 1]]);

            if (isset($widgetData['slides']) && is_array($widgetData['slides'])) {
                $slideIds = array_column($widgetData['slides'], 'id');

                if ($widget->wasRecentlyCreated) {
                    $widget->slides()->attach($slideIds); 
                } else {
                    $widget->slides()->sync($slideIds); 
                }
            }

            // Sync selected categories
            if (isset($widgetData['selected_categories']) && is_array($widgetData['selected_categories'])) {
                $widget->categories()->sync($widgetData['selected_categories']);
            }
        }

        // HEADER update logic (unchanged)
        $existingHeader = $page->headers()->where('header_id', $headerData->id)->first();
        if ($existingHeader) {
            $headerData->update([
                'logo_image_id' => $headerInput['logo']['id'] ?? null,
                'link' => $headerInput['link'] ?? null,
                'section' => $headerInput['section'],
                'menu_type' => $headerInput['menu_type'],
                'section_hamburger' => $headerInput['section_hamburger'],
            ]);
        } else {
            $currentHeader = $page->headers()->first();
            if ($currentHeader) {
                $page->headers()->detach($currentHeader->id);
                if ($currentHeader->is_saved === false) {
                    $currentHeader->delete();
                }
            }
            $page->headers()->attach($headerData->id);
            $headerData->update([
                'logo_image_id' => $headerInput['logo']['id'] ?? null,
                'link' => $headerInput['link'] ?? null,
                'section' => $headerInput['section'],
                'menu_type' => $headerInput['menu_type'],
                'section_hamburger' => $headerInput['section_hamburger'],
            ]);
        }

        // FOOTER logic
        $footerInput = $request->input('footer');
        $footerData = Footer::find($footerInput['id']);
        $currentFooter = $page->footers()->first();

        if (!$page->footers->contains($footerInput['id'])) {
            if ($currentFooter) {
                $page->footers()->detach($currentFooter->id);
                if (!$currentFooter->is_saved) {
                    $currentFooter->delete();
                }
            }
            $page->footers()->attach($footerInput['id']);
        }

        $footerData->update([
            'section' => $footerInput['section'] ?? $footerData->section,
        ]);

        if (isset($footerInput['social_media']) && is_array($footerInput['social_media'])) {
            $socialMediaIds = collect($footerInput['social_media'])->pluck('id')->toArray();
            $footerData->socialMedia()->sync($socialMediaIds);
        }

        if (isset($footerInput['logo']['id'])) {
            $footerData->logo_id = $footerInput['logo']['id'];
            $footerData->save();
        }

        foreach ($footerInput['widgets'] as $widget) {
            if (isset($widget['id'])) {
                $existingWidget = Widget::find($widget['id']);

                if ($existingWidget && !$footerData->widgets->contains($existingWidget->id)) {
                    $footerData->widgets()->attach($existingWidget->id);
                }

                if (isset($widget['selected_categories']) && is_array($widget['selected_categories'])) {
                    $existingWidget->categories()->sync($widget['selected_categories']);
                }

            } else {
                $newWidget = Widget::create([
                    'title' => $widget['title'] ?? null,
                    'type' => $widget['type'] ?? null,
                    'variant' => $widget['variant'] ?? null,
                    'is_saved' => false,
                    'description' => $widget['description'] ?? null,
                    'link' => $widget['link'] ?? null,
                    'link_text' => $widget['link_text'] ?? null,
                    'slide_link_text' => $widget['slide_link_text'] ?? null,
                    'content' => $widget['content'] ?? null,
                    'feed_type' => $widget['feed_type'] ?? null,
                    'to_show' => $widget['to_show'] ?? null,
                ]);

                $footerData->widgets()->attach($newWidget->id);

                if (isset($widget['selected_categories']) && is_array($widget['selected_categories'])) {
                    $newWidget->categories()->sync($widget['selected_categories']);
                }

                if (isset($widget['slides']) && is_array($widget['slides'])) {
                    foreach ($widget['slides'] as $slideData) {
                        $slide = Slide::find($slideData['id']);
                        if (!$slide) continue;
                        if (!$newWidget->slides->contains($slide->id)) {
                            $newWidget->slides()->attach($slide->id);
                        }
                    }
                }
            }
        }

        return;
    }


    private function detachAndDeleteItems($page)
    {
        // Widgets
        foreach ($page->widgets as $widget) {
            $page->widgets()->detach($widget->id);
            if ($widget->pages()->count() === 0) {
                $widget->delete();
            }
        }

        // Headers
        foreach ($page->headers as $header) {
            $page->headers()->detach($header->id);
            if ($header->pages()->count() === 0) {
                $header->delete();
            }
        }

        // Footers
        foreach ($page->footers as $footer) {
            $page->footers()->detach($footer->id);
            if ($footer->pages()->count() === 0) {
                $footer->delete();
            }
        }
    }
}
