<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
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
    Route::get(
        '/complaints',
        [ComplaintController::class, 'index']
    );
    Route::put(
        '/complaints/{id}/status',
        [ComplaintController::class, 'updateStatus']
    );

    Route::get('/complaints/{id}/timeline', [ComplaintController::class, 'show']);
    Route::get('complaints/{id}',[ComplaintController::class, 'getComplaintById']);
    Route::post('complaints/{id}/addMessage',[ComplaintController::class, 'addMessageToComplaint']);
});

