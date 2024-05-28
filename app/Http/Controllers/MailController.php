<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Throwable;

class MailController extends Controller
{

    /**
     * Envoie un email de confirmation de paiement
     * @param Request $request
     * @return void
     * @throws GuzzleException
     * @throws ApiErrorException
     * @throws Throwable
     */
    public function sendConfirmation(Request $request): void
    {
        $transaction_id = $request->transaction_id;
        $html = view('payment_success', compact('transaction_id'))->render();
        $session = Session::retrieve($transaction_id);
//        $email = $session['customer_details']['email'];
        $email = 'matteo.dinville@ynov.com';

        $client = new Client();
        $response = $client->post('https://api.brevo.com/v3/smtp/email', [
            'headers' => [
                'accept' => 'application/json',
                'api-key' => config('app.brevo.api_key'),
                'content-type' => 'application/json',
            ],
            'json' => [
                'sender' => [
                    'name' => 'Louisdev',
                    'email' => 'louisreynard919@gmail.com'
                ],
                'to' => [
                    [
                        'email' => 'matteo.dinville@ynov.com',
                        'name' => 'Mattgones',
                    ],
                ],
                'subject' => 'Confirmation de paiement',
                'templateId' => 1,
            ],
        ]);

        dd($response, $email);
    }
}
