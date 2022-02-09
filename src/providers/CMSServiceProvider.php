<?php

namespace hcolab\cms\providers;

use Illuminate\Support\ServiceProvider;

Class CMSServiceProvider extends ServiceProvider{
    public function boot(){
        $this->loadViewsFrom(__DIR__.'/../views/', 'CMSViews');
        $this->publishes([ __DIR__.'/../config/pages.php' => config_path('pages.php') ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \hcolab\cms\Console\MakePage::class
            ]);
        }

    }

    public function register(){
        include_once(__DIR__.'/../helpers/helpers.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }


}