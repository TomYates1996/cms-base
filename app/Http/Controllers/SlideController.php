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
            'description' => 'required|string',
            'link' => 'nullable|url',
            'image_id' => 'nullable|integer',
        ]);

        $slide = Slide::create([
            'title' => $request->title,
            'image_id' => $request->image_id,
            'description' => $request->description,
            'link' => $request->link,
        ]);

        return;
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_alt' => 'nullable|string|max:255',
            'description' => 'nullable |string',
            'link' => 'nullable|url',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('slides', 'public');
        }

        $slide = Slide::find($request->id);

        $slide->title = $request->title;
        $slide->image_path = $request->image;
        $slide->image_alt = $request->image_alt;
        $slide->description = $request->description;
        $slide->link = $request->link;

        $slide->save();

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
