<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\ProductVariantItem;
use Inertia\Inertia;


class PayPalController extends Controller
{
    public function captureOrder(Request $request)
    {
        $orderId = $request->orderID;

        if (!$orderId) {
            return response()->json(['error' => 'Missing orderID'], 422);
        }

        $clientId = config('paypal.client_id');
        $secret = config('paypal.secret');

        $response = Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->post('https://api.sandbox.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to get access token'], 500);
        }

        $accessToken = $response->json('access_token');

        $captureResponse = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.sandbox.paypal.com/v2/checkout/orders/{$orderId}/capture",  (object)[]);

        if (!$captureResponse->successful()) {
            logger()->error('PayPal Capture Failed', [
                'body' => $captureResponse->body(),
                'status' => $captureResponse->status(),
            ]);

            return response()->json([
                'error' => 'Failed to capture order',
                'details' => $captureResponse->json(),
            ], 400);
        }

        $captureData = $captureResponse->json();

        $paypalAmount = data_get($captureData, 'purchase_units.0.payments.captures.0.amount.value');

        $realPrice = 3.5;

        foreach ($request->items as $item) {
            $dbItem = ProductVariantItem::find($item['id']);

            if (!$dbItem) {
                return response()->json(['error' => "Invalid item ID: {$item['id']}"], 400);
            }

            $price = $dbItem->price;
            $quantity = $item['quantity'] ?? 1;
            $realPrice += $price * $quantity;
        }

        $expectedAmount = number_format($realPrice, 2, '.', '');

        if ($paypalAmount !== $expectedAmount) {
            return response()->json(['error' => `Price mismatch, possible tampering`], 400);
        }

        return response()->json(['status' => 'success', 'details' => $captureData]);
    }
}
