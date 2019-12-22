<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

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
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     *
     * @return void
     */
    public function map(Router $router)
    {
        $this->mapApiRoutes($router);

        $this->mapWebRoutes($router);
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param Router $router
     *
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
        $router->group(
            [
                'middleware' => 'web',
                'namespace'  => $this->namespace,
            ],
            function () use ($router) {
                require_once base_path('routes/web.php');
            }
        );
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @param Router $router
     *
     * @return void
     */
    protected function mapApiRoutes(Router $router)
    {
        $router->group(
            [
                'middleware' => 'api',
                'namespace'  => $this->namespace,
            ],
            function () use ($router) {
                require_once base_path('routes/api.php');
            }
        );
    }
}
