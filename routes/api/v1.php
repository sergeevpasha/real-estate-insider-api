<?php

declare(strict_types=1);

use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Auth\AuthController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->only('update');
    ROute::get('/users/{user}/passkeys', [UserController::class, 'getUserPasskeys'])->name('user.passkeys');
});
