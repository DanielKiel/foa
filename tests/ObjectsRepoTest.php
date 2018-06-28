<?php

use Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: dk
 * Date: 17.05.17
 * Time: 09:19
 */
class ObjectsRepoTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations, \Illuminate\Foundation\Testing\WithoutMiddleware;

    public function testBasics()
    {
        $objectType = foa_objectTypes()->insert(['name' => 'Objects']);

        $object = foa_objects()->insert([
            'objectType' => 'Objects',
            'foo' => 'bar',
            'key' => 'value'
        ]);

        $this->assertEquals('Objects', $object->data->objectType);
        $this->assertEquals('bar', $object->data->foo);
        $this->assertEquals('value', $object->data->key);

        $object = foa_objects()->update($object, [
            'foo' => 'bar',
            'key' => 'value_1',
            'edit' => 'done'
        ]);

        $this->assertEquals('Objects', $object->data->objectType);
        $this->assertEquals('bar', $object->data->foo);
        $this->assertEquals('value_1', $object->data->key);
        $this->assertEquals('done', $object->data->edit);
    }

    public function testSearch()
    {
        foa_objectTypes()->insert(['name' => 'Objects_1']);

        foa_objectTypes()->insert(['name' => 'Objects_2']);

        foa_objectTypes()->insert(['name' => 'Objects_3']);

        foa_objects()->insert([
            'objectType' => 'Objects_1',
            'email' => 'danielkoch€foa.de'
        ]);

        foa_objects()->insert([
            'objectType' => 'Objects_1',
            'email' => 'dk@manager.de'
        ]);

        foa_objects()->insert([
            'objectType' => 'Objects_2',
            'email' => 'test@mail.de'
        ]);

        foa_objects()->insert([
            'objectType' => 'Objects_2',
            'email' => 'test_2@mail.de'
        ]);

        foa_objects()->insert([
            'objectType' => 'Objects_3',
            'email' => 'test@mail.de'
        ]);

        $this->assertEquals(2, foa_objects()->search('', ['objectType = Objects_1'])->total());

        $this->assertEquals(1, foa_objects()->search('danielkoch', ['objectType = Objects_1'])->total());

        $this->assertEquals(1, foa_objects()->search('test@mail.de', ['objectType = Objects_3'])->total());
        $this->assertEquals(1, foa_objects()->search('', ['objectType = Objects_3', 'email = test@mail.de'])->total());

        $this->assertEquals(0, foa_objects()->search('test@mail.de', ['objectType = Objects_1'])->total());
        $this->assertEquals(2, foa_objects()->search('', ['objectType = Objects_1', 'email NOT LIKE test.de'])->total());
        $this->assertEquals(2, foa_objects()->search('', ['objectType = Objects_1', 'email LIKE .de'])->total());
    }
}
