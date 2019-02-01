<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 17.05.17
 * Time: 10:35
 */

namespace Dion\Foa\Atlas\V1;


use Route;

class V1Service
{
    public static function init($app)
    {
        if (! $app->routesAreCached()) {
            self::registerFoaAdminService();
            self::registerFoaUploadService();
            self::registerFrontendService();
        }
    }

    protected static function registerFrontendService()
    {
        Route::group(
            [
                'prefix' => 'services/v1',
                'namespace' => '\Dion\Foa\HTTP\Controllers'
            ],
            function() {
                Route::get('/{objectTypeName}', [
                    'as' => 'services.frontend.get', 'uses' => 'ObjectsServicesController@get'
                ]);
                Route::get('/{objectTypeName}/{id}', [
                    'as' => 'services.frontend.getById', 'uses' => 'ObjectsServicesController@getById'
                ]);
            }
        );
    }

    protected static function registerFoaUploadService()
    {
        Route::group(
            [
                'prefix' => 'services/upload/v1',
                'middleware' => [
                    'auth:api',
                ],
                'namespace' => '\Dion\Foa\HTTP\Controllers'
            ],
            function() {
                Route::post('/{objectType}', [
                    'as' => 'services.upload', 'uses' => 'UploadServiceController@upload'
                ]);

                Route::get('/{objectType}/{filename}', [
                    'as' => 'services.upload', 'uses' => 'UploadServiceController@get'
                ]);
            }
        );
    }

    protected static function registerFoaAdminService()
    {
        Route::group(
            [
                'prefix' => 'services/admin/v1',
                'middleware' => [
                    'auth:api',
                ],
                'namespace' => '\Dion\Foa\HTTP\Controllers'
            ],
            function() {
                Route::get('/{objectTypeName}', [
                    'as' => 'services.objects.get', 'uses' => 'ObjectsServicesController@get'
                ]);

                Route::get('/{objectTypeName}/{id}', [
                    'as' => 'services.objects.getById', 'uses' => 'ObjectsServicesController@getById'
                ]);

                Route::post('/{objectTypeName}', [
                    'as' => 'services.objects.insert', 'uses' => 'ObjectsServicesController@post'
                ]);

                Route::put('/{objectTypeName}/{objectId}', [
                    'as' => 'services.objects.update', 'uses' => 'ObjectsServicesController@put'
                ]);

                Route::delete('/{objectTypeName}/{objectId}', [
                    'as' => 'services.objects.delete', 'uses' => 'ObjectsServicesController@delete'
                ]);
            }
        );
    }
}
