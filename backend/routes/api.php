<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\EtlController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\PeriodController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::prefix('upload')->middleware('throttle:30,5')->group(function () {
        Route::post('/file', [UploadController::class, 'upload']);
        Route::get('/template', [UploadController::class, 'template']);
    });

    Route::prefix('etl')->middleware('throttle:20,5')->group(function () {
        Route::post('/validate', [EtlController::class, 'validate']);
        Route::post('/clean', [EtlController::class, 'clean']);
        Route::post('/import', [EtlController::class, 'import']);
        Route::get('/summary/{id}', [EtlController::class, 'summary']);
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [KpiController::class, 'dashboard']);
        Route::get('/filters', [KpiController::class, 'filters']);
        Route::get('/drilldown', [KpiController::class, 'drilldown']);
    });

    Route::prefix('kpi')->group(function () {
        Route::get('/cards', [KpiController::class, 'kpiCards']);
        Route::get('/monthly-trend', [KpiController::class, 'monthlyTrend']);
        Route::get('/dealer-rankings', [KpiController::class, 'dealerRankings']);
        Route::get('/product-rankings', [KpiController::class, 'productRankings']);
    });

    Route::prefix('insights')->group(function () {
        Route::get('/', [InsightController::class, 'index']);
    });

    Route::prefix('export')->middleware('throttle:10,5')->group(function () {
        Route::get('/csv', [ExportController::class, 'csv']);
        Route::get('/html', [ExportController::class, 'html']);
    });

    Route::middleware('throttle:60,1')->prefix('manage')->group(function () {
        Route::get('dealers', [MasterDataController::class, 'index']);
        Route::post('dealers', [MasterDataController::class, 'store']);
        Route::put('dealers/{id}', [MasterDataController::class, 'update']);
        Route::delete('dealers/{id}', [MasterDataController::class, 'destroy']);
        Route::get('products', [MasterDataController::class, 'index']);
        Route::post('products', [MasterDataController::class, 'store']);
        Route::put('products/{id}', [MasterDataController::class, 'update']);
        Route::delete('products/{id}', [MasterDataController::class, 'destroy']);
        Route::get('branches', [MasterDataController::class, 'index']);
        Route::post('branches', [MasterDataController::class, 'store']);
        Route::put('branches/{id}', [MasterDataController::class, 'update']);
        Route::delete('branches/{id}', [MasterDataController::class, 'destroy']);
        Route::get('periods', [PeriodController::class, 'index']);
        Route::delete('periods/{id}', [PeriodController::class, 'destroy']);
        Route::post('kpi/recalculate', [KpiController::class, 'recalculate']);
    });

    Route::prefix('audit')->group(function () {
        Route::get('/', [AuditLogController::class, 'index']);
    });
});
