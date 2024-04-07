<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 追加
use Illuminate\Support\Facades\View;

class ConsigneeServiceProvider extends ServiceProvider
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
      // 以下を追加
      View::composer('*','App\Http\View\Composers\ConsigneeComposer');
    }
}
