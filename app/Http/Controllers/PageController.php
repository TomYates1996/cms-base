<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use Inertia\Inertia;

class PageController extends Controller
{
    // Grab all the pages that exist on the database
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
        $slug = '/' . ltrim($slug, '/');
        $page = Page::where('slug', $slug)->firstOrFail();
        $pages = Page::where('show_in_nav', true)->get();


        return Inertia::render('Welcome', [
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    // Update a page
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string',
            'show_in_nav' => 'boolean',
        ]);

        $validated['slug'] = '/' . ltrim($validated['slug'], '/');


        $page->update($validated);

        return;
    }

    // Create a new page
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string',
            'show_in_nav' => 'boolean',
        ]);

        $validated['slug'] = '/' . ltrim($validated['slug'], '/');
        $validated['created_by'] = $user['id'];

        $page = Page::create($validated);

        return;
    }
}
