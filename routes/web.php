<?php

use App\Http\Controllers\FormController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::redirect('/', '/dashboard');
Route::get('/dashboard', [PaymentController::class, 'index'])->name('stripe.dashboard');
Route::get('/payment/{id}', [PaymentController::class, 'show'])->name('stripe.payment.show');
Route::post('/payment/{id}/capture', [PaymentController::class, 'capture'])->name('stripe.payment.capture');
Route::post('/payment/{id}/refund', [PaymentController::class, 'refund'])->name('stripe.payment.refund');
Route::post('/stripe_webhooks', [StripeWebhookController::class, 'endpoint'])->withoutMiddleware(['web', 'auth'])->name('stripe.webhooks');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['prefix' => 'form', 'middleware' => 'auth'], function () {
    Route::get('/', [FormController::class, 'index'])->name('form.index');
    Route::post('/', [FormController::class, 'submit'])->name('form.submit');
});

Route::get('payment_success', function () {
    return view('payment_success');
});

Route::get('payment_failed', function () {
    return view('payment_failed');
});

Route::group(['prefix' => 'mail'], function () {
    Route::get('/send/confirmation/', [MailController::class, 'sendConfirmation'])->name('email.send.confirmation');
});

require __DIR__.'/auth.php';
