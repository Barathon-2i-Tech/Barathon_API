<?php

namespace Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A test to check if a user can log out
     */
    public function test_user_can_logout()
    {

        $structure = [
            "status",
            "message",
            "data"
        ];

        $user = $this->createAdminUser();

        $login = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson(route('user.login'), [
            'email' => $user->email,
            'password' => 'azertyuiop'
        ]);

        $token = $login->getData()->data->token;

        $response = $this->postJson(route('user.logout'), [
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Bearer' => $token
        ])
            ->assertOk();

        $response->assertJsonStructure($structure)
                 ->assertJsonFragment(["message" => 'You have successfully been logged out and your tokens has been removed']);

    }

    /**
     * A test to check if a user can log out
     */
    public function test_user_can_not_logout_without_token()
    {
        $this->postJson(route('user.logout'), [
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
            ->assertJsonFragment(['message' => 'Unauthenticated.']);

    }

}
