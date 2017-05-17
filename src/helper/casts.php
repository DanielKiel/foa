<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 17.05.17
 * Time: 09:37
 */

if (! function_exists('recursiveToArray')) {
    //use it to get array clean of some stdClass objects, for example on incoming inputs
    function recursiveToArray(array $array) {
        $return = [];

        foreach ($array as $key => $value) {

            if ($value instanceof \stdClass) {
                $value = (array) $value;
            }

            if (is_array($value)) {
                $value = recursiveToArray($value);
            }

            if ($value instanceof \Illuminate\Support\Collection) {
                $value = recursiveToArray($value->toArray());
            }

            if (is_object($value) && method_exists($value, 'toArray')) {
                try {
                    $value = recursiveToArray($value->toArray());
                }
                catch (\Exception $e) {

                }
            }

            $return[$key] = $value;
        }

        return $return;
    }
}