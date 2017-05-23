<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 21:19
 */

if (! function_exists('foa_objects')) {
    /**
     * @return \Dion\Foa\Repositories\Objects
     */
    function foa_objects() {
        return app(\Dion\Foa\Contracts\ObjectsInterface::class);
    }
}

if (! function_exists('foa_objectTypes')) {
    /**
     * @return \Dion\Foa\Repositories\ObjectTypes
     */
    function foa_objectTypes() {
        return app(\Dion\Foa\Contracts\ObjectTypesInterface::class);
    }
}

if (! function_exists('foa_relations')) {
    /**
     * @return \Dion\Foa\Repositories\Relations
     */
    function foa_relations() {
        return app(\Dion\Foa\Contracts\RelationsInterface::class);
    }
}

if (! function_exists('foa_search')) {
    /**
     * @return \Dion\Foa\Repositories\SearchEngine
     */
    function foa_search() {
        return app(\Dion\Foa\Contracts\SearchEngineContract::class);
    }
}

if (! function_exists('foa_upload')) {
    /**
     * @return \Dion\Foa\Repositories\Uploads
     */
    function foa_upload() {
        return app(\Dion\Foa\Contracts\UploadInterface::class);
    }
}