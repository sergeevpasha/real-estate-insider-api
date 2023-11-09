<?php

use App\Http\Controllers\Auth\GithubLoginController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasskeyLoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login/github', [GithubLoginController::class, 'redirectToGithub']);
Route::get('/login/github/callback', [GithubLoginController::class, 'handleGithubCallback']);
Route::get('/login/google', [GoogleLoginController::class, 'redirectToGoogle']);
Route::get('/login/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);
Route::post('/auth/2fa/passkey/generate-options', [PasskeyLoginController::class, 'generateOptions']);
Route::post('/auth/2fa/passkey/registration/verify', [PasskeyLoginController::class, 'verify']);


Route::get('/', function () {
    return view('welcome');
});
