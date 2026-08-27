<?php

use Illuminate\Support\Facades\Route;
use Patchub\Client\Http\Controllers\MarkAsReadController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('patchub/mark-as-read', MarkAsReadController::class)->name('patchub.mark-as-read');
});
