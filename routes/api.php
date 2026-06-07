<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

// Authentication
Route::post("/login", [AuthController::class, "login"]);
Route::post("/logout", [AuthController::class, "logout"]);

// Authenticated user check (Laravel default)
Route::get("/user", function (Request $request) {
    return $request->user();
})->middleware("auth:sanctum");

// Core resources API
Route::apiResource("users", UserController::class);
Route::apiResource("teams", TeamController::class);
Route::apiResource("projects", ProjectController::class);

// Tasks & MicroTasks API
Route::apiResource("tasks", TaskController::class)->except(["index", "show"]);
Route::post("/tasks/{task}/micro-tasks", [TaskController::class, "storeMicroTask"]);
Route::put("/micro-tasks/{microTask}", [TaskController::class, "updateMicroTask"]);
Route::delete("/micro-tasks/{microTask}", [TaskController::class, "destroyMicroTask"]);

