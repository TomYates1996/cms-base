<?php

namespace App\Services;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PayPalService
{
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('paypal.client_id'),
                config('paypal.secret')
            )
        );

        $this->apiContext->setConfig([
            'mode' => config('paypal.mode', 'sandbox'),
        ]);
    }

    public function getApiContext()
    {
        return $this->apiContext;
    }
}
