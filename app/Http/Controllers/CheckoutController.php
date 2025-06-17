<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PromoCode;
use App\Models\Page;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    public function start(Request $request)
    {
        $items = $request->input('items');
        $promo = $request->input('promo');

        $subtotal = collect($items)->reduce(fn($sum, $item) => $sum + $item['price'] * $item['quantity'], 0);

        $discount = 0;
        if ($promo) {
            $promoModel = PromoCode::where('code', $promo)->first();
            if ($promoModel) {
                $discount = $promoModel->type === 'percent'
                    ? $subtotal * ($promoModel->discount_percentage / 100)
                    : $promoModel->discount_percentage;
            }
        }

        $totalAfterDiscount = max(0, $subtotal - (($subtotal/100) * $discount));
        $shipping = $totalAfterDiscount >= 50 ? 0 : 3.50;
        $finalTotal = $totalAfterDiscount + $shipping;


        $checkoutId = Str::uuid();
        session()->put("checkout_{$checkoutId}", [
            'items' => $items,
            'promo' => $promo,
            'discount' => $discount,
            'shipping' => $shipping,
            'total' => $finalTotal,
        ]);

        return response()->json(['checkout_id' => $checkoutId]);
    }

    public function show(string $id)
    {
        $cart = session("checkout_{$id}");

        if (!$cart) {
            abort(404);
        }

        $flatPages = Page::orderBy('level')->get();
        $groupedFlatPages = $flatPages->groupBy('section');
    
        $formattedPages = [];
        foreach ($groupedFlatPages as $section => $pagesInSection) {
            $formattedPages[$section] = $this->buildTree($pagesInSection->toArray());
        }
    
        $page = Page::where('slug', 'checkout')
            ->with('widgets.slides.image', 'headers.logo', 'footers.logo', 'footers.socialMedia', 'footers.widgets')
            ->first();

        if (!$page) {
            return Inertia::render('MissingPageNotice',  [
                'message' => 'Please create a page at /checkout - No page found.',
            ]);
        };

        $pageWidgets = $page->widgets;

        $finalWidgets = collect();

        $header = $page->headers->first();
        $footer = $page->footers->first();
    
        if ($header) {
            $header->pages = $formattedPages[$header->section] ?? collect();
            $header->hamburger_pages = $formattedPages[$header->section_hamburger] ?? collect();
        }

        return inertia('CheckoutPage', [
            'cart' => $cart,
            'header' => $header,
            'footer' => $footer,
            'pages' => $formattedPages,
        ]);
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

    // public function getAddresses(Request $request) 
    // {
    //     $postcode = $request->query('postcode');

    //     if (empty($postcode) || strlen($postcode) < 3) {
    //         return response()->json([], 400); 
    //     }

    //     $response = Http::get("https://api.postcodes.io/postcodes/{$postcode}/autocomplete");
    //     dd($response);
    //     if ($response->successful()) {
    //         $results = $response->json('result');
    //         return response()->json($results);
    //     }

    //     return response()->json([], 404);
    // }
}
