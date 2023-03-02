<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InseeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_Get_Siren_With_Valid_Siren()
    {
        $administrator = $this->createAdminUser();

        $siren = '329297097';
        $response = $this->actingAs($administrator)->get(route('check-siren', $siren));
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'Siren found'
            ]);
    }

    public function test_Get_Siren_With_Invalid_Siren()
    {
        $administrator = $this->createAdminUser();

        $siren = '12345678A';
        $response = $this->actingAs($administrator)->get(route('check-siren', $siren));
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'An error has occurred...',
                'message' => 'validation.numeric'
            ]);
    }

    public function test_Get_Siren_With_Non_existent_Siren()
    {
        $administrator = $this->createAdminUser();
        $siren = '111111111';
        $response = $this->actingAs($administrator)->get(route('check-siren', $siren));
        $response->assertStatus(404)
            ->assertJson([
                'status' => 'An error has occurred...',
                'message' => 'Siren not found'
            ]);
    }

    public function test_Get_Siret_With_Valid_Siret()
    {
        $administrator = $this->createAdminUser();

        $siret = '32929709700035';
        $response = $this->actingAs($administrator)->get(route('check-siret', $siret));
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'Siret found'
            ]);
    }

    public function test_Get_Siret_With_Invalid_Siret()
    {
        $administrator = $this->createAdminUser();

        $siret = '12345678A01234';
        $response = $this->actingAs($administrator)->get(route('check-siret', $siret));
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'An error has occurred...',
                'message' => 'validation.numeric'
            ]);
    }

    public function test_Get_Siret_With_Non_existent_Siret()
    {
        $administrator = $this->createAdminUser();

        $siret = '11111111111111';
        $response = $this->actingAs($administrator)->get(route('check-siret', $siret));
        $response->assertStatus(404)
            ->assertJson([
                'status' => 'An error has occurred...',
                'message' => 'Siret not found'
            ]);
    }
}
