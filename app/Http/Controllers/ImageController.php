<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;


class ImageController extends Controller
{
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
