<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\WebhookSettingController;
use App\Http\Controllers\Admin\DeliverableController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Public routes ──────────────────────────────────────────────────
    Route::post('/contact',               [ContactController::class,   'store']);
    Route::post('/bookings',              [PublicBookingController::class, 'store']);
    Route::post('/newsletter/subscribe',  [NewsletterController::class,'subscribe']);
    Route::post('/newsletter/unsubscribe',[NewsletterController::class,'unsubscribe']);
    Route::get('/settings',               [SiteSettingController::class, 'index']);
    Route::get('/cms',                    [\App\Http\Controllers\CmsContentController::class, 'index']);

    // Auth
    Route::post('/auth/login',  [AuthController::class, 'login']);

    // Stripe webhook (no auth — verified by signature)
    Route::post('/stripe/webhook', [StripeController::class, 'webhook']);

    // ── Authenticated client routes ────────────────────────────────────
    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/logout', [AuthController::class,      'logout']);
        Route::get('/auth/me',      [AuthController::class,      'me']);
        Route::get('/dashboard/deliverables', [DashboardController::class, 'deliverables']);
        Route::post('/stripe/checkout',       [StripeController::class,    'checkout']);
    });

    // ── Admin-only routes ──────────────────────────────────────────────
    Route::middleware(['auth:api', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
        Route::post('/admins',            [AdminUserController::class, 'store']);

        Route::get('/leads',              [LeadController::class,      'index']);
        Route::get('/leads/{lead}',       [LeadController::class,      'show']);
        Route::patch('/leads/{lead}',     [LeadController::class,      'update']);

        Route::get('/bookings',           [BookingController::class,   'index']);
        Route::post('/bookings',          [BookingController::class,   'store']);
        Route::patch('/bookings/{booking}',[BookingController::class,  'update']);

        Route::get('/subscribers',        [SubscriberController::class,'index']);

        Route::get('/webhooks',           [WebhookSettingController::class, 'show']);
        Route::put('/webhooks',           [WebhookSettingController::class, 'update']);

        Route::post('/settings',          [SiteSettingController::class, 'update']);
        Route::post('/cms',               [\App\Http\Controllers\CmsContentController::class, 'update']);

        Route::get('/clients',            [DeliverableController::class, 'clients']);
        Route::get('/deliverables',       [DeliverableController::class, 'index']);
        Route::post('/deliverables',      [DeliverableController::class, 'store']);
        Route::delete('/deliverables/{id}', [DeliverableController::class, 'destroy']);
    });
});

