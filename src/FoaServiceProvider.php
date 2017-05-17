<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 20:20
 */

namespace Dion\Foa;


use Dion\Foa\Atlas\Registrar;
use Dion\Foa\Commands\StructParser;
use Dion\Foa\Contracts\ObjectsInterface;
use Dion\Foa\Contracts\ObjectTypesInterface;
use Dion\Foa\Contracts\RelationsInterface;
use Dion\Foa\Contracts\SearchEngineContract;
use Dion\Foa\Repositories\Objects;
use Dion\Foa\Repositories\ObjectTypes;
use Dion\Foa\Repositories\Relations;
use Dion\Foa\Repositories\SearchEngine;
use Illuminate\Support\ServiceProvider;

class FoaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //register macros
        foreach (glob(__DIR__ . "/helper/*.php") as $filename) {
            require $filename;
        }

        //run php artisan vendor:publish  --tag=migrations, so later you can run php artisan:migrate to do your migration
        $this->publishes([
            __DIR__.'/database/migrations/' => $this->app->databasePath().'/migrations',
        ], 'migrations');


        $this->publishes([
            __DIR__.'/config/foa.php' => config_path('foa.php'),
            __DIR__.'/config/struct.json' => config_path('struct.json')
        ], 'config');

        $this->app->bind(ObjectsInterface::class, Objects::class);

        $this->app->bind(ObjectTypesInterface::class, ObjectTypes::class);

        $this->app->bind(RelationsInterface::class, Relations::class);

        $this->app->bind(SearchEngineContract::class, SearchEngine::class);

        Registrar::init($this->app);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            StructParser::class
        ]);
    }
}