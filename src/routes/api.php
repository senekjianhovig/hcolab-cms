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

Route::post('api/v2/utilities/upload/media', [ \hcolab\cms\controllers\FileUploadController::class , 'UploadToTemporaryAPIV2']);

Route::prefix('api/v1')->group(function () {
    Route::prefix('utilities')->group(function () {
        Route::post('upload/media', [ \hcolab\cms\controllers\FileUploadController::class , 'UploadToTemporaryAPI']);
    });

    Route::prefix('page')->group(function () {
        Route::get('{page_slug}', [hcolab\cms\controllers\PageController::class, 'renderAPI']);
    });


    Route::prefix('push-notifications')->group(function () {
        Route::get('/', [hcolab\cms\controllers\PushNotificationController::class, 'getNotifications']);
        Route::post('set-all-read', [hcolab\cms\controllers\PushNotificationController::class, 'setAllNotificationsRead']);
        Route::post('set-read', [hcolab\cms\controllers\PushNotificationController::class, 'setNotificationsRead']);
    });

   
});

