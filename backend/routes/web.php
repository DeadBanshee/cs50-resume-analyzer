<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResumeController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::post('/analyze', [ResumeController::class, 'analyze'])
    ->withoutMiddleware(VerifyCsrfToken::class);