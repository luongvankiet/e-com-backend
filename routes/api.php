<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::middleware('api')->group(function () {
        Route::get('me', [AuthController::class, 'getAuthenticatedUser']);
        Route::get('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('api')->group(function () {
    // User Routes
    Route::get('users/status-count', [UserController::class, 'statusCount']);
    Route::post('users/restore-many', [UserController::class, 'restoreMany']);
    Route::post('users/delete-many', [UserController::class, 'deleteMany']);
    Route::post('users/permanent-delete-many', [UserController::class, 'permanentDeleteMany']);
    Route::apiResource('users', UserController::class);

    // Role Routes
    Route::post('roles/delete-many', [RoleController::class, 'deleteMany']);
    Route::apiResource('roles', RoleController::class);

    // Permission Routes
    Route::apiResource('permissions', PermissionController::class);

    Route::apiResource('customers', CustomerController::class);
});
