<?php

namespace Auth;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class RegisterBarathonienTest extends TestCase
{
    use RefreshDatabase, withFaker;

    /**
     * A test to check if a barathonien can register
     */
    public function test_barathonien_can_register()
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
                    "user_id",
                    "barathonien_id"
                ],
                "token"
            ]];

        $response = $this->postJson(route('user.register.barathonien'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'birthday' => '1989-04-05',
            'address' => fake()->address,
            'postal_code' => '69000',
            'city' => fake()->city,
            'avatar' => "myavatar.jpg"
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertOk();

        $response->assertJsonStructure($structure);
    }

    /**
     * A test to check if a barathonien can register without birthday
     */
    public function test_barathonien_can_not_register_without_birthday()
    {
        $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.register.barathonien'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'address' => fake()->address,
            'postal_code' => '69000',
            'city' => fake()->city,
            'avatar' => "myavatar.jpg"
        ])
            ->assertStatus(422)
            ->assertInvalid(['birthday' => 'validation.required']);
    }

    /**
     * A test to check if a barathonien can register without birthday in a bad format
     */
    public function test_barathonien_can_not_register_without_birthday_bad_format()
    {
        $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.register.barathonien'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'birthday' => 'wrong date',
            'address' => fake()->address,
            'postal_code' => '69000',
            'city' => fake()->city,
            'avatar' => "myavatar.jpg"
        ])
            ->assertStatus(422)
            ->assertInvalid(['birthday' => 'validation.date']);
    }

    /**
     * A test to check if a barathonien can not register if he is a minor
     */
    public function test_barathonien_can_not_register_if_minor()
    {
        $today = new Carbon();
        $minor = $today->subYears(15);

        $this->postJson(route('user.register.barathonien'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'birthday' => $minor,
            'address' => fake()->address,
            'postal_code' => '69000',
            'city' => fake()->city,
            'avatar' => "myavatar.jpg"
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['birthday' => 'validation.before']);
    }

    /**
     * A test to check if a barathonien can not register without address
     */
    public function test_barathonien_can_not_register_without_address()
    {

        $this->postJson(route('user.register.barathonien'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'birthday' => '1989-04-05',
            'postal_code' => '69000',
            'city' => fake()->city,
            'avatar' => "myavatar.jpg"
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['address' => 'validation.required']);
    }

    /**
     * A test to check if a barathonien can not register with an address less than 5 characters
     */
    public function test_barathonien_can_not_register_with_address_less_5_characters()
    {
        $this->postJson(route('user.register.barathonien'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'birthday' => '1989-04-05',
            'address' => 'no',
            'postal_code' => '69000',
            'city' => fake()->city,
            'avatar' => "myavatar.jpg"
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['address' => 'validation.min.string']);
    }

    /**
     * A test to check if a barathonien can not register without postal code
     */
    public function test_barathonien_can_not_register_without_postal_code()
    {
        $this->postJson(route('user.register.barathonien'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'birthday' => '1989-04-05',
            'city' => fake()->city,
            'avatar' => "myavatar.jpg"
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['postal_code' => 'validation.required']);
    }

    /**
     * A test to check if a barathonien can not register with a postal code not equal to 5 characters
     */
    public function test_barathonien_can_not_register_with_postal_code_not_equal_5_characters()
    {
        $this->postJson(route('user.register.barathonien'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'birthday' => '1989-04-05',
            'address' => fake()->address,
            'postal_code' => '6900',
            'city' => fake()->city,
            'avatar' => "myavatar.jpg"
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['postal_code' => 'validation.size.string']);
    }

    /**
     * A test to check if a barathonien can not register without city
     */
    public function test_barathonien_can_not_register_without_city()
    {

        $this->postJson(route('user.register.barathonien'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'birthday' => '1989-04-05',
            'postal_code' => '69000',
            'avatar' => "myavatar.jpg"
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['city' => 'validation.required']);
    }

}
