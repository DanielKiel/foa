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
    public function findById($id)
    {

    }

    public function insert(BaseObject $object, $relationName, $relationAttributes)
    {
        $type = $object->objectType->hasRelationTypes()->where('name', $relationName)->first();

        if (! $type instanceof RelationType) {
            throw new RelationsException('type not defined: ' . $relationName);
        }

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

    public function update(BaseObject $object, RelationType $type, $relationAttributes)
    {
        if (! array_has($relationAttributes. 'id')) {
            return $this->insertRelationObject($object, $type, $relationAttributes);
        }

        array_set($relationAttributes, 'objectType', $type->targetType->name);

        $object = foa_objects()->update($relationAttributes);

        return Relation::firstOrCreate([
            'relation_type_id' => $type->id,
            'base_id' => $object->id,
            'target_id' => $object->id
        ]);
    }

    protected function insertRelationObject(BaseObject $object, RelationType $type, $relationAttributes)
    {
        array_set($relationAttributes, 'objectType', $type->targetType->name);
        $object = foa_objects()->insert($relationAttributes);

        return Relation::firstOrCreate([
            'relation_type_id' => $type->id,
            'base_id' => $object->id,
            'target_id' => $object->id
        ]);
    }

    protected function updateRelationObject(BaseObject $object, RelationType $type, $relationAttributes)
    {

    }

    public function delete($id)
    {

    }

    public function upsert()
    {

    }

    public function search()
    {

    }
}
