<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PostController;
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
Route::post('/custom/login', [AuthController::class, 'customLogin'])
    ->middleware(['guard:custom'])
    ->name('auth_custom_login')
;
Route::post('/register', [AuthController::class, 'register'])->name('auth_register');
Route::group(['middleware' => ['auth.jwt']], function () {
    Route::put('/token/refresh', [AuthController::class, 'refresh'])->name('auth_refresh');
    Route::prefix('vat')->middleware(['role:ROLE_PRODUCT|ROLE_HELPER'])->group(function () {
        Route::post('/check', [VatController::class, 'check'])->name('vat_check');
        Route::get('/', [VatController::class, 'list'])->name('vat_list');
        Route::get('/{id}', [VatController::class, 'get'])->name('vat_get');
        Route::put('/{id}', [VatController::class, 'update'])->name('vat_update');
        Route::delete('/{id}', [VatController::class, 'delete'])->name('vat_delete');
    });
    Route::prefix('post')->middleware(['role:ROLE_USER'])->group(function () {
        Route::get('/', [PostController::class, 'list'])->name('post_list');
        Route::get('/{id}', [PostController::class, 'get'])->name('post_get');
        Route::post('/', [PostController::class, 'create'])->name('post_create');
        Route::put('/{id}', [PostController::class, 'update'])->name('post_update');
        Route::delete('/{id}', [PostController::class, 'delete'])->name('post_delete');
    });
});
Route::prefix('file')->group(function () {
    Route::post('/', [FileController::class, 'upload'])->name('file_upload');
    Route::delete('/', [FileController::class, 'delete'])->name('file_delete');
    Route::get('/', [FileController::class, 'download'])->name('file_download');
});
