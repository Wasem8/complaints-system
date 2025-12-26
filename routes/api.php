<?php

use App\Http\Controllers\Api\FcmTokenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RelationController;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});

Route::get('/test-fcm', function(FcmService $fcmService) {
    $mockToken = 'dummy_token_for_testing';

    $response = $fcmService->sendNotification(
        $mockToken,
        'اختبار إشعار',
        'هذا إشعار تجريبي بدون FCM Token حقيقي',
        ['type' => 'test']
    );

    logger()->info('FCM test response', $response);

    return response()->json([
        'message' => 'تم تجربة الإشعار (لن يصل فعليًا)',
        'fcm_response' => $response
    ]);
});

Route::get('/who-am-i', function () {
    return response()->json([
        'port' => $_SERVER['SERVER_PORT'],
        'time' => now()->toDateTimeString()
    ]);
});

