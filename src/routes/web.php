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

Route::prefix('cms')->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'render'])->name('dashboard');
    Route::get('generate', function(){ return view('CMSViews::generate');});
    Route::prefix('page')->group(function () {
        Route::get('{page_slug}', [hcolab\cms\controllers\PageController::class, 'render'])->name('page');
        Route::get('{page_slug}/create', [hcolab\cms\controllers\PageController::class, 'create'])->name('page.create');
        Route::post('{page_slug}/save', [hcolab\cms\controllers\PageController::class, 'save'])->name('page.save');

        Route::get('{page_slug}/show/{id}', [hcolab\cms\controllers\PageController::class, 'show'])->name('page.show');
        Route::get('{page_slug}/edit/{id}', [hcolab\cms\controllers\PageController::class, 'edit'])->name('page.edit');

        Route::post('{page_slug}/query', [hcolab\cms\controllers\PageController::class, 'query'])->name('page.table-data');

        
    });
});