<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * 父类 RouteServiceProvider 的 boot() 方法中会调用 $this->loadRoutes();
     * $this->loadRoutes() 方法中，若 map 方法(本方法)存在，则调用本方法
     *
     * @return void
     */
    public function map()
    {
        if(isset($_SERVER['HTTP_HOST']) && stripos($_SERVER['HTTP_HOST'], 'admin.uparpu.com') === false){
            // channel
            $this->mapChannelRoutes();
        } else if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api') === 0) {
            // api
            $this->mapApiRoutes();
        } else{
            // admin
            $this->mapWebRoutes();
        }
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }


    protected function mapChannelRoutes(){
        Route::middleware('web')
            ->namespace($this->namespace. '\Channel')
            ->group(base_path('routes/channel.php'));
    }


    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
