<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use Inertia\Inertia;

class PageController extends Controller
{
    // Load the CMS Page Page
    public function load()
    {

        $pages = Page::where('level', 1)->get();

        return Inertia::render('cms/pages/Pages', [
            'pages' => $pages,
            'parent' => null,
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

        return Inertia::render('cms/pages/EditContent', [
            'pages' => $pages,
            'page' => $page,
            'widgets' => $page->widgets,
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
            'parent' => 'nullable|array'
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
}
