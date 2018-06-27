<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 20:50
 */

namespace Dion\Foa\Repositories;


use Dion\Foa\Contracts\ObjectsInterface;
use Dion\Foa\Events\DataDefined;
use Dion\Foa\Models\Object;
use Dion\Foa\Models\ObjectType;

class Objects implements ObjectsInterface
{
    /** @var array */
    public $whiteList = [
        'id', 'objecttypes_id', 'created_at', 'deleted_at', 'updated_at', 'data'
    ];

    public $objectType;

    public $errors = [];

    public function setObjectType(ObjectType $objectType)
    {
        $this->objectType = $objectType;
    }

    public function findById($id)
    {
        return Object::find($id);
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function insert(array $attributes = [])
    {
        $attributes = $this->prepareAttributes($attributes);

        if ($this->validate($this->objectType, $attributes) === false) {
            return [
                'status' => 'error',
                'errors' => $this->errors
            ];
        }

        return Object::create($attributes);
    }

    /**
     * @param Object $object
     * @param array $attributes
     * @return mixed
     */
    public function update(Object $object, array $attributes = [])
    {
        //need to inject values stored till yet in data or at object base properties
        array_set($attributes, 'data', recursiveToArray((array) $object->data));

        if (! array_has($attributes, 'objecttypes_id')) {
            array_set($attributes, 'objecttypes_id', $object->objecttypes_id);
        }

        $attributes = $this->prepareAttributes($attributes);

        if ($this->validate($this->objectType, $attributes) === false) {
            return [
                'status' => 'error',
                'errors' => $this->errors
            ];
        }

        $object->update($attributes);

        return $object->fresh();
    }

    public function delete($id)
    {
        return Object::destroy($id);
    }

    public function upsert()
    {

    }

    public function search(
        $query = '',
        array $filters = [],
        $pagination = ['per_page' => 25, 'page_name' => 'page', 'page' => null]
    )
    {
        return foa_search()
            ->setBaseQuery(new Object())
            ->setQuery($query, 'data')
            ->setFilters($filters)
            ->setPagination($pagination)
            ->performSearch();
    }

    private function validate(ObjectType $objectType, array $attributes)
    {
        $validationRules = foa_objectTypes()->getValidationRules($objectType);

        return true;
    }

    private function prepareAttributes(array $attributes = [])
    {
        $return = [];

        foreach ($attributes as $property => $attribute) {
            if (in_array($property, $this->whiteList)) {
                array_set( $return, $property, $attribute );

                array_forget($attributes, $property);
            }
        }

        if (array_has($return, 'objecttypes_id')) {
            $this->objectType = foa_objectTypes()->findById(array_get($return, 'objecttypes_id'));
        }

        if (! array_has($return, 'objecttypes_id') && array_has($attributes, 'objectType')) {
            $this->objectType = foa_objectTypes()->findByName(array_get($attributes, 'objectType'));

            if ($this->objectType instanceof ObjectType) {
                array_set($return, 'objecttypes_id', $this->objectType->id);
            }
        }

        //transform the rest to data
        $data = array_get($return, 'data', []);

        foreach ($attributes as $property => $attribute) {
            array_set($data, $property, $attribute);
        }

        event($event = new DataDefined($this->objectType, $data));

        array_set($return, 'data', $event->data);

        return $return;

    }
}