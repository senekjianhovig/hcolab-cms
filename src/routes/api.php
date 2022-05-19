<?php

use Illuminate\Support\Facades\Route;

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
Route::prefix('api/v1')->group(function () {
    Route::prefix('utilities')->group(function () {
        Route::post('upload/media', [ \hcolab\cms\controllers\FileUploadController::class , 'UploadToTemporaryAPI']);
    });
});

