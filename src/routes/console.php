<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('hcolab:generateSitemap', function () {
    (new \hcolab\cms\controllers\SitemapController)->generateSitemap();
})->purpose('Generate Sitemap of the website');


Artisan::command('hcolab:processSingleMedia {id}', function ($id) {
    (new \hcolab\cms\controllers\FileUploadController)->processSingleMedia($id);
})->purpose('Process Single Cron');


