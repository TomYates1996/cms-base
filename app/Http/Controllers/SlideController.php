<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;
use Inertia\Inertia;


class SlideController extends Controller
{
    public function load()
    {
        return Inertia::render('cms/Slides', [
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_alt' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('slides', 'public');
        }
         // Create the slide
         $slide = Slide::create([
            'title' => $request->title,
            'image_path' => $imagePath,
            'image_alt' => $request->image_alt,
            'description' => $request->description,
            'link' => $request->link,
        ]);

        return;
    }

    // Get all the slides

    public function index()
    {
        return response()->json([
            'slides' => Slide::all(),
        ]);
    }
}
