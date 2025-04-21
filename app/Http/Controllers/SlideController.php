<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slide;
use App\Models\Image;
use Inertia\Inertia;


class SlideController extends Controller
{
    public function load()
    {
        return Inertia::render('cms/Slides', [
        ]);
    }
    public function load_edit($slide_id)
    {
        $slide = Slide::find($slide_id);

        return Inertia::render('cms/slides/EditSlide', [
            'slide' => $slide,
        ]);
    }
    public function load_create()
    {
        $images = Image::all();

        return Inertia::render('cms/slides/NewSlide', [
            'images' => $images,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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
            'description' => 'nullable|string',
            'link' => ['nullable', 'regex:/^(\/|https?:\/\/)/i'],
            'image_id' => 'nullable|integer',
        ]);

        $slide = Slide::find($request->id);

        $slide->title = $request->title;
        $slide->image_id = $request->image_id;
        $slide->description = $request->description;
        $slide->link = $request->link;

        $slide->save();

        return;
    }

    // Get all the slides

    public function index()
    {
        return response()->json([
            'slides' => Slide::with('image')->get(),
        ]);
    }

    public function delete($id) {
        Slide::findOrFail($id)->delete();
        return response()->json(['message' => 'Slide deleted']);
    }

}
