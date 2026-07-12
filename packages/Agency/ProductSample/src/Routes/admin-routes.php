<?php

use Illuminate\Support\Facades\Route;
use Agency\ProductSample\Http\Controllers\Admin\SampleRequestController;
use Agency\ProductSample\Http\Controllers\Admin\SampleInventoryController;

Route::group([
    'prefix'     => config('app.admin_url', 'admin'),
    'middleware' => ['web', 'admin'],
], function () {
    // Sample Requests
    Route::get('/sample-requests', [SampleRequestController::class, 'index'])->name('admin.sample-requests.index');
    Route::get('/sample-requests/{id}', [SampleRequestController::class, 'show'])->name('admin.sample-requests.show');
    Route::post('/sample-requests/{id}/approve', [SampleRequestController::class, 'approve'])->name('admin.sample-requests.approve');
    Route::post('/sample-requests/{id}/reject', [SampleRequestController::class, 'reject'])->name('admin.sample-requests.reject');
    Route::post('/sample-requests/{id}/status', [SampleRequestController::class, 'updateStatus'])->name('admin.sample-requests.update-status');

    // Sample Inventory
    Route::get('/sample-inventory', [SampleInventoryController::class, 'index'])->name('admin.sample-inventory.index');
    Route::post('/sample-inventory/{productId}', [SampleInventoryController::class, 'update'])->name('admin.sample-inventory.update');
});
