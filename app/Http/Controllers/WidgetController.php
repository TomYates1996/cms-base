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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


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
            'variant' => 'required|string',
            'slide_ids' => 'required|array', 
            'slide_ids.*' => 'exists:slides,id', 
        ]);

        $widget = Widget::create([
            'page_id' => $pageId,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'variant' => $validated['variant'],
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
            'variant' => 'required|string',
            'slide_ids' => 'required|array',  // Ensure it's an array of slide IDs
            'slide_ids.*' => 'exists:slides,id',  // Ensure each slide ID exists
        ]);

        // Find the widget to update
        $widget = Widget::findOrFail($id);

        // Update the widget data
        $widget->update([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'variant' => $validated['variant'],
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

    public function delete_saved_widget(Widget $widget)
    {
        $widget->pages()->detach(); 
        $widget->slides()->detach();
    
        $widget->delete();
    
        return response()->json(['message' => 'Widget deleted successfully.']);
    }

    public function unsave_widget(Widget $widget)
    {
        DB::transaction(function () use ($widget) {
            $pageWidgets = $widget->pages()->withPivot('position')->get();

            
            foreach ($pageWidgets as $pageWidget) {
                $clonedWidget = $widget->replicate();
                $clonedWidget->is_saved = false;
                $clonedWidget->name = null;
                $clonedWidget->template_id = null;
                $clonedWidget->save();
                
                $slideIds = $widget->slides()->pluck('slides.id')->toArray();
                if (!empty($slideIds)) {
                    $clonedWidget->slides()->sync($slideIds);
                }
                
                DB::table('page_widget')
                ->where('page_id', $pageWidget->pivot->page_id)
                ->where('widget_id', $widget->id)
                ->update([
                    'widget_id' => $clonedWidget->id,
                    'position' => $pageWidget->pivot->position,
                ]);
            }
            
            $widget->update([
                'is_saved' => false,
                'name' => null,
                'template_id' => null,
            ]);
            
            if ($widget->pages()->count() === 0) {
                $widget->delete();
            }
        });

        return response()->json(['message' => 'Widget unsaved, cloned, and position maintained on pages.']);
    }

    public function save_widget(Request $request, Widget $widget)
    {
        $request->validate([
            'is_saved' => 'required|boolean',
            'name' => 'nullable|string|max:255',
        ]);

        if ($request->is_saved) {
            $templateId = (Widget::max('template_id') ?? 0) + 1;
        }
            
        $widget->update([
            'is_saved' => $request->is_saved,
            'name' => $request->name,
            'template_id' => $templateId ?? null,
        ]);
        
        return;
    }

    public function create_save_widget(Request $request)
    {
        $widgetData = $request->input('item');

        $validator = Validator::make($widgetData, [
            'title' => 'nullable|string',
            'subtitle' => 'nullable|string',
            'description' => 'nullable|string',
            'link' => 'nullable|string',
            // 'type' => 'required|string',
            // 'is_saved' => 'required|boolean',
            'name' => 'nullable|string',
            'slides' => 'nullable|array',
            'slides.*.id' => 'integer|exists:slides,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 400);
        }
        
        if ($widgetData['is_saved']) {
            $templateId = (Widget::max('template_id') ?? 0) + 1;

            $widget = Widget::create([
                'title' => $widgetData['title'] ?? null,
                'description' => $widgetData['description'] ?? null,
                'subtitle' => $widgetData['subtitle'] ?? null,
                'link' => $widgetData['link'] ?? null,
                'type' => $widgetData['type'],
                'variant' => $widgetData['variant'],
                'is_saved' => true,
                'name' => $widgetData['name'],
                'template_id' => $templateId,
            ]);
        
            if (isset($widgetData['slides']) && is_array($widgetData['slides'])) {
                $slideIds = array_column($widgetData['slides'], 'id');
                $widget->slides()->attach($slideIds); 
            }
            
            return response()->json([
                'message' => 'Widget saved successfully.',
                'widget' => $widget,
            ], 200);

        } else {
            $widget->update([
                'is_saved' => false,
                'name' => null,
                'template_id' => null,
            ]);
        
            return response()->json([
                'message' => 'Widget not saved (is_saved is false).',
                'widget' => $widget,
            ], 200);
        }
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
        dd($item);
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
        
        // Handle Widgets
        if ($type === 'widgets') {
            $widgets = $model::where('template_id', $templateId)->get();
    
            foreach ($widgets as $widget) {
                $widget->update([
                    'title' => $item['title'] ?? $widget->title,
                    'type' => $item['type'] ?? $widget->type,
                    'variant' => $item['variant'] ?? $widget->variant,
                    'name' => $item['name'] ?? $widget->name,
                ]);
    
                if (isset($item['slides']) && is_array($item['slides'])) {
                    $slideIds = collect($item['slides'])->pluck('id')->toArray();
                    $widget->slides()->sync($slideIds);
                }
            }
        }
    
        // Handle Headers or Footers
        if ($type === 'headers' || $type === 'footers') {
            $footerOrHeader = $model::where('template_id', $templateId)->first();
            
            if ($footerOrHeader) {
                $data = [
                    'section' => $item['section'] ?? $footerOrHeader->section,
                    'name' => $item['name'] ?? $footerOrHeader->name,
                ];
    
                if ($type === 'headers') {
                    $data['link'] = $item['link'] ?? $footerOrHeader->link;
                    $data['logo_image_id'] = $item['logo']['id'] ?? null;
                } else {
                    $data['logo_id'] = $item['logo']['id'] ?? null;
                }
    
                // Update Footer or Header
                $footerOrHeader->update($data);
                
                // Handle Social Media Links
                if ($type === 'footers' && !empty($item['social_media'])) {
                    $socialIds = collect($item['social_media'])
                        ->pluck('id')
                        ->filter()
                        ->toArray();
                    $footerOrHeader->socialMedia()->sync($socialIds);
                }
    
                // Handle Widgets in Footer
                if ($type === 'footers' && !empty($item['widgets'])) {
                    $widgetIds = collect($item['widgets'])
                        ->pluck('id')
                        ->filter()
                        ->toArray();
                    $footerOrHeader->widgets()->sync($widgetIds);
                }
            }
        }
    
        return response()->json(['message' => 'Item updated successfully.']);
    }

    public function cta_test(Request $request)
    {
        $validated = $request->validate([
            // 'footer_id'   => 'required|exists:footers,id',
            'title'       => 'nullable|string|max:255',
            'type'        => 'required|string',
            'variant'        => 'required|string',
            'description' => 'nullable|string',
        ]);
        $widget = Widget::create([
            'page_id'     => null,
            'title'       => $validated['title'],
            'type'        => $validated['type'],
            'variant'        => $validated['variant'],
            'order'       => null,
            'template_id' => null,
            'is_saved'    => false,
            'name'        => null,
            'subtitle'    => null,
            'description' => $validated['description'],
        ]);
    
        // $footer = Footer::find($validated['footer_id']);
        // $footer->widgets()->attach($widget->id);
    
        return response()->json([
            'message' => '',
            'widget' => $widget,
        ]);
    }

}
