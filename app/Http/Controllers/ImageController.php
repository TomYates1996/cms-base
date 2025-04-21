<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImageController extends Controller
{
    // Load the CMS Images Page
    public function load()
    {
    
        $images = Image::all();

        return Inertia::render('cms/Images', [
            'images' => $images,
        ]);
    }

    public function load_edit(Request $request)
    {
        $imageId = $request->input('image_id');

        $image = Image::find($imageId);

        return Inertia::render('cms/EditImage', [
            'image' => $image,
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'credits' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'image_alt' => 'nullable|string|max:255',
            'image_path' => 'nullable|string|max:255',
            'id' => 'required|numeric',
        ]);

        $id = $validatedData['id'];

        $image = Image::findOrFail($id);
    
        if ($request->hasFile('image')) {
            $validatedData['image_path'] = $request->file('image')->store('slides', 'public');
        }

        unset($validatedData['id']);
    
        $image->update($validatedData);
    
        return;
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            'image_alt' => 'nullable|string',
            'title' => 'required|string|max:255',
            'credits' => 'nullable|string|max:255',
        ]);

        $imagePath = $request->file('image')->store('slides', 'public');

        $image = Image::create([
            'image_path' => $imagePath,
            'image_alt' => $request->input('image_alt'),
            'title' => $request->input('title'),
            'credits' => $request->input('credits'),
        ]);

        return;
    }

    public function index() 
    {
        return response()->json([
            'images' => Image::all(),
        ]);
    }
}
