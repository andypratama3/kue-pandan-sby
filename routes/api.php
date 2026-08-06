<?php

use App\Http\Controllers\Chatbot\WhatsAppWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Webhook WhatsApp generik — dipakai Fonnte (POST) dan Meta (POST /meta).
// Meta butuh GET terpisah untuk verifikasi awal hub.challenge.
Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle'])->middleware('throttle:60,1');
Route::get('/webhook/whatsapp/meta', [WhatsAppWebhookController::class, 'verify']);
Route::post('/webhook/whatsapp/meta', [WhatsAppWebhookController::class, 'handle'])->middleware('throttle:60,1');

// Route lama Meta Cloud API (prototipe) — dinonaktifkan, sudah digantikan
// oleh WhatsAppWebhookController generik. Controller lama masih disimpan
// sampai migrasi terbukti stabil di production.
// Route::get('/webhook/meta', [\App\Http\Controllers\Chatbot\WebhookController::class, 'verify']);
// Route::post('/webhook/meta', [\App\Http\Controllers\Chatbot\WebhookController::class, 'receive']);
