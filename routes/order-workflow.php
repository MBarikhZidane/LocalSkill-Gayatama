<?php

use App\Http\Controllers\OrderWorkflowController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('user')->name('user.workflow.')->group(function () {
    Route::get('order-workflow/summary', [OrderWorkflowController::class, 'summary'])->name('summary');
    Route::get('orders/{order}/conversation', [OrderWorkflowController::class, 'show'])->name('show');
    Route::get('orders/{order}/messages', [OrderWorkflowController::class, 'history'])->name('history');
    Route::post('orders/{order}/read', [OrderWorkflowController::class, 'read'])->middleware('throttle:120,1')->name('read');
    Route::get('orders/{order}/attachments/{attachment}', [OrderWorkflowController::class, 'download'])->name('download');
    Route::post('orders/{order}/workflow/{action}', [OrderWorkflowController::class, 'write'])->middleware('throttle:30,1')->name('write');
});
