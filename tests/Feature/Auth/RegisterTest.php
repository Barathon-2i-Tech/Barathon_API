<?php

namespace Auth;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class RegisterTest extends TestCase
{
    use RefreshDatabase, withFaker;


    /**
     *************** Register without profile Test **********************************************
     */

    /**
     * A test to check if a User without profile can register
     */
    public function test_user_can_register_without_profile()
    {
        $structure = [
            "status",
            "message",
            "data" => [
                "user" => [
                    "first_name",
                    "last_name",
                    "email",
                    "updated_at",
                    "created_at",
                    "user_id",
                ],
                "token"
            ]];

        $response = $this->postJson(route('user.register'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop'], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertOk();

        $response->assertJsonStructure($structure);
    }

    /**
     * A test to check if a User with the same email can't register
     */
    public function test_user_can_not_register_with_same_email()
    {
        $this->test_user_can_register_without_profile();

        $this->postJson(route('user.register'), [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop'
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['email' => 'validation.unique']);


    }

    /**
     * A test to check if a User without first_name can't register
     */
    public function test_user_can_not_register_without_first_name()
    {
        $this->postJson(route('user.register'), [
            'last_name' => fake()->lastName,
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop'
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['first_name' => 'validation.required']);


    }

    /**
     * A test to check if a User without last_name can't register
     */
    public function test_user_can_not_register_without_last_name()
    {
        $this->postJson(route('user.register'), [
            'first_name' => fake()->firstName,
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop'
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['last_name' => 'validation.required']);


    }

    /**
     * A test to check if a User without password can't register
     */
    public function test_user_can_not_register_without_password()
    {
        $this->postJson(route('user.register'), [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'email' => 'toto@gmail.com',
            'password_confirmation' => 'azertyuiop'
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['password' => 'validation.required']);


    }

    /**
     * A test to check if a User without password confirmation can't register
     */
    public function test_user_can_not_register_without_password_confirmation()
    {
        $this->postJson(route('user.register'), [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop'
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['password' => 'validation.confirmed']);


    }

    /**
     * A test to check if a User with a password less than 8 characters can't register
     */
    public function test_user_can_not_register_with_password_less_8_characters()
    {

        $this->postJson(route('user.register'), [
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'email' => 'toto@gmail.com',
            'password' => 'Pass',
            'password_confirmation' => 'Pass'
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['password' => 'validation.min.string']);

    }

}
