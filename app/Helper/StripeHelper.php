<?php

namespace App\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use stdClass;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeHelper
{

    /**
     * Retourne l'URL de paiement Stripe
     * @param string $token
     * @return string
     * @throws ApiErrorException
     */
    public static function generateCheckout(string $token): string
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

    /**
     * Gère la capture dy paiement
     * @param array $payload
     */
    public static function handleChargeCapture(array $payload)
    {
        Log::info('Stripe webhook received', $payload);

        $cities = [
            ['id' => 1, 'name' => 'Paris'],
            ['id' => 2, 'name' => 'Lyon'],
            ['id' => 3, 'name' => 'Marseille']
        ];
        $cars = [
            ['id' => 1, 'name' => 'Aston Martin'],
            ['id' => 2, 'name' => 'Bentley'],
            ['id' => 3, 'name' => 'Cadillac'],
            ['id' => 4, 'name' => 'Ferrari'],
            ['id' => 5, 'name' => 'Jaguar']
        ];
        $jwt = $payload['metadata']['token'] ?? "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJfdG9rZW4iOiI3ZVdlRXg5OEV0VHpKeUw5bkdvT3lxcmxndDRVc0FDS2FFWndPd1NPIiwibGFzdG5hbWUiOiJSZXluYXJkIiwiZmlyc3RuYW1lIjoiTG91aXMiLCJhZ2UiOiI5MiIsImNpdHkiOiIyIiwidmVoaWNsZSI6IjIiLCJ0b2tlbiI6ImNhNmRlNDY2LTY5YTAtNGQ4NS1hMTZiLWU1ZDk1NWVlZjY1NSJ9.XrefaXar3_LbOjQ7-sioeWxitVVWirq9d-gzjdh5cts";
        Log::info('Token', ['jwt' => $jwt]);
        $headers = new stdClass();
        $key = 'mattgones';

        $decoded = JWT::decode($jwt, new Key($key, 'HS256'), $headers);
        $firstname = $decoded->firstname;
        $lastname = $decoded->lastname;
        $city = $decoded->city;
        $vehicle = $decoded->vehicle;
        $token = $decoded->token;

        $cityName = '';
        $carName = '';
        foreach ($cities as $value) {
            if ($value['id'] == $city) {
                $cityName = $value['name'];
                break;
            }
        }
        foreach ($cars as $car) {
            if ($car['id'] == $vehicle) {
                $carName = $car['name'];
                break;
            }
        }

        Log::info('Data', [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'city' => $cityName,
            'vehicle' => $carName,
            'token' => $token
        ]);

    }

}
