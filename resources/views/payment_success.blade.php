@php
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Illuminate\Support\Facades\Auth;
    use Stripe\Stripe;
    use Stripe\Checkout\Session;
    use Stripe\PaymentIntent;

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

    $transaction_id = $_GET['transaction_id'];
    Stripe::setApiKey(config('app.stripe.secret'));

    $session = Session::retrieve($transaction_id);
    $email = $session['customer_details']['email'];

    $payment_intent = PaymentIntent::retrieve($session->payment_intent);
    $stripeCustomerId = $payment_intent->customer;

    $payementObject = PaymentIntent::retrieve($session->payment_intent);
    $payementMethod = $payementObject->payment_method;
    $payementMethodObject = \Stripe\PaymentMethod::retrieve($payementMethod);
    $last4 = $payementMethodObject->card->last4;

    $metaJwt = $payment_intent->metadata->token;
    $headers = new stdClass();
    $key = 'mattgones';

    $decoded = JWT::decode($metaJwt, new Key($key, 'HS256'), $headers);
@endphp

@extends('welcome')

@section('content')
    <aside class="bg-gray-100 p-6 rounded-lg shadow-md">
        <div class="text-green-600 font-bold text-lg mb-4">
            ✅ Merci pour votre paiement.
        </div>
        <div class="mb-4">
            <h2 class="font-semibold text-gray-700">Récapitulatif</h2>
        </div>

        <div class="mb-4">
            <p class="font-semibold text-gray-700">Conducteur :</p>
            <p class="text-gray-600">{{$decoded->lastname}} {{$decoded->firstname}}, {{$decoded->age}} ans</p>
        </div>

        <div class="mb-4">
            <p class="font-semibold text-gray-700">Véhicule :</p>
            <p class="text-gray-600">{{$cars[$decoded->vehicle - 1]['name']}}</p>
        </div>

        <div class="mb-4">
            <p class="font-semibold text-gray-700">Ville :</p>
            <p class="text-gray-600">{{$cities[$decoded->city - 1]['name']}}</p>
        </div>

        <hr class="my-4">

        <div class="mb-4">
            <p class="font-semibold text-gray-700">Email :</p>
            <p class="text-gray-600">{{$email ?? 'Aucun email ❌'}}</p>
        </div>

        <div class="mb-4">
            <p class="font-semibold text-gray-700">Montant du dépôt de garantie :</p>
            <p class="text-gray-600">{{$payment_intent->amount / 100}} €</p>
        </div>

        <div class="mb-4">
            <p class="font-semibold text-gray-700">Versement réalisé avec une carte bancaire se terminant par :</p>
            <p class="text-gray-600">-{{$last4}}</p>
        </div>

        <div class="mb-4">
            <p class="font-semibold text-gray-700">Numéro de transaction :</p>
            <p class="text-gray-600">{{$transaction_id}} passé dans l’URL</p>
        </div>

        <a href="{{route('email.send.confirmation')}}">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Envoyer un email de confirmation
            </button>
        </a>
    </aside>

@endsection

