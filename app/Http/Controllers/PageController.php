<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Widget;
use App\Models\Header;
use App\Models\Footer;
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





    // Load the CMS Page Children page page
    public function children(Request $request, $slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $children = $page->children;

        return Inertia::render('cms/pages/Pages', [
            'pages' => $children,
            'parent' => $page,
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
        $page = Page::where('slug', $slug)->with('widgets.slides.image', 'headers.logo', 'footers.logo')->firstOrFail(); 

        $pages = Page::where('show_in_nav', true)->get()->groupBy('section');

        $headers = $page->headers->map(function ($header) use ($pages) {
            $header->pages = $pages[$header->section] ?? collect();
            return $header;
        });

        $footers = $page->footers;

        $savedWidgets = Widget::with('slides.image')->where('is_saved', true)->whereNull('page_id')->get();
        $savedHeaders = Header::with('logo')->where('is_saved', true)->whereNull('page_id')->get()->map(function ($header) use ($pages) {
            $header->pages = $pages[$header->section] ?? collect();
            return $header;
        });;
        $savedFooters = Footer::with('logo')
            ->where('is_saved', true)
            ->whereNull('page_id')
            ->get()
            ->map(function ($footer) use ($pages) {
                $footer->pages = $pages[$footer->section] ?? collect();
                return $footer;
            });

        return Inertia::render('cms/pages/EditContent', [
            'pages' => $pages,
            'page' => $page,
            'widgets' => $page->widgets,
            'savedWidgets' => $savedWidgets,
            'savedHeaders' => $savedHeaders,
            'savedFooters' => $savedFooters,
            'headers' => $headers,
            'footers' => $footers,
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

    // Load a page
    public function show($slug)
    {
        $slug = ltrim($slug, '/');
        $page = Page::where('slug', $slug)->with('widgets.slides.image', 'headers.logo')->firstOrFail();

        $navPages = Page::where('show_in_nav', true)->get()->groupBy('section');

        $headers = $page->headers->map(function ($header) use ($navPages) {
            $header->pages = $navPages[$header->section] ?? collect();
            return $header;
        });

        $page->increment('views');

        return Inertia::render('Welcome', [
            'page' => $page,
            'pages' => $navPages,
            'widgets' => $page->widgets,
            'headers' =>$headers,
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

        return;
    }

    public function destroy(Request $request, $id) 
    {
        $user = auth()->user();
        if ($user) {
            $page = Page::findOrFail($id);
           
            $children = Page::where('parent_id', $page->id)->get();
           
            foreach ($children as $child) {
                $grandchildren = Page::where('parent_id', $child->id)->get();
                foreach ($grandchildren as $grandchild) {
                    $grandchild->widgets()->delete();
                    $grandchild->headers()->delete();
                    $grandchild->delete();
                }
                $child->widgets()->delete();
                $child->headers()->delete();
                $child->delete();
            }

            $page->widgets()->delete();
            $page->headers()->delete();
        
            $page->delete();
        } else {
            return;
        }
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
}
