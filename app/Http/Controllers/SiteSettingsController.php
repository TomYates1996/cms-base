<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    public function updatePaypalCredentials(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string',
            'secret' => 'required|string',
            'mode' => 'required|in:sandbox,live',
        ]);

        $encryptedSecret = encrypt($request->secret);

        SiteSetting::set('paypal_client_id', $request->client_id);
        SiteSetting::set('paypal_secret', $encryptedSecret);
        SiteSetting::set('paypal_mode', $request->mode);

        return back()->with('success', 'PayPal credentials updated.');
    }

}
