<?php
/**
 * Created by PhpStorm.
 * User: danielkoch
 * Date: 2019-02-08
 * Time: 17:12
 */

namespace Dion\Foa\Repositories;


use Dion\Foa\Contracts\AttributeCasterInterface;
use Dion\Foa\Contracts\AttributeFunctionsResolverInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class AttributeFunctionsResolver implements AttributeFunctionsResolverInterface
{
    public function handle(array $functions, array $objectArray)
    {
        $expressionLanguage = new ExpressionLanguage();

        foreach ($functions as $key => $function) {
            try {
                $result = $expressionLanguage->evaluate(
                    $function,
                    [
                        'obj' => $objectArray
                    ]
                );


                array_set(
                    $objectArray,
                    $key,
                    $result
                );
            }
            catch(\Exception $e) {
                Log::error($e->getMessage());
            }

        }

        return $objectArray;
    }
}
