<?php

namespace hcolab\cms\providers;

use Illuminate\Support\ServiceProvider;

Class CMSServiceProvider extends ServiceProvider{
    public function boot(){
        $this->loadViewsFrom(__DIR__.'/../views/', 'CMSViews');
        $this->publishes([ __DIR__.'/../config/pages.php' => config_path('pages.php') ], 'config');
    }

    public function register(){
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }


}