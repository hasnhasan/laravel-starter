<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $views = resource_path("views/backend");

        $this->loadViewsFrom($views, 'backend');
    }
}
