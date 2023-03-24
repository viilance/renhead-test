<?php

use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ProfessorController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TraderController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);

// approvals
Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::patch('/approvals/{id}/approve', [ApprovalController::class, 'approve']);
    Route::patch('/approvals/{id}/disapprove', [ApprovalController::class, 'disapprove']);
    Route::apiResource('approvals', ApprovalController::class);
});

// professors
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('professors', ProfessorController::class);
});

// traders
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('traders', TraderController::class);
});

// jobs
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('jobs', JobController::class);
});

// report
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/report/earnings', [ReportController::class, 'earningsReport']);
});
