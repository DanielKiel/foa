<?php

use Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 21:14
 */
class AttributeCastsTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations, \Illuminate\Foundation\Testing\WithoutMiddleware;

    public function test_cast_password()
    {
        $objectType = foa_objectTypes()->insert([
            'name' => 'Casting',

        ]);

        foa_objectTypes()->defineSchema($objectType,[
            'pass' => 'password',
            'myT' => 'text',
            'myI' => 'int'
        ]);

        $object = foa_objects()->insert([
            'objectType' => 'Casting',
            'pass' => 'myPassword',
            'myT' => 'myT',
            'myI' => '1'
        ]);

        $this->assertNotEquals('myPassword', $object->data->pass);
        $this->assertTrue(is_int($object->data->myI));

        $this->assertNotEquals('myPassword', array_get($object->toArray(), 'pass'));
        $this->assertEquals('myT', array_get($object->toArray(), 'myT'));
        $this->assertTrue(is_int(array_get($object->toArray(), 'myI')));
    }
}
