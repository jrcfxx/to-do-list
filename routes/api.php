<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskChangeController;
use App\Http\Controllers\AuthController;


// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Grouping routes protected with auth:sanctum middleware
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Users routes
    Route::middleware(['permission:view-users'])->group(function () {
        Route::get('/users', [UsersController::class, 'index']);
        Route::get('/users/{id}', [UsersController::class, 'show']);
    });
    Route::group(['permission:create-users'], function () {
        Route::post('/users', [UsersController::class, 'create']);
    });
    Route::group(['permission:edit-users'], function () {
        Route::put('/users/{id}', [UsersController::class, 'update']);
    });
    Route::group(['permission:delete-users'], function () {
        Route::delete('/users/{id}', [UsersController::class, 'delete']);
    });

    // Task routes
    Route::group(['permission:view-tasks'], function () {
        Route::get('/tasks', [TaskController::class, 'index']);
        Route::get('/tasks/{id}', [TaskController::class, 'show']);
    });
    Route::group(['permission:create-tasks'], function () {
        Route::post('/tasks', [TaskController::class, 'create']);
    });
    Route::group(['permission:edit-tasks'], function () {
        Route::put('/tasks/{id}', [TaskController::class, 'update']);
    });
    Route::group(['permission:delete-tasks'], function () {
        Route::delete('/tasks/{id}', [TaskController::class, 'delete']);
    });

    // TaskChange routes
    Route::group(['permission:view-taskchanges'], function () {
        Route::get('/task_changes', [TaskChangeController::class, 'index']);
        Route::get('/task_changes/{taskChange}', [TaskChangeController::class, 'show']);
        Route::post('/task_changes', [TaskChangeController::class, 'create']);
    });
});

// Testing the middleware
Route::middleware(['auth:sanctum'])->get('/guard-check', function (Request $request) {
    return response()->json([
        // Name of the guard used to authenticate the user
        'guard' => Auth::getDefaultDriver(),
        // User details
        'user' => Auth::user(),
    ]);
});