<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Dealer Sales Analytics System API',
        'version' => '1.0',
    ]);
});

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('/user', [App\Http\Controllers\AuthController::class, 'user']);
    
    Route::prefix('upload')->group(function () {
        Route::post('/file', [App\Http\Controllers\UploadController::class, 'upload'])->middleware('role:admin');
        Route::get('/template', [App\Http\Controllers\UploadController::class, 'template']);
    });
    
    Route::prefix('etl')->middleware('role:admin')->group(function () {
        Route::post('/validate', [App\Http\Controllers\EtlController::class, 'validate']);
        Route::post('/clean', [App\Http\Controllers\EtlController::class, 'clean']);
        Route::post('/import', [App\Http\Controllers\EtlController::class, 'import']);
        Route::get('/summary/{id}', [App\Http\Controllers\EtlController::class, 'summary']);
    });
    
    Route::prefix('dashboard')->group(function () {
        Route::get('/kpi-cards', [App\Http\Controllers\DashboardController::class, 'kpiCards']);
        Route::get('/charts/sales-trend', [App\Http\Controllers\DashboardController::class, 'salesTrend']);
        Route::get('/charts/revenue-trend', [App\Http\Controllers\DashboardController::class, 'revenueTrend']);
        Route::get('/charts/profit-trend', [App\Http\Controllers\DashboardController::class, 'profitTrend']);
        Route::get('/ranking/dealers', [App\Http\Controllers\DashboardController::class, 'dealerRanking']);
        Route::get('/ranking/products', [App\Http\Controllers\DashboardController::class, 'productRanking']);
    });
    
    Route::prefix('reports')->group(function () {
        Route::post('/export/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf']);
        Route::post('/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel']);
    });
    
    Route::prefix('filters')->group(function () {
        Route::get('/periods', [App\Http\Controllers\FilterController::class, 'periods']);
        Route::get('/branches', [App\Http\Controllers\FilterController::class, 'branches']);
        Route::get('/dealers', [App\Http\Controllers\FilterController::class, 'dealers']);
    });
});
