<?php

use Tests\TestCase;

/**
 * Created by PhpStorm.
 * User: dk
 * Date: 16.05.17
 * Time: 21:14
 */
class AttributeValidationTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations, \Illuminate\Foundation\Testing\WithoutMiddleware;

    public function test_validation()
    {
        $objectType = foa_objectTypes()->insert([
            'name' => 'Validation',

        ]);

        foa_objectTypes()->defineValidationRules($objectType,[
            'pass' => 'min:16',
        ]);

        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPassword',
        ]);

        $this->assertFalse($object instanceof \Dion\Foa\Models\BaseObject);


        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPasswordHasNowMoreThen16Characters',
        ]);

        $this->assertTrue($object instanceof \Dion\Foa\Models\BaseObject);


        $object2 = foa_objects()->update($object,[
            'objectType' => 'Validation',
            'pass' => 'myPassword',
        ]);

        $this->assertFalse($object2 instanceof \Dion\Foa\Models\BaseObject);
    }

    public function test_required_by_schema_setup_min()
    {
        $objectType = foa_objectTypes()->insert([
            'name' => 'Validation',

        ]);

        foa_objectTypes()->defineValidationRules($objectType,[
            'pass' => 'min:16',
        ]);

        foa_objectTypes()->defineSchema($objectType,[
            'pass' => 'password',
            'neededText' => 'text'
        ]);

        foa_objectTypes()->defineSetup($objectType, [
            'schema' => 'min'
        ]);

        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPassword',
        ]);

        $this->assertFalse($object instanceof \Dion\Foa\Models\BaseObject);


        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPasswordHasNowMoreThen16Characters',
            'neededText' => 'i am here'
        ]);

        $this->assertTrue($object instanceof \Dion\Foa\Models\BaseObject);


        $object2 = foa_objects()->update($object,[
            'objectType' => 'Validation',
            'pass' => 'myPasswordHasNowMoreThen16Characters',
        ]);

        //this is an update - so saved values are megred with new values and so each update is a partial update
        $this->assertTrue($object2 instanceof \Dion\Foa\Models\BaseObject);

        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPasswordHasNowMoreThen16Characters',
            'neededText' => 'i am here',
            'allowed' => 'i am allowed when using exact'
        ]);

        $this->assertTrue($object instanceof \Dion\Foa\Models\BaseObject);
    }

    public function test_required_by_schema_setup_exact()
    {
        $objectType = foa_objectTypes()->insert([
            'name' => 'Validation',

        ]);

        foa_objectTypes()->defineValidationRules($objectType,[
            'pass' => 'min:16',
        ]);

        foa_objectTypes()->defineSchema($objectType,[
            'pass' => 'password',
            'neededText' => 'text'
        ]);

        foa_objectTypes()->defineSetup($objectType, [
            'schema' => 'exact'
        ]);

        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPassword',
        ]);

        $this->assertFalse($object instanceof \Dion\Foa\Models\BaseObject);


        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPasswordHasNowMoreThen16Characters',
            'neededText' => 'i am here'
        ]);

        $this->assertTrue($object instanceof \Dion\Foa\Models\BaseObject);


        $object2 = foa_objects()->update($object,[
            'objectType' => 'Validation',
            'pass' => 'myPasswordHasNowMoreThen16Characters',
        ]);

        //this is an update - so saved values are megred with new values and so each update is a partial update
        $this->assertTrue($object2 instanceof \Dion\Foa\Models\BaseObject);


        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPassword',
            'neededText' => 'i am here',
            'notAllowed' => 'i am not allowed when using exact'
        ]);

        $this->assertFalse($object instanceof \Dion\Foa\Models\BaseObject);
    }

    public function test_required_by_schema_setup_sometimes()
    {
        $objectType = foa_objectTypes()->insert([
            'name' => 'Validation',

        ]);

        foa_objectTypes()->defineValidationRules($objectType,[
            'pass' => 'min:16',
        ]);

        foa_objectTypes()->defineSchema($objectType,[
            'pass' => 'password',
            'neededText' => 'text'
        ]);

        foa_objectTypes()->defineSetup($objectType, [
            'schema' => 'sometimes'
        ]);

        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPassword',
        ]);

        $this->assertFalse($object instanceof \Dion\Foa\Models\BaseObject);

        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'neededText' => 'no we are here',
        ]);

        $this->assertTrue($object instanceof \Dion\Foa\Models\BaseObject);


        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPasswordHasNowMoreThen16Characters',
            'neededText' => 'i am here'
        ]);

        $this->assertTrue($object instanceof \Dion\Foa\Models\BaseObject);


        $object2 = foa_objects()->update($object,[
            'objectType' => 'Validation',
            'pass' => 'myPasswordHasNowMoreThen16Characters',
        ]);

        //this is an update - so saved values are megred with new values and so each update is a partial update
        $this->assertTrue($object2 instanceof \Dion\Foa\Models\BaseObject);

        $object = foa_objects()->insert([
            'objectType' => 'Validation',
            'pass' => 'myPasswordHasNowMoreThen16Characters',
            'neededText' => 'i am here',
            'allowed' => 'i am allowed when using exact'
        ]);

        $this->assertTrue($object instanceof \Dion\Foa\Models\BaseObject);
    }
}
