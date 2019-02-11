<?php
/**
 * Created by PhpStorm.
 * User: danielkoch
 * Date: 2019-02-11
 * Time: 19:05
 */

namespace Dion\Foa\Listeners;


use Dion\Foa\Contracts\AttributeFunctionsResolverInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class AttributeFunctions
{
    public function handle($event)
    {
        $functions = foa_objectTypes()->getFunctions($event->objectType);

        if (empty($functions)) {
            return;
        }

        $event->data = resolve(AttributeFunctionsResolverInterface::class)
            ->handle($functions, $event->data);
    }
}
