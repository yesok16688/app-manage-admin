<?php

namespace App\Providers;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if(env('app_env') != 'production') {
            DB::listen(function (QueryExecuted $query) {
                $sqlTmpl = str_replace('?', '"%s"', $query->sql);
                Log::info(sprintf('execute sql[%d ms]: %s', $query->time, sprintf($sqlTmpl, ...$query->bindings)));
            });
        }
    }
}
