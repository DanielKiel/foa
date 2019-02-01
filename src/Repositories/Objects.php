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
use Dion\Foa\Exceptions\ObjectsException;
use Dion\Foa\Models\BaseObject;
use Dion\Foa\Models\ObjectType;
use Dion\Foa\Rules\NotAllowed;
use Illuminate\Support\Facades\Validator;

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

        return $this;
    }

    public function findById($id)
    {
        return BaseObject::find($id);
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function insert(array $attributes = [])
    {
        $attributes = $this->prepareAttributes($attributes);

        //remove the relation names here if ncessary
        $relationNames = foa_objectTypes()->getReadableRelationTypesArray($this->objectType);
        $relationAttributes = [];

        if (! empty($relationNames)) {
            $relationAttributes = array_only($attributes['data'], array_keys($relationNames));
            array_forget($attributes['data'], array_keys($relationNames));
        }

        if ($this->validate($this->objectType, $attributes) === false) {
            return (new FailedObject($this->errors))->codeFailure();
        }

        $attributes = $this->castAttributes($attributes);

        $object = BaseObject::create($attributes);

        if (! empty($relationAttributes)) {
            foreach ($relationAttributes as $relationName => $relationInsert) {
                foa_relations()->upsert($object, $relationName, $relationInsert);
            }
        }

        return $object;
    }

    /**
     * @param BaseObject $object
     * @param array $attributes
     * @return mixed
     */
    public function update(BaseObject $object, array $attributes = [])
    {
        //need to inject values stored till yet in data or at object base properties
        array_set($attributes, 'data', recursiveToArray((array) $object->data));

        if (! array_has($attributes, 'objecttypes_id')) {
            array_set($attributes, 'objecttypes_id', $object->objecttypes_id);
        }

        $attributes = $this->prepareAttributes($attributes);

        //remove the relation names here if ncessary
        $relationNames = foa_objectTypes()->getReadableRelationTypesArray($this->objectType);
        $relationAttributes = [];

        if (! empty($relationNames)) {
            $relationAttributes = array_only($attributes['data'], array_keys($relationNames));
            array_forget($attributes['data'], array_keys($relationNames));
        }

        if ($this->validate($this->objectType, $attributes) === false) {
            return (new FailedObject($this->errors))->codeFailure();
        }

        $attributes = $this->castAttributes($attributes);

        $object->update($attributes);

        if (! empty($relationAttributes)) {
            foreach ($relationAttributes as $relationName => $relationInsert) {
                foa_relations()->upsert($object, $relationName, $relationInsert);
            }
        }

        return $object->fresh();
    }

    public function delete($id)
    {
        return BaseObject::destroy($id);
    }

    public function upsert()
    {

    }

    public function searchByObjectType(
        $query = '',
        array $filters = [],
        $pagination = ['per_page' => 25, 'page_name' => 'page', 'page' => null]
    )
    {
        if (! $this->objectType instanceof ObjectType) {
            throw new ObjectsException('searchByObjectType needs an objectType be defined to can search');
        }

        return foa_search()
            ->setBaseQuery((new BaseObject())->where('objecttypes_id', $this->objectType->id))
            ->setQuery($query, 'data')
            ->setFilters($filters)
            ->setPagination($pagination)
            ->performSearch();
    }

    public function search(
        $query = '',
        array $filters = [],
        $pagination = ['per_page' => 25, 'page_name' => 'page', 'page' => null]
    )
    {
        return foa_search()
            ->setBaseQuery(new BaseObject())
            ->setQuery($query, 'data')
            ->setFilters($filters)
            ->setPagination($pagination)
            ->performSearch();
    }

    private function validate(ObjectType $objectType, array $attributes)
    {
        $validationRules = foa_objectTypes()->getValidationRules($objectType);

        if (empty($validationRules)) {
            return true;
        }

        $setup = foa_objectTypes()->getSetup($objectType);
        $schemaSetup = array_get($setup, 'schema');

        if ($schemaSetup === 'exact') {
            $schema = foa_objectTypes()->getSchema($objectType);

            foreach (array_get($attributes, 'data') as $attribute => $value) {
                if (! array_has($schema, $attribute) && $attribute !== 'objectType') {
                    array_set($validationRules, $attribute, new NotAllowed());
                }
            }
        }

        $validator = Validator::make(array_get($attributes, 'data'), $validationRules);

        if($validator->fails()) {
            $this->errors = $validator->errors();

            return false;
        }

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

        array_set($return, 'data', $data);

        return $return;

    }

    public function castAttributes($attributes)
    {
        event($event = new DataDefined($this->objectType, array_get($attributes, 'data')));

        array_set($attributes, 'data', $event->data);

        return $attributes;
    }
}
