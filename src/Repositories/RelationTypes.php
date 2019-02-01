<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 20:58
 */

namespace Dion\Foa\Repositories;


use Dion\Foa\Contracts\RelationsInterface;
use Dion\Foa\Contracts\RelationTypesInterface;
use Dion\Foa\Exceptions\RelationsException;
use Dion\Foa\Models\ObjectType;
use Dion\Foa\Models\RelationType;

class RelationTypes implements RelationTypesInterface
{
    protected $defaultVariant = 'hasOne';

    public function resolveDefinition(ObjectType $objectType)
    {
        $relations = recursiveToArray((array)  $objectType->rules->relations);

        if (empty($relations)) {
            return;
        }

        foreach ($relations as $relation) {
            $this->validateRelationArray($relation);

            $targetType = foa_objectTypes()->findByName(array_get($relation, 'target_type'));

            if (! $targetType instanceof ObjectType) {
                throw new RelationsException('target type cannot be found: ' . array_get($relation, 'target_type'));
            }

            $variant = array_get($relation, 'variant', $this->defaultVariant);
            $name = array_get($relation, 'name');
            $inverse_name = array_get($relation, 'inverse_name');

            $existingRelationType = RelationType::where('base_type_id', $objectType->id)
                ->where('target_type_id', $targetType->id)
                ->first();

            if (! $existingRelationType instanceof RelationType) {
                $this->insert([
                    'base_type_id' => $objectType->id,
                    'target_type_id' => $targetType->id,
                    'variant' => $variant,
                    'name' => $name,
                    'inverse_name' => $inverse_name
                ]);

                continue;
            }

            $this->update($existingRelationType, [
                'variant' => $variant,
                'name' => $name,
                'inverse_name' => $inverse_name
            ]);
        }
    }

    public function findById($id)
    {
        return RelationType::find($id);
    }

    public function insert(array $data)
    {
        return RelationType::create($data);
    }

    public function update(RelationType $relationType, array $data)
    {
        $relationType->update($data);

        return $relationType->fresh();
    }

    public function delete($id)
    {

    }

    public function search()
    {

    }

    protected function validateRelationArray(array $relation)
    {
        if (! array_has($relation, 'target_type')) {
            throw new RelationsException('target_type must be defined');
        }

        if (! array_has($relation, 'name')) {
            throw new RelationsException('name must be defined');
        }

        if (! array_has($relation, 'inverse_name')) {
            throw new RelationsException('inverse_name must be defined');
        }
    }
}
