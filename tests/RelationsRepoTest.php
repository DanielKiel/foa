<?php

use Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: dk
 * Date: 17.05.17
 * Time: 09:19
 */
class RelationsRepoTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations, \Illuminate\Foundation\Testing\WithoutMiddleware;

    public function test_add_relation()
    {
        //define the schema
        $address = foa_objectTypes()->insert([
            'name' => 'Address',

        ]);

        foa_objectTypes()->defineSchema($address,[
            'street' => 'text'
        ]);

        $contact = foa_objectTypes()->insert([
            'name' => 'Contact',

        ]);

        foa_objectTypes()->defineSetup($contact, [
            'schema' => 'exact'
        ]);

        foa_objectTypes()->defineSchema($contact,[
            'email' => 'text'
        ]);

        foa_objectTypes()->defineRelations($contact, [
            [
                'name' => 'addresses',
                'inverse_name' => 'contact',
                'target_type' => 'Address',
                'variant' => 'hasMany'
            ]
        ]);

        //now add a realtion
        $obj = foa_objects()->insert([
            'objectType' => 'Contact',
            'email' => 'me@me.de',
            'addresses' => [
                'insert' => [
                    [
                        'street' => 'first'
                    ],
                    [
                        'street' => 'second'
                    ]
                ]
            ]
        ]);

        $this->assertEquals(2, \Dion\Foa\Models\Relation::count());

        $addresses = foa_objects()
            ->setObjectType(foa_objectTypes()
            ->findByName('Address'))
            ->searchByObjectType()
            ->data;

        $this->assertEquals(2, count($addresses));

        foa_objects()->update($obj, [
            'addresses' => [
                'update' => $addresses
            ]
        ]);

        $this->assertEquals(2, \Dion\Foa\Models\Relation::count());

        $addresses = foa_objects()
            ->setObjectType(foa_objectTypes()
                ->findByName('Address'))
            ->searchByObjectType()
            ->data;

        $this->assertEquals(2, count($addresses));
    }
}
