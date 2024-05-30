<?php

namespace App\Http\Controllers;

use Exception;
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
        $email = $session['customer_details']['email'];
        try {
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
                            'email' => $email,
                            'name' => 'Client',
                        ]
                    ],
                    'subject' => 'Confirmation de paiement',
                    'htmlContent' => $html,
                ],
            ]);
        } catch (Throwable $e) {
            dd($e);
        }

        dd($response);

    }
}
