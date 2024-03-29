<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Indicates whether the default seeder should run before each test.
     * @var bool
     */

    protected $seed = true;

    public function createAdminUser()
    {
        $user = User::whereNotNull('administrator_id')->first();
        return $user;
    }

    public function createBarathonienUser()
    {
        $user = User::whereNotNull('barathonien_id')->first();
        return $user;
    }

    public function createOwnerUser()
    {
        $user = User::whereNotNull('owner_id')->first();
        return $user;
    }

    public function createEmployeeUser()
    {
        $user = User::whereNotNull('employee_id')->first();
        return $user;
    }

    public function createNewAdminUser()
    {
        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admintest@mail.fr',
            'password' => Hash::make('password'),
            'superAdmin' => true,]);
        return $user;
    }
}
