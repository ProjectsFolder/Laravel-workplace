<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('auth_login');
Route::group(['middleware' => 'auth.jwt'], function () {
    Route::put('/token/refresh', [AuthController::class, 'refresh'])->name('auth_refresh');
    Route::prefix('vat')->group(function () {
        Route::post('/check', [VatController::class, 'check'])->name('vat_check');
        Route::get('/', [VatController::class, 'list'])->name('vat_list');
        Route::get('/{id}', [VatController::class, 'get'])->name('vat_get');
        Route::put('/{id}', [VatController::class, 'update'])->name('vat_update');
        Route::delete('/{id}', [VatController::class, 'delete'])->name('vat_delete');
    });
});
