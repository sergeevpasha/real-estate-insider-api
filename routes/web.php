<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GithubLoginController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasskeyLoginController;
use App\Http\Controllers\Auth\PasskeyRegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FileStorageController;
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
Route::post('/auth/2fa/passkey/register/generate-options', [PasskeyRegisterController::class, 'generateOptions']);
Route::post('/auth/2fa/passkey/register/verify', [PasskeyRegisterController::class, 'verify']);
Route::post('/auth/2fa/passkey/login/generate-strict-options', [PasskeyLoginController::class, 'generateStrictOptions']);
Route::post('/auth/2fa/passkey/login/generate-options', [PasskeyLoginController::class, 'generateOptions']);
Route::post('/auth/2fa/passkey/login/verify', [PasskeyLoginController::class, 'verify']);
Route::get('/auth/user', [AuthController::class, 'fetchUser']);
ROute::delete('auth/passkeys/{passkey}', [AuthController::class, 'deleteUserPasskey'])
    ->name('user.passkey.delete');
ROute::patch('auth/passkeys/{passkey}', [AuthController::class, 'updateUserPasskey'])
    ->name('user.passkey.update');

Route::post('/send-reset-link', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->name('password.request');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.reset');
Route::post('/validate-token', [PasswordResetController::class, 'validateExpirationToken'])
    ->name('password.validate-token');

Route::get('files/{name}', [FileStorageController::class, 'viewable'])
    ->middleware('auth')
    ->where('name', '.*')
    ->name('files.view');
Route::get('downloads/{name}', [FileStorageController::class, 'downloadable'])
    ->middleware('auth')
    ->where('name', '.*')
    ->name('files.download');
