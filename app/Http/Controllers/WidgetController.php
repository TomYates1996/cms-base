<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Widget;
use App\Models\Slide;
use App\Models\Page;
use App\Models\Header;
use App\Models\Footer;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;


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

        $widget->load('slides');

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

    public function save(Request $request)
    {
        
        $request->validate([
            'page_id' => 'required|integer',
            'widgets' => 'nullable|array',
            'widgets.*.title' => 'nullable|string',
            'widgets.*.type' => 'required|string',
            'widgets.*.slides' => 'nullable|array', 
            'widgets.*.slides.*.id' => 'integer|exists:slides,id',

            'headers' => 'nullable|array',
            'headers.*.logo.id' => 'nullable|exists:images,id',
            'headers.*.link' => 'nullable|string',
            'headers.*.section' => 'required|in:primary,secondary,footer',
            
            'footers' => 'nullable|array',
            'footers.*.logo.id' => 'nullable|exists:images,id',
            'footers.*.section' => 'required|in:primary,secondary,footer',
        ]);


        $widgets = $request->input('widgets');
        $headers = $request->input('headers');
        $footers = $request->input('footers');
        $page_id = $request->input('page_id');
        
        Widget::where('page_id', $page_id)->delete();
        Header::where('page_id', $page_id)->delete();
        Footer::where('page_id', $page_id)->delete();

        foreach ($widgets as $index => $widget) {
            $widget['page_id'] = $page_id;
            $widget['order'] = $index + 1; 

            if (!isset($widget['created_at'])) {
                $widget['created_at'] = Carbon::now();  
            }

            $new_widget = Widget::create($widget);

            if (isset($widget['slides']) && is_array($widget['slides'])) {
                $new_widget->slides()->attach(array_column($widget['slides'], 'id'));
            }
        }

        foreach ($headers as $index => $header) {
            Header::create([
                'page_id' => $page_id,
                'logo_image_id' => $header['logo']['id'] ?? null,
                'link' => $header['link'] ?? null,
                'section' =>$header['section'],
                'order' => $index + 1,
                'name' => $header['name'],
                'is_saved' => $header['is_saved'],
                'template_id' => $header['template_id'],
            ]);
        }
        foreach ($footers as $index => $footer) {
            Footer::create([
                'page_id' => $page_id,
                'logo_id' => $footer['logo']['id'] ?? null,
                'section' =>$footer['section'],
                'order' => $index + 1,
                'name' => $footer['name'],
                'is_saved' => $footer['is_saved'],
                'template_id' => $footer['template_id'],
            ]);
        }

        return;
    }

    public function save_widget(Request $request, Widget $widget)
    {
        $request->validate([
            'is_saved' => 'required|boolean',
            'name' => 'nullable|string|max:255',
        ]);

        
        if ($request->is_saved) {
            $templateId = (Widget::max('template_id') ?? 0) + 1;

            $widget->update([
                'is_saved' => $request->is_saved,
                'name' => $request->name,
                'template_id' => $templateId,
            ]);
            
            $clonedWidget = $widget->replicate(); 
            $clonedWidget->page_id = null; 
            $clonedWidget->save(); 
        
            if ($widget->slides()->exists()) {
                $clonedWidget->slides()->sync($widget->slides->pluck('id')); 
            }
        } else {
            $templateId = $widget->template_id;
            $widgetToDelete = Widget::where('template_id', $templateId)->whereNull('page_id')->first(); 
            
            if ($widgetToDelete) {
                $widgetToDelete->delete();
            }

            $widget->update([
                'is_saved' => $request->is_saved,
                'name' => $request->name,
                'template_id' => null,
            ]);
        }
        
        return;
    }
    public function save_header(Request $request, Header $header)
    {
        if ($request->is_saved) {
            $templateId = (Header::max('template_id') ?? 0) + 1;

            $header->update([
                'is_saved' => $request->is_saved,
                'name' => $request->name,
                'template_id' => $templateId,
            ]);
            
            $clonedHeader = $header->replicate(); 
            $clonedHeader->page_id = null; 
            $clonedHeader->save(); 
        
        } else {
            $templateId = $header->template_id;
            $headerToDelete = Header::where('template_id', $templateId)->whereNull('page_id')->first(); 
            
            if ($headerToDelete) {
                $headerToDelete->delete();
            }

            $header->update([
                'is_saved' => $request->is_saved,
                'name' => $request->name,
                'template_id' => null,
            ]);
        }
        
        return;
    }
    public function save_footer(Request $request, Footer $footer)
    {
        if ($request->is_saved) {
            $templateId = (Footer::max('template_id') ?? 0) + 1;

            $footer->update([
                'is_saved' => $request->is_saved,
                'name' => $request->name,
                'template_id' => $templateId,
            ]);
            
            $clonedFooter = $footer->replicate(); 
            $clonedFooter->page_id = null; 
            $clonedFooter->save(); 
        
        } else {
            $templateId = $footer->template_id;
            $footerToDelete = Footer::where('template_id', $templateId)->whereNull('page_id')->first(); 
            
            if ($footerToDelete) {
                $footerToDelete->delete();
            }

            $footer->update([
                'is_saved' => $request->is_saved,
                'name' => $request->name,
                'template_id' => null,
            ]);
        }
        
        return;
    }

    public function create_save_widget(Request $request)
    {
        $data = $request->validate([
            'item' => 'required|array',
            'item.title' => 'nullable|string',
            'item.type' => 'required|string',
            'item.is_saved' => 'required|boolean',
            'item.name' => 'nullable|string',
            'item.slides' => 'nullable|array',
            'item.slides.*.id' => 'integer|exists:slides,id',
        ]);

        $item = $data['item'];

        if ($item['is_saved']) {
            $templateId = (Widget::max('template_id') ?? 0) + 1;

            $clonedWidgetData = $item;
            $clonedWidgetData['template_id'] = $templateId;
            $clonedWidgetData['page_id'] = null;
            $clonedWidgetData['is_saved'] = true;

            $clonedWidget = Widget::create($clonedWidgetData);

            if (!empty($item['slides'])) {
                $clonedWidget->slides()->attach(array_column($item['slides'], 'id'));
            }

            return response()->json([
                'message' => 'Template widget saved.',
                'template_id' => $templateId,
            ]);
        }

        return response()->json([
            'message' => 'Widget not marked as saved.',
        ], 400);
    }

    public function create_save_header(Request $request)
    {
        // Validate the incoming request
        $data = $request->validate([
            'item' => 'required|array',
            'item.is_saved' => 'required|boolean',
            'item.name' => 'nullable|string', 
            'item.logo' => 'nullable|array', 
            'item.link' => 'nullable|string', 
        ]);

        $item = $data['item'];

        if ($item['is_saved']) {
            $templateId = (Header::max('template_id') ?? 0) + 1;

            $clonedHeaderData = $item;
            $clonedHeaderData['template_id'] = $templateId;
            $clonedHeaderData['page_id'] = null;  
            $clonedHeaderData['is_saved'] = true;
            $clonedHeaderData['logo_image_id'] = $item['logo']['id'];

            $clonedHeader = Header::create([
                'name' => $clonedHeaderData['name'], 
                'logo_image_id' => $clonedHeaderData['logo']['id'],
                'link' => $clonedHeaderData['link'],
                'template_id' => $clonedHeaderData['template_id'],
                'page_id' => $clonedHeaderData['page_id'],
                'is_saved' => $clonedHeaderData['is_saved'],
            ]);

            return response()->json([
                'message' => 'Template Header saved.',
                'template_id' => $templateId,
            ]);
        }
        return response()->json([
            'message' => 'Header not marked as saved.',
        ], 400);
    }
    public function create_save_footer(Request $request)
    {
        // Validate the incoming request
        $data = $request->validate([
            'item' => 'required|array',
            'item.is_saved' => 'required|boolean',
            'item.name' => 'nullable|string', 
            'item.logo' => 'nullable|array', 
        ]);

        $item = $data['item'];

        if ($item['is_saved']) {
            $templateId = (Footer::max('template_id') ?? 0) + 1;
            $clonedFooterData = $item;
            $clonedFooterData['template_id'] = $templateId;
            $clonedFooterData['page_id'] = null;  
            $clonedFooterData['is_saved'] = true;
            $clonedFooterData['logo_id'] = $item['logo']['id'];

            $clonedFooter = Footer::create([
                'name' => $clonedFooterData['name'], 
                'logo_id' => $clonedFooterData['logo']['id'],
                'template_id' => $clonedFooterData['template_id'],
                'page_id' => $clonedFooterData['page_id'],
                'is_saved' => $clonedFooterData['is_saved'],
            ]);


            return response()->json([
                'message' => 'Template Footer saved.',
                'template_id' => $templateId,
            ]);
        }
        return response()->json([
            'message' => 'Footer not marked as saved.',
        ], 400);
    }

    public function delete_save_item(Request $request)
    {
        $request->validate([
            'template_id' => 'required|integer',
            'type' => 'required|string|in:widgets,headers,footers',
        ]);

        $typeMap = [
            'widgets' => \App\Models\Widget::class,
            'headers' => \App\Models\Header::class,
            'footers' => \App\Models\Footer::class,
        ];

        $type = $request->type;
        $templateId = $request->template_id;

        if (isset($typeMap[$type])) {
            $model = $typeMap[$type];
    
            $model::where('template_id', $templateId)
                ->where('is_saved', true)
                ->delete();
    
            return response()->json(['message' => 'Saved item deleted from all locations.']);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }

    public function update_save_item(Request $request)
    {
        $request->validate([
            'template_id' => 'required|integer',
            'type' => 'required|string|in:widgets,headers,footers',
            'item' => 'required|array',
        ]);

        $typeMap = [
            'widgets' => \App\Models\Widget::class,
            'headers' => \App\Models\Header::class,
            'footers' => \App\Models\Footer::class,
        ];
    
        $type = $request->input('type');
        $item = $request->input('item');
        $templateId = $request->input('template_id');
    
        if (!isset($typeMap[$type])) {
            return response()->json(['error' => 'Invalid type.'], 400);
        }
    
        $model = $typeMap[$type];
    
        $query = $model::where('template_id', $templateId);
    
        if ($type === 'widgets') {
            $query->each(function ($widget) use ($item) {
                $widget->update([
                    'title' => $item['title'] ?? $widget->title,
                    'type' => $item['type'] ?? $widget->type,
                    'name' => $item['name'] ?? $widget->name,
                ]);
    
                if (isset($item['slides']) && is_array($item['slides'])) {
                    $slideIds = collect($item['slides'])->pluck('id')->toArray();
                    $widget->slides()->sync($slideIds);
                }
            });
        } elseif ($type === 'headers' || $type === 'footers') {
            $query->each(function ($row) use ($item, $type) {
                $data = [
                    'section' => $item['section'] ?? $row->section,
                    'name' => $item['name'] ?? $row->name,
                ];
    
                if ($type === 'headers') {
                    $data['link'] = $item['link'] ?? $row->link;
                    $data['logo_image_id'] = $item['logo']['id'] ?? null;
                } else {
                    $data['logo_id'] = $item['logo']['id'] ?? null;
                }
    
                $row->update($data);
            });
        }
    
        return response()->json(['message' => 'Saved item updated across all instances.']);
    }

}
