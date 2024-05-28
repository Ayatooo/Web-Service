<?php

namespace App\Helper;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeHelper
{

    public static function generateCheckout(string $token)
    {
        Stripe::setApiKey(config('app.stripe.secret'));
        header('Content-Type: application/json');

        $infos = [
            'submit_type' => 'pay',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => 1,
                    'product_data' => [
                        'name' => 'Nom du produit vendu',
                    ],
                ],
                'quantity' => 100,
            ]],
            'mode' => 'payment',
            'payment_intent_data' => [
                'capture_method' => 'manual',
                'setup_future_usage' => 'off_session',
                'metadata' => [
                    'token' => $token
                ],
            ],
            'metadata' => [
                'token' => $token,
            ],
            'success_url' => config('app.url') . '/payment_success?transaction_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('app.url') . '/payment_failed'
        ];

        $checkout_session = Session::create($infos);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);

        return $checkout_session->url;
    }

}
