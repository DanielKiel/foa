<?php
/**
 * Created by PhpStorm.
 * User: danielkoch
 * Date: 2019-02-11
 * Time: 19:33
 */

namespace Dion\Foa\Contracts;


interface AttributeFunctionsResolverInterface
{
    public function handle(array $functions, array $objectArray);
}
