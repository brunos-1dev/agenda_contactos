<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OrgService;

class AppServiceProvider extends ServiceProvider
{
    /** Registrar bindings en el contenedor */
    public function register(): void
    {
        // Disponibiliza el servicio como app('org')
        $this->app->singleton('org', OrgService::class);
    }

    /** Bootstrap de la app */
    public function boot(): void
    {
        //
    }
}
