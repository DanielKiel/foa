<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 28.06.18
 * Time: 15:33
 */

namespace Dion\Foa\Tests;


use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $user = User::create([
            'email' => 'superadmin@email.email',
            'password' => 'password',
            'name' => 'name',
        ]);

        $this->actingAs($user);
        Passport::actingAs($user);
    }
}