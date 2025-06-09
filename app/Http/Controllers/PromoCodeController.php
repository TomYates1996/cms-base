<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
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