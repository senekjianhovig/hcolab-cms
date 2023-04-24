<?php

namespace hcolab\cms\controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\Sitemap\SitemapGenerator;

class SitemapController extends Controller
{
    public function generateSitemap(){
        SitemapGenerator::create(config('app.url'))
        ->getSitemap()
        ->writeToDisk('public', 'sitemap.xml');
            // ->writeToFile(public_path('sitemap.xml'));

            return true;
    }
}