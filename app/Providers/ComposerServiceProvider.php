<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(
            'layouts.header', 'App\Http\ViewComposers\HeaderComposer'
        );
        View::composer(
            'layouts.minicart', 'App\Http\ViewComposers\CartComposer'
        );
    }
}
