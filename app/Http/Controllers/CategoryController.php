<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Inertia\Inertia;


class CategoryController extends Controller
{
    public function index() 
    {
        return response()->json([
            'categories' => Category::with('subcategories')->get(),
        ]);
    }

    public function load_categories()
    {
        $categories = Category::with('subcategories')->get();

        return Inertia::render('crm/categories/CategoriesHome', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string',
        ]);
        
        $validated['slug'] = trim($validated['slug'], '/');
        $category = Category::create($validated);

    return;
    }

    public function store_subcat(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string',
            'parent_cat_id' => 'required|integer',
        ]);
        
        $validated['slug'] = trim($validated['slug'], '/');
        $subcategory = SubCategory::create($validated);

    return;
    }
}
