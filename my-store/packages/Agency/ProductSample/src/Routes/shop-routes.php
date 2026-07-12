<?php

use Illuminate\Support\Facades\Route;
use Agency\ProductSample\Http\Controllers\Shop\SampleRequestController;

Route::group([
    'prefix'     => 'sample-requests',
    'middleware' => ['web', 'theme', 'locale', 'currency', 'customer'],
], function () {
    Route::get('/', [SampleRequestController::class, 'index'])->name('shop.sample-requests.index');
    Route::post('/', [SampleRequestController::class, 'store'])->name('shop.sample-requests.store');
    Route::post('/{id}/cancel', [SampleRequestController::class, 'cancel'])->name('shop.sample-requests.cancel');
});
