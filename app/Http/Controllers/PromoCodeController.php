<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;
use Inertia\Inertia;


class PromoCodeController extends Controller
{
    public function index() 
    {
        return response()->json([
            'coupons' => PromoCode::all(),
        ]);
    }

    public function load_coupons()
    {
        $coupons = PromoCode::all();

        return Inertia::render('crm/coupons/CouponsHome', [
            'coupons' => $coupons,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'expires_at' => 'nullable|date',
        ]);
        
        $validated['active'] = true;

        $coupon = PromoCode::create($validated);
    return;
    }

    public function validatePromo(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);


        $promo = PromoCode::valid()->where('code', $request->code)->first();

        if (!$promo) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired promo code.',
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'discount_percentage' => $promo->discount_percentage,
            'message' => 'Promo code applied successfully.',
        ]);
    }
}