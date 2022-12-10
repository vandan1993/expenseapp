<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentsController;
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


Route::post('/login', [AuthController::class , 'login']);

Route::post('/register' , [AuthController::class , 'registerUser']);


Route::group(['middleware' => 'auth:sanctum'] , function (){
    Route::get('/logout', [AuthController::class , 'logout']);
    Route::post('/splitpayment' , [PaymentsController::class , 'makePayment']);
    Route::post('/getUserBalance', [PaymentsController::class , 'getUserBalance']);
});

Route::get('/getEveryoneBalance' , [PaymentsController::class , 'getEveryoneBalance']);