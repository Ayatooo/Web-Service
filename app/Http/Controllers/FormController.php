<?php

namespace App\Http\Controllers;

use App\Helper\StripeHelper;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Firebase\JWT\JWT;
use Stripe\Exception\ApiErrorException;

class FormController
{

    /**
     * Retourne le formulaire
     * @return View
     */
    public function index(): View
    {
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
        return view('form', [
            'cities' => $cities,
            'cars' => $cars
        ]);
    }

    /**
     * Traite le formulaire
     * @param Request $request
     * @return Redirector|Application|RedirectResponse
     * @throws ApiErrorException
     */
    public function submit(Request $request): Redirector|Application|RedirectResponse
    {

        $key = 'mattgones';
        $payload = $request->all();


        $jwt = JWT::encode($payload, $key, 'HS256');

        $checkout = StripeHelper::generateCheckout($jwt);

        return redirect($checkout);
    }
}
