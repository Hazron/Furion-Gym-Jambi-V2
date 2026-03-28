<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//FONNTE API

//ROUTE STOP
Route::post('/webhook/inbox', [WebhookController::class, 'handleInbox']);

//ROUTE PROGRESS BAR
Route::post('/webhook/status', [WebhookController::class, 'handleStatus']);