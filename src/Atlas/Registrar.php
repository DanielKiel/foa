<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 17.05.17
 * Time: 10:35
 */

namespace Dion\Foa\Atlas;


use Dion\Foa\Atlas\V1\V1Service;

class Registrar
{
    public static function init($app)
    {
        if (! $app->routesAreCached()) {
            V1Service::init($app);
        }
    }
}