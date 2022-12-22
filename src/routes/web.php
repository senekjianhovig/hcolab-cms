<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'web'] , function(){

Route::group(['prefix' => 'cms'], function(){
    Route::post('/login' , [ \hcolab\cms\controllers\UserController::class , 'login'])->name('login');
    Route::get('/login' , [ \hcolab\cms\controllers\UserController::class , 'renderLoginPage'])->name('login');

    Route::post('/force-change-password' , [ \hcolab\cms\controllers\UserController::class , 'forceChangePassword'])->name('force-change-password');
    Route::get('/force-change-password' , [ \hcolab\cms\controllers\UserController::class , 'renderForceChangePassword'])->name('force-change-password');
});

Route::group(['prefix' => 'cms' , 'middleware' => [\hcolab\cms\middlewares\CMSAuth::class,\hcolab\cms\middlewares\CMSSetup::class]], function(){
    Route::post('/upload', [ \hcolab\cms\controllers\FileUploadController::class , 'UploadToTemporary']); 
    Route::get('/',  function(){ return view('CMSViews::dashboard.index');})->name('dashboard');

    Route::get('/logout' , [\hcolab\cms\controllers\UserController::class , 'logout'])->name('logout');
    Route::get('/change-password' , [\hcolab\cms\controllers\UserController::class , 'renderChangePassword'])->name('change-password');
    Route::post('/change-password' , [\hcolab\cms\controllers\UserController::class , 'changePassword'])->name('change-password');

    Route::get('/role-permissions/{id}/{token}' , [\hcolab\cms\controllers\UserController::class , 'renderRolePermissions'])->name('role-permissions');   
    Route::post('/role-permissions/{id}/{token}' , [\hcolab\cms\controllers\UserController::class , 'rolePermissions'])->name('role-permissions');
 
    
    Route::prefix('theme-builder')->group(function () {
        Route::get('/{id}',  function($id){ return view('CMSViews::page.theme-builder' , ['id' => $id]);});
        Route::get('section/{section}',  function($section){ return view('CMSViews::components.theme-builder-section' , ['section' => $section]);});
    });

    Route::get('generate', function(){ return view('CMSViews::generate');});
    Route::prefix('page')->group(function () {
        Route::get('{page_slug}', [hcolab\cms\controllers\PageController::class, 'render'])->name('page');
        Route::get('{page_slug}/create', [hcolab\cms\controllers\PageController::class, 'create'])->name('page.create');
        Route::post('{page_slug}/save', [hcolab\cms\controllers\PageController::class, 'save'])->name('page.save');
        Route::get('{page_slug}/show/{id}', [hcolab\cms\controllers\PageController::class, 'show'])->name('page.show');
        Route::get('{page_slug}/edit/{id}', [hcolab\cms\controllers\PageController::class, 'edit'])->name('page.edit');
        Route::post('{page_slug}/delete/{id}', [hcolab\cms\controllers\PageController::class, 'delete'])->name('page.delete');
        Route::post('{page_slug}/query', [hcolab\cms\controllers\PageController::class, 'query'])->name('page.table-data');
    });
});


});
