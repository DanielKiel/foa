<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 20:58
 */

namespace Dion\Foa\Repositories;


use Dion\Foa\Contracts\ObjectTypesInterface;
use Dion\Foa\Models\ObjectType;
use Illuminate\Database\Eloquent\Collection;

class ObjectTypes implements ObjectTypesInterface
{
    private $search;

    public function describe(string $name)
    {
        $objectType = $this->findByName($name);

        $cacheable = cache()->rememberForever('desc:' . $objectType->name, function () use ($objectType) {
            return [
                'schema' => foa_objectTypes()->getSchema($objectType),
                'validation' => foa_objectTypes()->getValidationRules($objectType),
                'setup' => foa_objectTypes()->getSetup($objectType),
                'relations' => $this->getReadableRelationTypesArray($objectType)
            ];
        });

        return $cacheable;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        return ObjectType::find($id);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function findByName(string $name)
    {
        return ObjectType::where('name', $name)->first();
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function insert(array $attributes = [])
    {
        array_set($attributes, 'rules', $this->defineDefaultRules($attributes));

        $objectType = ObjectType::create($attributes);

        foa_relationTypes()->resolveDefinition($objectType->fresh());

        $this->clearCache($objectType);

        return $objectType;
    }

    /**
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update(ObjectType $objectType, array $attributes = [])
    {
        array_set($attributes, 'rules', $this->defineDefaultRules($attributes));

        $objectType->update($attributes);

        foa_relationTypes()->resolveDefinition($objectType);

        $this->clearCache($objectType);

        return $objectType;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return (bool) ObjectType::destroy($id);
    }

    /**
     * @param array $attributes
     * @param array $identifiers
     */
    public function upsert(array $attributes = [], array $identifiers = ['id'])
    {
        //return ObjectType::firstOrCreate($attributes);
    }

    public function search(
        $query = '',
        $pagination = ['per_page' => 25, 'page_name' => 'page', 'page' => null]
    )
    {
        return foa_search()
            ->setBaseQuery(new ObjectType())
            ->setQuery($query, 'name')
            ->setPagination($pagination)
            ->performSearch();
    }

    /**
     * @param ObjectType $objectType
     * @return array
     */
    public function getSchema(ObjectType $objectType): array
    {
        return recursiveToArray( (array) $objectType->rules->schema );
    }

    /**
     * @param ObjectType $objectType
     * @return array
     */
    public function getSetup(ObjectType $objectType): array
    {
        return recursiveToArray( (array) $objectType->rules->setup );
    }

    /**
     * @param ObjectType $objectType
     * @return array
     */
    public function getRelationTypes(ObjectType $objectType): array
    {
        return recursiveToArray( (array) $objectType->rules->relations );
    }

    /**
     * @param ObjectType $objectType
     * @return array
     */
    public function getFunctions(ObjectType $objectType): array
    {
        return recursiveToArray( (array) $objectType->rules->functions );
    }

    public function getReadableRelationTypesArray(ObjectType $objectType): array
    {
        //we must merge it with defined relation names
        $relationNames = [];
        foreach ($objectType->hasRelationTypes as $relationType) {
            $relationNames[$relationType->name] = 'rel:' . $relationType->targetTYpe->name;
        }

        foreach ($objectType->belongsRelationTypes as $relationType) {
            $relationNames[$relationType->name] = 'rel:' . $relationType->baseType->name;
        }

        return $relationNames;
    }

    /**
     * @param ObjectType $objectType
     * @return array
     */
    public function getValidationRules(ObjectType $objectType): array
    {
        $rules = recursiveToArray( (array) $objectType->rules->validation );

        $setup = $this->getSetup($objectType);

        if (! array_has($setup, 'schema')) {
            return $rules;
        }

        $schemaSetup = array_get($setup, 'schema');

        //when using min or exact, the attributes are required
        if ($schemaSetup === 'min' || $schemaSetup === 'exact') {
            $schema = $this->getSchema($objectType);

            foreach ($rules as $key => $rule) {
                array_set($rules, $key, 'required|' . $rule);
            }

            foreach ($schema as $attribute => $cast) {
                if (! array_has($rules, $attribute)) {
                    array_set($rules, $attribute, 'required');
                }
            }
        }

        return $rules;
    }

    public function defineValidationRules(ObjectType $objectType, array $validation): void
    {
        $rules = $objectType->rules;

        $rules->validation = $validation;

        $objectType->update(['rules' => $rules]);

        $this->clearCache($objectType);
    }

    /**
     * @param ObjectType $objectType
     * @param array $schema
     */
    public function defineSchema(ObjectType $objectType, array $schema = []): void
    {
        $rules = $objectType->rules;

        $rules->schema = $schema;

        $objectType->update(['rules' => $rules]);

        $this->clearCache($objectType);
    }

    /**
     * @param ObjectType $objectType
     * @param array $setup
     */
    public function defineSetup(ObjectType $objectType, array $setup = []): void
    {
        $rules = $objectType->rules;

        $rules->setup = $setup;

        $objectType->update(['rules' => $rules]);

        $this->clearCache($objectType);
    }

    /**
     * @param ObjectType $objectType
     * @param array $functions
     */
    public function defineFunctions(ObjectType $objectType, array $functions = []): void
    {
        $rules = $objectType->rules;

        $rules->functions = $functions;

        $objectType->update(['rules' => $rules]);

        $this->clearCache($objectType);
    }

    /**
     * @param ObjectType $objectType
     * @param array $relations
     */
    public function defineRelations(ObjectType $objectType, array $relations = []): void
    {
        $rules = $objectType->rules;

        $rules->relations = $relations;

        $objectType->update(['rules' => $rules]);

        foa_relationTypes()->resolveDefinition($objectType);

        $this->clearCache($objectType);
    }

    protected function defineDefaultRules($attributes):array
    {
        $rules = array_get($attributes, 'rules', []);

        if (! array_has($rules, 'schema')) {
            array_set($rules, 'schema', []);
        }

        if (! array_has($rules, 'relations')) {
            array_set($rules, 'relations', []);
        }

        if (! array_has($rules, 'validation')) {
            array_set($rules, 'validation', []);
        }

        if (! array_has($rules, 'static')) {
            array_set($rules, 'static', false);
        }

        if (! array_has($rules, 'setup')) {
            array_set($rules, 'setup', false);
        }

        if (! array_has($rules, 'functions')) {
            array_set($rules, 'functions', []);
        }

        return $rules;
    }

    protected function clearCache(ObjectType $objectType)
    {
        $key = 'desc:' . $objectType->name;

        if (cache()->has($key)) {
            cache()->delete($key);
        }
    }
}
