<?php
// app/Http/Controllers/PaymentController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Balance;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Refund;
use Stripe\Charge;

class PaymentController extends Controller
{

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $balance = Balance::retrieve();
        $availableBalance = $balance->available[0]->amount / 100 . ' ' . $balance->available[0]->currency;
        $pendingBalance = $balance->pending[0]->amount / 100 . ' ' . $balance->pending[0]->currency;
        $paymentIntents = PaymentIntent::all(['limit' => 10]);

        // foreach $paymentIntents, check if there is a refund
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

    public function show($id)
    {
        $payment = PaymentIntent::retrieve($id);
        // on vérifie si le paiement a été remboursé
        $refund = Refund::all(['payment_intent' => $payment->id]);
        $payment->refund = false;
        if ($refund->data) {
            $payment->refund = true;
        }
        $payementMethodValue = $payment->payment_method;
        $paymentMethod = PaymentMethod::retrieve($payementMethodValue);
        $email = $paymentMethod->billing_details->email;
        return view('payment-details', [
            'payment' => $payment,
            'email' => $email,
        ]);
    }

    public function refund($id)
    {
        $paymentIntent = PaymentIntent::retrieve($id);
        $refund = Refund::create([
            'payment_intent' => $paymentIntent->id,
        ]);
        return redirect()->back()->with('success', 'Payment refunded successfully');
    }

    public function capture($id, Request $request)
    {
        $amount = $request->input('amount');
        $paymentIntent = PaymentIntent::retrieve($id);
        $paymentIntent->capture(['amount_to_capture' => $amount]);
        return redirect()->back()->with('success', 'Payment captured successfully');
    }
}
