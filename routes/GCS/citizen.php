<?php

use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('citizen')->group(function (){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',[AuthController::class,'loginCitizen']);
});


Route::prefix('citizen')->middleware(['auth:api', 'role:citizen'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/email-verification',[AuthController::class,'emailVerification']);
    Route::get('/email-verification',[AuthController::class,'sendEmailVerification']);
});
