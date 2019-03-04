<?php
  
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function signIn($user = null)
    {
        $user = $user ?: factory('App\User')->create();

        $this->actingAs($user);

        return $user;
    }
}
