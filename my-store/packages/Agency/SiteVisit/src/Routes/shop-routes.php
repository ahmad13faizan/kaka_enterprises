<?php

use Illuminate\Support\Facades\Route;
use Agency\SiteVisit\Http\Controllers\Shop\SiteVisitController;

Route::group([
    'prefix'     => 'site-visits',
    'middleware' => ['web', 'theme', 'locale', 'currency', 'customer'],
], function () {
    Route::get('/', [SiteVisitController::class, 'index'])->name('shop.site-visits.index');
    Route::get('/create', [SiteVisitController::class, 'create'])->name('shop.site-visits.create');
    Route::post('/', [SiteVisitController::class, 'store'])->name('shop.site-visits.store');
    Route::post('/{id}/cancel', [SiteVisitController::class, 'cancel'])->name('shop.site-visits.cancel');
});
