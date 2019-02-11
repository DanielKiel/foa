<?php

use Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 21:14
 */
class AttributeFunctionsTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations, \Illuminate\Foundation\Testing\WithoutMiddleware;

    public function test_cast_password()
    {
        $objectType = foa_objectTypes()->insert([
            'name' => 'FunctionalTest',

        ]);

        foa_objectTypes()->defineSchema($objectType,[
            'foo' => 'int',
            'bar' => 'int'
        ]);

        foa_objectTypes()->defineFunctions($objectType,[
            '__sum' => 'obj["foo"] + obj["bar"]',
            '__divide' => 'obj["foo"] / obj["bar"]'
        ]);

        $object = foa_objects()->insert([
            'objectType' => 'FunctionalTest',
            'foo' => '12',
            'bar' => '24'
        ]);

        $this->assertEquals(36, $object->data->__sum);
        $this->assertEquals(0.5, $object->data->__divide);

        $objArray = $object->toArray();

        $this->assertTrue(array_has($objArray, '__sum'));

        $this->assertEquals(36, array_get($objArray, '__sum'));
        $this->assertEquals(0.5, array_get($objArray, '__divide'));
    }
}
