<?php

use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('employee')->group(function (){
    Route::post('/login',[AuthController::class,'loginEmployee']);

});

Route::prefix('employee')->middleware(['auth:api', 'role:employee'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

