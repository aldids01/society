<?php

use App\Http\Controllers\Api\V1\GrainController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::ApiResource('/loans', LoanController::class);
    Route::ApiResource('/grains', GrainController::class);
    Route::ApiResource('/members', MemberController::class);
});
