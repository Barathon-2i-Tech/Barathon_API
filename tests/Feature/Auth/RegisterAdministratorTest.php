<?php

namespace Auth;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class RegisterAdministratorTest extends TestCase
{
    use RefreshDatabase, withFaker;

    /**
     * A test to check if an administrator can register
     */
    public function test_administrator_can_register()
    {
        $structure = [
            "status",
            "message",
            "data" => [
                "userLogged" => [
                    "first_name",
                    "last_name",
                    "email",
                    "avatar",
                    "updated_at",
                    "created_at",
                    "administrator_id",
                ],
                "token"
            ]];
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->postJson(route('user.register.admin'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'superAdmin' => false], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertOk();

        $response->assertJsonStructure($structure);
    }
}
