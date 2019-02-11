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

class RelationsServicesController extends Controller
{
    public function get(Request $request, $objectType, BaseObject $object, $relationName)
    {
        if ($object->objectType->name !== $objectType) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => [
                    'could not load object from objectType ' . $objectType
                ]], 404);
        }

        $query = $request->input('query');
        $filters = $request->input('filters', []);
        $per_page = $request->input('per_page', 25);
        $page = $request->input('page', null);

        array_push($filters, 'objectType = ' . $objectType);

        return foa_relations()->search($object, $relationName,$query, $filters, [
            'per_page' => $per_page, 'page_name' => 'page', 'page' => $page
        ]);
    }

    public function attach(Request $request, $objectType, BaseObject $object, $relationName, BaseObject $relatedObject)
    {
        return foa_relations()->attach($object, $relationName, $relatedObject);
    }

    public function detach(Request $request, $objectType, BaseObject $object, $relationName, BaseObject $relatedObject)
    {
        return foa_relations()->detach($object, $relationName, $relatedObject);
    }
}
