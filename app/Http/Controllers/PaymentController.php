<?php
// app/Http/Controllers/PaymentController.php
namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Balance;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Charge;
use Stripe\StripeClient;

class PaymentController extends Controller
{

    /**
     * Affiche le tableau de bord de l'utilisateur
     * @return View|RedirectResponse
     * @throws ApiErrorException
     */
    public function index(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $balance = Balance::retrieve();
        $availableBalance = $balance->available[0]->amount / 100 . ' ' . $balance->available[0]->currency;
        $pendingBalance = $balance->pending[0]->amount / 100 . ' ' . $balance->pending[0]->currency;
        $paymentIntents = PaymentIntent::all(['limit' => 10]);

        foreach ($paymentIntents->data as $paymentIntent) {
            $refund = Refund::all(['payment_intent' => $paymentIntent->id]);
            if ($refund->data) {
                $paymentIntent->refund = $refund->data[0];
            }
        }

        return view('stripe-dashboard', [
            'availableBalance' => $availableBalance,
            'pendingBalance' => $pendingBalance,
            'payments' => $paymentIntents->data,
        ]);
    }

    /**
     * Affiche les détails d'un paiement
     * @param string $id
     * @return View
     * @throws ApiErrorException
     */
    public function show(string $id): View
    {
        $payment = PaymentIntent::retrieve($id);
        $refund = Refund::all(['payment_intent' => $payment->id]);
        $payment->refund = false;
        if ($refund->data) {
            $payment->refund = true;
        }
        $payementMethodValue = $payment->payment_method;
        $paymentMethod = PaymentMethod::retrieve($payementMethodValue);
        $email = $paymentMethod->billing_details->email;
        $canRenew = true;
        $expiration = $payment->expiration_date;
        if ($expiration) {
            $canRenew = $expiration->diffInMinutes(now()) > 15;
        }
        return view('payment-details', [
            'payment' => $payment,
            'email' => $email,
            'canRenew' => $canRenew,
        ]);
    }

    /**
     * Rembourse un paiement
     * @param string $id
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function refund(string $id): RedirectResponse
    {
        $paymentIntent = PaymentIntent::retrieve($id);
        Refund::create([
            'payment_intent' => $paymentIntent->id,
        ]);
        return redirect()->back()->with('success', 'Payment refunded successfully');
    }

    /**
     * Encaisser un paiement
     * @param string $id
     * @param Request $request
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function capture(string $id, Request $request): RedirectResponse
    {
        $amount = $request->input('amount');
        $paymentIntent = PaymentIntent::retrieve($id);
        $paymentIntent->capture(['amount_to_capture' => $amount]);
        return redirect()->back()->with('success', 'Payment captured successfully');
    }

    /**
     * Renouvelle un paiement
     * @param string $id
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function renew(string $id): RedirectResponse
    {
        $paymentIntent = PaymentIntent::retrieve($id);
        $paymentMethod = PaymentMethod::retrieve($paymentIntent->payment_method);

        $paymentMethodId = $paymentMethod->id;
        $price = $paymentIntent->amount;
        $currency = $paymentIntent->currency;
        $card = $paymentMethod->card;

        if ($card->checks->cvc_check !== 'pass') {
            return redirect()->back()->with('error', 'Your card is invalid');
        }

        $newPaymentIntent = PaymentIntent::create([
            'amount' => $price,
            'currency' => $currency,
            'payment_method' => $paymentMethodId,
            'customer' => $paymentMethod->customer,
            'confirm' => true,
            'confirmation_method' => 'manual',
            'metadata' => [
                'renew' => true,
                'original_transaction' => $paymentIntent->id,
            ],
            'return_url' => route('payment.success'),
        ]);

        if ($newPaymentIntent->status !== 'succeeded') {
            return redirect()->back()->with('error', 'Payment failed');
        }

        return redirect()->back()->with('success', 'Payment renewed successfully');
    }

}
