<?php

use Illuminate\Support\Facades\Route;
use Agency\SiteVisit\Http\Controllers\Admin\SiteVisitController;

Route::group([
    'prefix'     => config('app.admin_url', 'admin') . '/site-visits',
    'middleware' => ['web', 'admin'],
], function () {
    Route::get('/', [SiteVisitController::class, 'index'])->name('admin.site-visits.index');
    Route::get('/{id}', [SiteVisitController::class, 'show'])->name('admin.site-visits.show');
    Route::post('/{id}/assign', [SiteVisitController::class, 'assign'])->name('admin.site-visits.assign');
    Route::post('/{id}/status', [SiteVisitController::class, 'updateStatus'])->name('admin.site-visits.update-status');
    Route::post('/mass-cancel', [SiteVisitController::class, 'massCancel'])->name('admin.site-visits.mass-cancel');
});
