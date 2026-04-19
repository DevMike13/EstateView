<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(database_path('migrations'));
 
        if (is_dir(database_path('migrations/prpcmblmts'))) {
            $this->loadMigrationsFrom(database_path('migrations/prpcmblmts'));
        }
    }
}
