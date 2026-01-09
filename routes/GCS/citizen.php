<?php

use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintTypeController;
use App\Http\Controllers\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('citizen')->group(function (){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',[AuthController::class,'loginCitizen'])->middleware('throttle:1000,1');
    Route::post('/email-verification',[AuthController::class,'emailVerification']);

});

    Route::middleware('auth:api')->post('/fcm-token', [FcmTokenController::class, 'save']);


Route::prefix('citizen')->middleware(['auth:api', 'role:citizen','check.status'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/email-verification',[AuthController::class,'sendEmailVerification']);
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::post('/complaints/{id}', [ComplaintController::class, 'update']);
    Route::get('/Complaint-status/{id}',[ComplaintController::class,'show']);
    Route::get('complaints',[ComplaintController::class, 'getAllcomplaint']);
    Route::get('departments',[DepartmentController::class,'index']);
    Route::get('types',[ComplaintTypeController::class,'index']);
});


Route::middleware('auth:api')->prefix('citizen/notifications')->group(function () {

    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'unread']);
    Route::get('/unread-count', [NotificationController::class, 'unreadCount']);

    Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);

    Route::delete('/{id}', [NotificationController::class, 'destroy']);
});
