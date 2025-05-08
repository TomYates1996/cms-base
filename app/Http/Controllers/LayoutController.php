<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layout;
use App\Models\Header;
use App\Models\Footer;
use App\Models\Widget;
use App\Models\Page;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class LayoutController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
           
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
           
        // $validated['created_by'] = $user->id;
           
        $header = Header::create(); 
        $footer = Footer::create();

        $validated['header_id'] = $header->id;
        $validated['footer_id'] = $footer->id;
        
        $layout = Layout::create($validated);
           
        return;
    }

    public function destroy(Request $request, $id) 
    {
        $user = auth()->user();
    
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $layout = Layout::findOrFail($id);
    
        DB::transaction(function () use ($layout) {
            $widgetIds = $layout->widgets()->pluck('widgets.id')->toArray();
    
            $layout->widgets()->detach();
    
            foreach ($widgetIds as $widgetId) {
                $widget = Widget::find($widgetId);
    
                if (
                    $widget &&
                    $widget->layouts()->count() === 0 &&
                    $widget->pages()->count() === 0 &&
                    $widget->footers()->count() === 0 &&
                    !$widget->is_saved
                ) {
                    $widget->delete();
                }
            }
    
            $layout->delete();
        });
    
        return;
    }

     // Load the CMS Edit Content Page
    public function load_edit_content(Request $request, $id)
    {
        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');

        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }

        $layout = Layout::where('id', $id)
            ->with('widgets.slides.image', 'header.logo', 'footer.logo', 'footer.socialMedia', 'footer.widgets')
            ->firstOrFail();

        $header = $layout->header;
        $footer = $layout->footer;

        // Load the saved widgets
        $savedWidgets = Widget::with('slides.image')
            ->where('is_saved', true)
            ->whereNull('page_id')
            ->get();

        $savedHeaders = Header::with('logo')
            ->where('is_saved', true)
            ->whereNull('page_id')
            ->get()
            ->map(function ($header) use ($formattedPages) {
                $header->pages = $formattedPages[$header->section] ?? collect();
                return $header;
            });

        $savedFooters = Footer::with('logo', 'socialMedia', 'widgets.slides.image')
            ->where('is_saved', true)
            ->whereNull('page_id')
            ->get()
            ->map(function ($footer) use ($formattedPages) {
                $footer->pages = $formattedPages[$footer->section] ?? collect();
                return $footer;
            });

        if ($header) {
            $header->pages = $formattedPages[$header->section] ?? collect();
            $header->hamburger_pages = $formattedPages[$header->section_hamburger] ?? collect();
        }

        return Inertia::render('cms/pages/EditContent', [
            'pages' => $formattedPages,
            'page' => $layout,
            'widgets' => $layout->widgets,
            'savedWidgets' => $savedWidgets,
            'savedHeaders' => $savedHeaders,
            'savedFooters' => $savedFooters,
            'header' => $header,
            'footer' => $footer,
            'layout' => true,
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'layout_id' => 'required|integer',
            'widgets' => 'nullable|array',
            'widgets.*.title' => 'nullable|string',
            'widgets.*.type' => 'required|string',
            'widgets.*.slides' => 'nullable|array', 
            'widgets.*.slides.*.id' => 'integer|exists:slides,id',

            'header.logo.id' => 'nullable|exists:images,id',
            'header.link' => 'nullable|string',
            'header.section' => 'required|in:primary,secondary,footer',
            'header.section_hamburger' => 'required|in:primary,secondary,footer',
            'header.menu_type' => 'required|in:dropdown,hamburger',
            
            'footer.logo.id' => 'nullable|exists:images,id',
            'footer.section' => 'required|in:primary,secondary,footer',
            'footer.social_media' => 'nullable|array',
            'footer.social_media.*.id' => 'integer|exists:social_media_links,id',
        ]);

        
        $widgets = $request->input('widgets');
        $headerInput = $request->input('header');
        $headerData = Header::find($headerInput['id']);
        $layout_id = $request->input('layout_id');
        $layout = Layout::find($layout_id);
        
        $incomingWidgetIds = collect($widgets)->pluck('id')->filter()->toArray(); 
        $currentWidgetIds = $layout->widgets()->pluck('widgets.id')->toArray();
        
        $widgetsToDetach = array_diff($currentWidgetIds, $incomingWidgetIds);

        if (!empty($widgetsToDetach)) {
            $layout->widgets()->detach($widgetsToDetach);
        }
        
        Widget::whereIn('id', $widgetsToDetach)
            ->where('is_saved', false)
            ->get()
            ->each(function ($widget) {
                if (
                    $widget->pages()->count() === 0 &&
                    $widget->layouts()->count() === 0
                ) {
                    $widget->delete();
                }
            });
        
        foreach ($widgets as $index => $widgetData) {
            
            if (isset($widgetData['id'])) {
                $existingWidget = Widget::find($widgetData['id']);
                $existingWidget->update([
                    'title' => $widgetData['title'] ?? null,
                    'description' => $widgetData['description'] ?? null,
                    'subtitle' => $widgetData['subtitle'] ?? null,
                    'link' => $widgetData['link'] ?? null,
                ]);
                $widget = $existingWidget;
            } else {
                $widget = Widget::create($widgetData);
            }
    
            $widget->layouts()->syncWithoutDetaching([$layout_id => ['position' => $index + 1]]);
    
            if (isset($widgetData['slides']) && is_array($widgetData['slides'])) {
                $slideIds = array_column($widgetData['slides'], 'id');
    
                if ($widget->wasRecentlyCreated) {
                    $widget->slides()->attach($slideIds); 
                } else {
                    $widget->slides()->sync($slideIds); 
                }
            }
        }
        
        $existingHeader = $layout->header;

        if ($existingHeader && $existingHeader->id === $headerData['id']) {
            $headerData->update([
                'logo_image_id' => $headerInput['logo']['id'] ?? null,
                'link' => $headerInput['link'] ?? null,
                'section' =>$headerInput['section'],
                'menu_type' => $headerInput['menu_type'],
                'section_hamburger' => $headerInput['section_hamburger'],
            ]);
        } else {
            $currentHeader = $layout->header;
            dd($currentHeader);
            if ($currentHeader &&
                $currentHeader->layouts->count() === 0 &&
                $currentHeader->pages->count() === 0
            ) {
                $currentHeader->delete();
            }
            
            $layout->update([
                'header_id' => $headerData['id'],
            ]);

            $headerData->update([
                'logo_image_id' => $headerInput['logo']['id'] ?? null,
                'link' => $headerInput['link'] ?? null,
                'section' =>$headerInput['section'],
                'menu_type' => $headerInput['menu_type'],
                'section_hamburger' => $headerInput['section_hamburger'],
            ]);
        }
        
        $footerInput = $request->input('footer');
        $footerData = Footer::find($footerInput['id']);
        
        $currentFooter = $layout->footer;
        
        if ($layout->footer_id !== $footerInput['id']) {
            if ($currentFooter &&
                $currentFooter->layouts->count() === 0 &&
                $currentFooter->pages->count() === 0
            ) {
                if (!$currentFooter->is_saved) {
                    $currentFooter->delete();
                }
                $layout->update([
                    'footer_id' => $footerInput['id'],
                ]);
            }
        }
    
        $footerData->update([
            'section' => $footerInput['section'] ?? $footerData->section,
        ]);
    
        if (isset($footerInput['social_media']) && is_array($footerInput['social_media'])) {
            $socialMediaIds = collect($footerInput['social_media'])->pluck('id')->toArray();
            $footerData->socialMedia()->sync($socialMediaIds);
        }

        if (isset($footerInput['logo']['id'])) {
            $footerData->logo_id = $footerInput['logo']['id'];
            $footerData->save();
        }

        foreach ($footerInput['widgets'] as $widget) {
            if (isset($widget['id'])) {
                $existingWidget = Widget::find($widget['id']);
        
                if ($existingWidget && !$footerData->widgets->contains($existingWidget->id)) {
                    $footerData->widgets()->attach($existingWidget->id);
                }
            } else {
                $newWidget = Widget::create([
                    'title'       => $widget['title'] ?? null,
                    'type'        => $widget['type'] ?? null,
                    'is_saved'    => false,
                    'description' => $widget['description'] ?? null,
                ]);
        
                $footerData->widgets()->attach($newWidget->id);

                if (isset($widget['slides']) && is_array($widget['slides'])) {
                    foreach ($widget['slides'] as $slideData) {
                        $slide = Slide::find($slideData['id']);
                        
                        if (!$slide) {
                            continue; 
                        }
            
                        if (!$widget->slides->contains($slide->id)) {
                            $widget->slides()->attach($slide->id);
                        }
                    }
                }
            }
        }        
        return;
    }

    public function index()
    {
        $layouts = Layout::all();
        return response()->json($layouts);
    }





    private function buildTree($pages, $parentId = null) {
        $branch = [];
    
        foreach ($pages as $page) {
            if ($page['parent_id'] === $parentId) {
                $children = $this->buildTree($pages, $page['id']);
                if ($children) {
                    $page['children'] = $children;
                }
                $branch[] = $page;
            }
        }
    
        return $branch;
    }
}
