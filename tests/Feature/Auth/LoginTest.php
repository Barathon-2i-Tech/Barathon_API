<?php

namespace Auth;

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

        $structure = [
            "status",
            "message",
            "data" => [
                "userLogged" => [
                    "user_id",
                    "first_name",
                    "last_name",
                    "email",
                    "email_verified_at",
                    "avatar",
                    "owner_id",
                    "barathonien_id",
                    "administrator_id",
                    "employee_id",
                    "deleted_at",
                    "updated_at",
                    "created_at"
                ],
                "token"
            ]];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'azertyuiop'
        ])
            ->assertOk();

        $response->assertJsonStructure($structure);
    }


    /**
     * A test to check if Barathonien User can log in with email and password
     */
    public function test_barathonien_user_can_login_with_email_and_password()
    {
        $user = $this->createBarathonienUser();


        $structure = [
            "status",
            "message",
            "data" => [
                "userLogged" => [
                    "user_id",
                    "first_name",
                    "last_name",
                    "email",
                    "email_verified_at",
                    "avatar",
                    "owner_id",
                    "barathonien_id",
                    "administrator_id",
                    "employee_id",
                    "deleted_at",
                    "updated_at",
                    "created_at"
                ],
                "token"
            ]];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'azertyuiop'
        ])
            ->assertOk();

        $response->assertJsonStructure($structure);
    }

    /**
     * A test to check if Owner User can log in with email and password
     */
    public function test_owner_user_can_login_with_email_and_password()
    {
        $user = $this->createOwnerUser();

        $structure = [
            "status",
            "message",
            "data" => [
                "userLogged" => [
                    "user_id",
                    "first_name",
                    "last_name",
                    "email",
                    "email_verified_at",
                    "avatar",
                    "owner_id",
                    "barathonien_id",
                    "administrator_id",
                    "employee_id",
                    "deleted_at",
                    "updated_at",
                    "created_at"
                ],
                "token"
            ]];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'azertyuiop'
        ])
            ->assertOk();

        $response->assertJsonStructure($structure);
    }

    /**
     * A test to check if Employee User can log in with email and password
     */
    public function test_employee_user_can_login_with_email_and_password()
    {
        $user = $this->createEmployeeUser();

        $structure = [
            "status",
            "message",
            "data" => [
                "userLogged" => [
                    "user_id",
                    "first_name",
                    "last_name",
                    "email",
                    "email_verified_at",
                    "avatar",
                    "owner_id",
                    "barathonien_id",
                    "administrator_id",
                    "employee_id",
                    "deleted_at",
                    "updated_at",
                    "created_at"
                ],
                "token"
            ]];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'azertyuiop'
        ])
            ->assertOk();

        $response->assertJsonStructure($structure);
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
