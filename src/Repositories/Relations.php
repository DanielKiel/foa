<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 20:58
 */

namespace Dion\Foa\Repositories;


use Dion\Foa\Contracts\RelationsInterface;
use Dion\Foa\Exceptions\RelationsException;
use Dion\Foa\Models\BaseObject;
use Dion\Foa\Models\ObjectType;
use Dion\Foa\Models\Relation;
use Dion\Foa\Models\RelationType;

class Relations implements RelationsInterface
{
    public function get(BaseObject $object, $relationName)
    {
        $type = $this->getRelationType($object, $relationName);

        return $this->relationalBaseQuery($object, $type)
            ->get();
    }

    public function search(BaseObject $object, $relationName, $query = '',
       array $filters = [],
       $pagination = ['per_page' => 25, 'page_name' => 'page', 'page' => null]
    )
    {
        $type = $this->getRelationType($object, $relationName);

        return foa_search()
            ->setBaseQuery($this->relationalBaseQuery($object, $type))
            ->setQuery($query, 'data')
            ->setFilters($filters)
            ->setPagination($pagination)
            ->performSearch();
    }

    public function findById($id)
    {
        return Relation::find($id);
    }

    public function upsert(BaseObject $object, $relationName, $relationAttributes)
    {
        $type = $this->getRelationType($object, $relationName);

        foreach ($relationAttributes as $mode => $objects) {
            if ($mode === 'insert') {
                foreach ($objects as $objectAttributes) {
                    $this->insertRelationObject($object, $type, $objectAttributes);
                }
            }
            elseif ($mode === 'update') {
                foreach ($objects as $objectAttributes) {
                    $this->updateRelationObject($object, $type, $objectAttributes);
                }
            }
        }
    }

    protected function insertRelationObject(BaseObject $object, RelationType $type, $relationAttributes)
    {
        array_set($relationAttributes, 'objectType', $type->targetType->name);
        $relatedObject = foa_objects()->insert($relationAttributes);

        return Relation::firstOrCreate([
            'relation_type_id' => $type->id,
            'base_id' => $object->id,
            'target_id' => $relatedObject->id
        ]);
    }

    protected function updateRelationObject(BaseObject $object, RelationType $type, $relationAttributes)
    {
        if (! array_has($relationAttributes, 'id')) {
            return $this->insertRelationObject($object, $type, $relationAttributes);
        }

        $existingObject = foa_objects()->findById(array_get($relationAttributes, 'id'));

        array_set($relationAttributes, 'objectType', $type->targetType->name);

        $relatedObject = foa_objects()->update($existingObject, $relationAttributes);

        return Relation::firstOrCreate([
            'relation_type_id' => $type->id,
            'base_id' => $object->id,
            'target_id' => $relatedObject->id
        ]);
    }

    /**
     * @param BaseObject $object
     * @param $relationName
     * @return RelationType
     * @throws RelationsException
     */
    protected function getRelationType(BaseObject $object, $relationName): RelationType
    {
        $type = $object->objectType->hasRelationTypes()->where('name', $relationName)->first();

        if (! $type instanceof RelationType) {
            throw new RelationsException('type not defined: ' . $relationName);
        }

        return $type;
    }

    protected function relationalBaseQuery(BaseObject $object, RelationType $type)
    {
        return BaseObject::join('relations', function($join) use($object, $type) {
            $join->on('target_id', 'objects.id')
                ->where('relation_type_id', $type->id)
                ->where('base_id', $object->id);
        });
    }
}
