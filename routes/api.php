<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/user')->group(function () {
    Route::get('/retrieve', [UserController::class, 'retrieve']);
    Route::post('/sign-in', [UserController::class, 'signin']);
    Route::post('/sign-up', [UserController::class, 'signup']);
    Route::patch('/{email}/email-verification', [UserController::class, 'emailVerification']);
    Route::put('/{id}/password-confirmation', [UserController::class, 'confirmedPassword']);
});
