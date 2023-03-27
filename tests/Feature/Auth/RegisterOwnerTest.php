<?php

namespace Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Tests\TestCase;


class RegisterOwnerTest extends TestCase
{
    use RefreshDatabase, withFaker;


    /**
     * A test to check if a owner can register
     */
    public function test_owner_can_register()
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
                    "owner_id"
                ],
                "token"
            ]];


        $file = File::create('kbis.pdf');

        $response = $this->postJson(route('user.register.owner'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'siren' => '010203040',
            'kbis' => $file,
            'active' => false,
            'avatar' => "myavatar.jpg"
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertOk();

        $response->assertJsonStructure($structure);
    }

    /**
     * A test to check if a owner can register without siren
     */
    public function test_owner_can_not_register_without_siren()
    {
        $file = File::create('kbis.pdf');

        $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.register.owner'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'kbis' => $file,
            'active' => false,
            'avatar' => "myavatar.jpg"
        ])
            ->assertStatus(422)
            ->assertInvalid(['siren' => 'Le numéro SIREN est obligatoire.']);
    }


    /**
     * A test to check if a barathonien can not register without kbis
     */
    public function test_owner_can_not_register_without_kbis()
    {

        $this->postJson(route('user.register.owner'), [
            'first_name' => 'Pierre',
            'last_name' => 'Dupont',
            'email' => 'toto@gmail.com',
            'password' => 'azertyuiop',
            'password_confirmation' => 'azertyuiop',
            'siren' => fake()->siren(),
            'active' => false,
            'avatar' => "myavatar.jpg"
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertStatus(422)
            ->assertInvalid(['kbis' => 'Le KBIS est obligatoire.']);
    }

}
