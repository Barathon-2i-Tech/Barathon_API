<?php

namespace Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A test to check if Admin User can log in with email and password
     */
    public function test_admin_user_can_login_with_email_and_password()
    {
        $user = $this->createAdminUser();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'azertyuiop'
        ])
            ->assertOk();

        $this->assertArrayHasKey('token', $response->json('data'));
    }


    /**
     * A test to check if Barathonien User can log in with email and password
     */
    public function test_barathonien_user_can_login_with_email_and_password()
    {
        $user = $this->createBarathonienUser();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'azertyuiop'
        ])
            ->assertOk();

        $this->assertArrayHasKey('token', $response->json('data'));
    }

    /**
     * A test to check if Owner User can log in with email and password
     */
    public function test_owner_user_can_login_with_email_and_password()
    {
        $user = $this->createOwnerUser();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'azertyuiop'
        ])
            ->assertOk();

        $this->assertArrayHasKey('token', $response->json('data'));
    }

    /**
     * A test to check if Employee User can log in with email and password
     */
    public function test_employee_user_can_login_with_email_and_password()
    {
        $user = $this->createEmployeeUser();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'azertyuiop'
        ])
            ->assertOk();

        $this->assertArrayHasKey('token', $response->json('data'));
    }

    /**
     * A test to check if an unregistered user can't log in
     * **/
    public function test_if_user_email_is_not_available_then_it_return_error()
    {
        $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => 'wrongmail@mail.com',
            'password' => 'azertyuiop'
        ])
            ->assertUnauthorized();
    }

    /**
     * A test to check if a user with a wrong password can't connect
     * **/
    public function test_if_user_with_wrong_password_return_error()
    {
        $user = $this->createAdminUser();

        $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'badPassword'
        ])
            ->assertUnauthorized();
    }
}
