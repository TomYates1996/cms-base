<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\PromoCode;

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

        return inertia('CheckoutPage', [
            'cart' => $cart,
        ]);
    }
}
