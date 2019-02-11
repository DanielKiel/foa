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

class ObjectsServicesController extends Controller
{
    public function getById($objectType, $id)
    {
        $object = foa_objects()->findById($id);

        if (! $object instanceof BaseObject) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => [
                    'could not load object'
                ]], 404);
        }

        if ($object->objectType->name !== $objectType) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => [
                    'could not load object from objectType ' . $objectType
                ]], 404);
        }

        return new JsonResponse($object->toArray());
    }

    /**
     * @param Request $request
     * @param $objectType
     *
     * you can search with:
     *
     *  'query' => '<query-string>'
     *
     * you can filter with:
     *
     *  'filters' => [
     *      '<property> = <compareValue>',
     *      '<property_2> NOT LIKE <compareValue 2>'
     *  ]
     *
     *  you can define size of response data with:
     *
     *  'per_page' => 10
     *
     *  you can page with:
     *  'page' => 2
     *
     */
    public function get(Request $request, $objectType)
    {
        $query = $request->input('query');
        $filters = $request->input('filters', []);
        $per_page = $request->input('per_page', 25);
        $page = $request->input('page', null);

        array_push($filters, 'objectType = ' . $objectType);

        return (array) foa_objects()->search($query, $filters, [
            'per_page' => $per_page, 'page_name' => 'page', 'page' => $page
        ]);
    }

    public function post(Request $request, $objectType)
    {
        $attributes = $request->input();

        array_set($attributes, 'objectType', $objectType);

        $result = foa_objects()->insert($attributes);

        if ($result instanceof BaseObject) {
            return new JsonResponse([
                'status' => 'success',
                'data' => $result->toArray()
            ]);
        }

        return new JsonResponse($result, 422);
    }

    public function put(Request $request, $objectType, $id)
    {
        $object = foa_objects()->findById($id);

        if (! $object instanceof BaseObject) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => [
                    'could not load object'
                ]], 404);
        }

        if ($object->objectType->name !== $objectType) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => [
                    'could not load object from objectType ' . $objectType
                ]], 404);
        }

        $attributes = $request->input();

        $result = foa_objects()->update($object, $attributes);

        if ($result instanceof BaseObject) {
            return new JsonResponse([
                'status' => 'success',
                'data' => $result->toArray()
            ]);
        }

        return new JsonResponse($result, 422);
    }

    public function delete(Request $request, $objectType, $id)
    {
        $result = foa_objects()->delete($id);

        if ($result === false) {
            return new JsonResponse([
                'status' => 'error',
                'errors' => [
                    'could not delete object '
                ]], 404);
        }

        return new JsonResponse([
            'status' => 'success',
        ]);
    }
}
