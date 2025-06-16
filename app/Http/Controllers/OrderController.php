<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{

    // public function testMail()
    // {
    //     $order = Order::first();
        
    //     Mail::to('tomwozere2k5@gmail.com')->send(new OrderConfirmationMail($order));

    //     return 'Email attempted.';
    // }
    public function order_confirmation(Request $request, $order_number)
    {
        $order = Order::where('order_number', $order_number)->first();

        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');
    
        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }
    
        $page = Page::where('slug', 'order-confirmation')
            ->with('widgets.slides.image', 'headers.logo', 'footers.logo', 'footers.socialMedia', 'footers.widgets')
            ->first();

        if (!$page) {
            return Inertia::render('MissingPageNotice',  [
                'message' => 'Please create a page at /order-confirmation - No page found.',
            ]);
        };

        $pageWidgets = $page->widgets;

        $finalWidgets = collect();

        // foreach ($pageWidgets as $widget) {
        //     if ($widget->variant === 'blog_post') {
        //         foreach ($blogWidgets as $blogWidget) {
        //             $finalWidgets->push($blogWidget);
        //         }
        //     } else {
        //         $finalWidgets->push($widget);
        //     }
        // }

        $header = $page->headers->first();
        $footer = $page->footers->first();
    
        if ($header) {
            $header->pages = $formattedPages[$header->section] ?? collect();
            $header->hamburger_pages = $formattedPages[$header->section_hamburger] ?? collect();
        }

        if ($order->email) {
            Mail::to($order->email)->send(new OrderConfirmationMail($order));
        }
    
        return Inertia::render('OrderConfirmationPage', [
            'pages' => $formattedPages,
            'widgets' => $finalWidgets,
            'header' => $header,
            'footer' => $footer,
            'order' => $order,
        ]);
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    private function buildTree($pages, $parentId = null) 
    {
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
