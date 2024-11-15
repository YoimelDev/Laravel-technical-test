<?php

use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\RoleChangeRequestController;
use App\Http\Controllers\Api\V1\CurrencyConversionController;

// Rutas públicas
Route::prefix('v1')->group(function () {
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
});

// Rutas JSON:API autenticadas
JsonApiRoute::server('v1')
    ->middleware('auth:sanctum')
    ->prefix('v1')
    ->resources(function ($server) {
        $server->resource('users', JsonApiController::class)
            ->relationships(function ($relationships) {
                $relationships->hasOne('company');
                $relationships->hasMany('roleChangeRequests');
            });

        $server->resource('companies', JsonApiController::class)
            ->relationships(function ($relationships) {
                $relationships->hasOne('user');
                $relationships->hasMany('activityTypes');
            });

        $server->resource('activity-types', JsonApiController::class)
            ->relationships(function ($relationships) {
                $relationships->hasMany('companies');
            });
            
        $server->resource('role-change-requests', JsonApiController::class)
            ->relationships(function ($relationships) {
                $relationships->hasOne('user');
                $relationships->hasOne('processor');
            });
    });

// Rutas personalizadas autenticadas
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::post('/role-requests', [RoleChangeRequestController::class, 'store']);
    Route::patch('/role-requests/{request}/process', [RoleChangeRequestController::class, 'process']);
    
    Route::post('/currency/convert', [CurrencyConversionController::class, 'convert']);
    Route::get('/currency/history', [CurrencyConversionController::class, 'history']);
});