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

        return ObjectType::create($attributes);
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
    public function getValidationRules(ObjectType $objectType): array
    {
        //need to transform schema relevant data to validation rules

        return [];
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

        if (! array_has($rules, 'static')) {
            array_set($rules, 'static', false);
        }

        return $rules;
    }
}