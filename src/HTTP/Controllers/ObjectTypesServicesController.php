<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 17.05.17
 * Time: 09:48
 */

namespace Dion\Foa\HTTP\Controllers;


use App\Http\Controllers\Controller;
use Dion\Foa\Models\BaseObject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ObjectTypesServicesController extends Controller
{
    public function describe(Request $request, $objectType)
    {
        return foa_objectTypes()->describe($objectType);
    }
}
