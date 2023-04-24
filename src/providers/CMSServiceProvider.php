<?php

namespace hcolab\cms\providers;

use Illuminate\Support\ServiceProvider;

Class CMSServiceProvider extends ServiceProvider{
    public function boot(){
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->loadViewsFrom(__DIR__.'/../views/', 'CMSViews');
        $this->publishes([ __DIR__.'/../config/pages.php' => config_path('pages.php') ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \hcolab\cms\console\MakePage::class,
                \hcolab\cms\console\MakeSection::class
            ]);
        }

        $this->publishes([ __DIR__.'/../../public' => public_path('hcolab/cms') ], 'public');
    }

    public function register(){
        include_once(__DIR__.'/../helpers/helpers.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/console.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }


}