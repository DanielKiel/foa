<?php

use Dion\Foa\Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 21:14
 */
class ObjectAccessTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations, \Illuminate\Foundation\Testing\WithoutMiddleware;

    public function test_access()
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

        $this->assertTrue($object instanceof \Dion\Foa\Models\Object);

        $otherUser =  \App\User::create([
            'email' => 'superadminMe@email.email',
            'password' => 'password',
            'name' => 'name',
        ]);


        \Laravel\Passport\Passport::actingAs($otherUser);


        $this->expectException(\Dion\Foa\Exceptions\ObjectAccessException::class);

        $me = foa_objects()->findById($object->id);
    }
}
