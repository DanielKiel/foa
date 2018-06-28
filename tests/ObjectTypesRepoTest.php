<?php

use Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 21:14
 */
class ObjectTypesRepoTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations, \Illuminate\Foundation\Testing\WithoutMiddleware;

    public function testBasicInserts()
    {
        $repo = foa_objectTypes();

        $objectType = $repo->insert(['name' => 'Objects']);

        $this->assertInstanceOf(\Dion\Foa\Models\ObjectType::class, $objectType);

        $this->assertEmpty($repo->getSchema($objectType));

        $schema = [
            'foo' => 'text',
        ];

        $repo->defineSchema($objectType, $schema);

        $objectType = $objectType->fresh();

        $this->assertEquals('text', $objectType->rules->schema->foo);

    }

    public function testSearch()
    {
        $repo = foa_objectTypes();

        $repo->insert(['name' => 'Objects_1']);

        $repo->insert(['name' => 'Objects_2']);

        $repo->insert(['name' => 'Objects_3']);

        //without pagination
        $searchResult = $repo->search('Objects_1',  false);

        $this->assertEquals(1, $searchResult->count());

        $searchResult = $repo->search('Objects_',  false);

        $this->assertEquals(3, $searchResult->count());

        //use pagination
        $searchResult = $repo->search('Objects_1');

        $this->assertEquals(1, $searchResult->total());

        $searchResult = $repo->search('Objects_');

        $this->assertEquals(3, $searchResult->total());
    }
}
