<?php


use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});



Route::prefix('admin')->group(function (){
    Route::post('/login',[AuthController::class,'loginAdmin'])->middleware(['throttle:5,1']);;
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
    Route::patch('/users/{id}/status', [UserManagementController::class, 'updateStatus']);
    Route::get('user/search', [UserManagementController::class, 'searchUser']);


    Route::get('/roles', [PermissionController::class, 'roles']);
    Route::get('/permissions', [PermissionController::class, 'permissions']);

    Route::post('/roles', [PermissionController::class, 'createRole']);
    Route::post('/permissions', [PermissionController::class, 'createPermission']);

    Route::post('/roles/{role}/assign', [PermissionController::class, 'assignPermissions']);


    Route::prefix('audit-logs')->group(function () {
        Route::get('/',[AuditController::class,'index']);
        Route::get('/filter',[AuditController::class,'filter']);
        Route::get('/{id}', [AuditController::class, 'show']);
    });









    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::get('/complaints/{id}', [ComplaintController::class, 'show']);

    Route::put('/complaints/{id}/status', [ComplaintController::class, 'updateStatus']);

    Route::get('/complaints/{id}/timeline', [ComplaintController::class, 'timeline']);



    // Dashboard
    Route::get('/dashboard', [ReportController::class, 'dashboard']);

    // Logs
    Route::get('/logs', [ReportController::class, 'logs']);
    Route::get('/error-logs', [ReportController::class, 'errorLogs']);

    // Export
    Route::get('/export/csv', [ReportController::class, 'exportCSV']);
    Route::get('/export/pdf', [ReportController::class, 'exportPDF']);
});


