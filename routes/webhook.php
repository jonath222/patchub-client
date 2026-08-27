<?php

use Illuminate\Support\Facades\Route;
use Patchub\Client\Http\Controllers\WebhookController;
use Patchub\Client\Http\Middleware\VerifySignature;

Route::post(config('patchub-client.webhook_path'), [WebhookController::class, 'store'])
    ->middleware(VerifySignature::class)
    ->name('patchub.webhook');
