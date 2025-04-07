<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Widget;
use App\Models\Slide;
use Inertia\Inertia;


class WidgetController extends Controller
{
    public function index($pageId)
    {
        $widgets = Widget::with('slides')->where('page_id', $pageId)->get();

        return response()->json($widgets);  
    }

    // Show the form to create a new widget)
    public function create($pageId)
    {
        // Fetch available slides (or any other data for the form)
        $slides = Slide::all();

        return view('widgets.create', compact('pageId', 'slides'));  
    }

    public function store(Request $request, $pageId)
    {
        $validated = $request->validate([
            'title' => 'nullable|string',
            'type' => 'required|string',
            'slide_ids' => 'required|array', 
            'slide_ids.*' => 'exists:slides,id', 
        ]);

        $widget = Widget::create([
            'page_id' => $pageId,
            'title' => $validated['title'],
            'type' => $validated['type'],
        ]);
        
        $widget->slides()->attach($validated['slide_ids']);  

        return response()->json([
            'message' => 'Widget added successfully!',
            'widget' => $widget,
        ]);
    }
    // Show the form to edit an existing widget
    public function edit($id)
    {
        // Retrieve the widget along with its slides
        $widget = Widget::with('slides')->findOrFail($id);
        $slides = Slide::all();  // Fetch available slides

        return view('widgets.edit', compact('widget', 'slides'));
    }

    // Update an existing widget and its associated slides
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'title' => 'nullable|string',
            'type' => 'required|string',
            'slide_ids' => 'required|array',  // Ensure it's an array of slide IDs
            'slide_ids.*' => 'exists:slides,id',  // Ensure each slide ID exists
        ]);

        // Find the widget to update
        $widget = Widget::findOrFail($id);

        // Update the widget data
        $widget->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
        ]);

        // Sync the slides (this removes old associations and adds the new ones)
        $widget->slides()->sync($validated['slide_ids']);  // Using sync to keep the relationship updated

        // Redirect or respond as needed
        return redirect()->route('widgets.index', ['pageId' => $widget->page_id]);
    }

    // Delete a widget
    public function destroy(Request $request, $pageId, $id)
    {
        $widget = Widget::findOrFail($id);

        $widget->slides()->detach();

        $widget->delete();

        return;
    }
}
