<?php

use Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: dk
 * Date: 17.05.17
 * Time: 09:19
 */
class RelationTypesRepoTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations, \Illuminate\Foundation\Testing\WithoutMiddleware;

    public function test_basic()
    {
        foa_objectTypes()->insert([
            'name' => 'Image',

        ]);

        $objectType = foa_objectTypes()->insert([
            'name' => 'Contact',

        ]);

        foa_objectTypes()->defineSchema($objectType,[
            'pass' => 'password',
            'myT' => 'text',
            'myI' => 'int'
        ]);

        foa_objectTypes()->defineRelations($objectType, [
            [
                'name' => 'avatar',
                'inverse_name' => 'contact',
                'target_type' => 'Image',
                'variant' => 'hasOne'
            ]
        ]);

        $this->assertEquals(1, \Dion\Foa\Models\RelationType::count());

        $type = \Dion\Foa\Models\RelationType::first();

        $this->assertEquals('hasOne', $type->variant);
        $this->assertEquals('avatar', $type->name);
        $this->assertEquals('contact', $type->inverse_name);

        $this->assertEquals('Contact', $type->baseType->name);
        $this->assertEquals('Image', $type->targetType->name);


        foa_objectTypes()->defineRelations($objectType, [
            [
                'name' => 'avatars',
                'inverse_name' => 'contact',
                'target_type' => 'Image',
                'variant' => 'hasMany'
            ]
        ]);

        $this->assertEquals(1, \Dion\Foa\Models\RelationType::count());

        $type = \Dion\Foa\Models\RelationType::first();

        $this->assertEquals('hasMany', $type->variant);
        $this->assertEquals('avatars', $type->name);
        $this->assertEquals('contact', $type->inverse_name);
    }
}
