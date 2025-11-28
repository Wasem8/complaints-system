<?php

use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('admin')->group(function (){
    Route::post('/login',[AuthController::class,'loginAdmin']);
});

Route::prefix('admin')->middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/departments',  [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);

    Route::get('/employees',  [EmployeeController::class, 'index']);
    Route::post('/employees', [EmployeeController::class, 'store']);


    Route::get('/users', [UserManagementController::class, 'index']);
    Route::get('/users/{id}', [UserManagementController::class, 'show']);
    Route::post('/users', [UserManagementController::class, 'store']);
    Route::put('/users/{id}', [UserManagementController::class, 'update']);
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);

    Route::get('/roles', [PermissionController::class, 'roles']);
    Route::get('/permissions', [PermissionController::class, 'permissions']);

    Route::post('/roles', [PermissionController::class, 'createRole']);
    Route::post('/permissions', [PermissionController::class, 'createPermission']);

    Route::post('/roles/{role}/assign', [PermissionController::class, 'assignPermissions']);







    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::get('/complaints/{id}', [ComplaintController::class, 'show']);

    Route::put('/complaints/{id}/status', [ComplaintController::class, 'updateStatus']);
    Route::post('/complaints/{id}/notes', [ComplaintController::class, 'addNote']);

    Route::get('/complaints/{id}/timeline', [ComplaintController::class, 'timeline']);

    Route::put('/complaints/{id}/archive', [ComplaintController::class, 'archive']);
});

