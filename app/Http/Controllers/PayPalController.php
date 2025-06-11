<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\ProductVariantItem;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;


class PayPalController extends Controller
{
    public function captureOrder(Request $request)
    {
        $request->validate([
            'orderID' => 'required|string',
            'items' => 'required|array|min:1',
            'first_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address1' => 'required|string',
            'phone' => 'required|string',
            'city' => 'required|string',
            'postcode' => 'required|string',
            'country' => 'required|string',
        ]);

        $orderId = $request->orderID;

        if (!$orderId) {
            return response()->json(['error' => 'Missing orderID'], 422);
        }


        // $clientId = SiteSetting::get('paypal_client_id');
        // $secret = decrypt(SiteSetting::get('paypal_secret'));
        // $mode = SiteSetting::get('paypal_mode', 'sandbox');

        // $paypalApiBase = $mode === 'live'
        //     ? 'https://api.paypal.com'
        //     : 'https://api.sandbox.paypal.com';


        $clientId = config('paypal.client_id');
        $secret = config('paypal.secret');

        $response = Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->post('https://api.sandbox.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        // $response = Http::withBasicAuth($clientId, $secret)
        //     ->asForm()
        //     ->post("{$paypalApiBase}/v1/oauth2/token", [
        //         'grant_type' => 'client_credentials',
        //     ]);

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to get access token'], 500);
        }

        $accessToken = $response->json('access_token');

        $captureResponse = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.sandbox.paypal.com/v2/checkout/orders/{$orderId}/capture",  (object)[]);

        // $captureResponse = Http::withToken($accessToken)
        //     ->withHeaders(['Content-Type' => 'application/json'])
        //     ->post("{$paypalApiBase}/v2/checkout/orders/{$orderId}/capture", (object)[]);

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
            return response()->json(['error' => 'Price mismatch, possible tampering'], 400);
        }

        $order = Order::create([
            'order_number' => $orderId,
            'price' => $expectedAmount,
            'completed' => false,
            'status' => 'created',

            'first_name' => $request->first_name,
            'last_name' => $request->surname,
            'email' => $request->email,
            'phone' => $request->phone,
            'delivery_address' => $request->address1,
            'city' => $request->city,
            'postcode' => $request->postcode,
            'country' => $request->country,

            'paypal_order_id' => $orderId,
            'paypal_capture_id' => data_get($captureData, 'purchase_units.0.payments.captures.0.id'),
            'paypal_response' => json_encode($captureData),
        ]);

        foreach ($request->items as $item) {
            $dbItem = ProductVariantItem::find($item['id']);
            $quantity = $item['quantity'] ?? 1;

            OrderItem::create([
                'order_id' => $order->id,
                'product_variant_item_id' => $dbItem->id,
                'quantity' => $quantity,
                'price' => $dbItem->price,
            ]);
        }

        return response()->json([
            'status' => 'success', 
            'details' => $captureData, 
            'order_number' => $order->order_number,
        ]);
    }
}
