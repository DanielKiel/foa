<?php
/**
 * Created by PhpStorm.
 * User: danielkoch
 * Date: 2019-02-08
 * Time: 17:12
 */

namespace Dion\Foa\Repositories;


use Dion\Foa\Contracts\AttributeCasterInterface;

class AttributeCaster implements AttributeCasterInterface
{
    public function password($value)
    {
        return encrypt($value);
    }
}
