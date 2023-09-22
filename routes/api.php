<?php

use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
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

Route::controller(UserController::class)->group(function () {
    Route::post('users', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/show', [TransactionController::class, 'allTransaction']);
    Route::get('/deposit', [TransactionController::class, 'getDepositData']);
    Route::post('/deposit', [TransactionController::class, 'storeDeposit']);
    Route::get('/withdrawal', [TransactionController::class, 'getWithdrawalData']);
    Route::post('/withdrawal', [TransactionController::class, 'storeWithdrawal']);

    //logout route
    Route::post('/logout', [UserController::class, 'logout']);
});
