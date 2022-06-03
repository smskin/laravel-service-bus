<?php

use Illuminate\Support\Facades\Route;
use SMSkin\ServiceBus\Http\Controllers\ConsumerController;

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

Route::post('consumer', [ConsumerController::class, '__invoke'])->name('consumer');