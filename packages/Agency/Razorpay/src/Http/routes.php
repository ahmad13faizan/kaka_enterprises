<?php

use Agency\Razorpay\Http\Controllers\RazorpayController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('razorpay')->group(function () {
        Route::get('/redirect', [RazorpayController::class, 'redirect'])->name('razorpay.redirect');

        // Razorpay checkout posts the payment result back here. The request
        // originates from the Razorpay popup, so CSRF is skipped and the
        // signature is verified server-side instead.
        Route::post('/callback', [RazorpayController::class, 'callback'])
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->name('razorpay.callback');

        Route::get('/cancel', [RazorpayController::class, 'cancel'])->name('razorpay.cancel');
    });
});
