<?php

namespace Tests\Unit;

use App\Http\Controllers\InseeController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class InseeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     *
     * TODO: refactor the tests to use with mock
     *
     */


//    /**
//     * A test to get a 200 response with valid siren with a mock.
//     *
//     * @return void
//     */
//    public function test_Get_Siren_With_existent_Siren(): void
//    {
//        $administrator = $this->createAdminUser();
//        // mock the InseeController class
//        $this->withoutExceptionHandling();
//        $mock = Mockery::mock(InseeController::class);
//        $mock->allows('getSiren')->andReturns('status');
//        $response = $this->actingAs($administrator)->get(route('check-siren', '329297097'));
//        $response->assertStatus(200);
//    }

    /**
     * A test to get check siren validation.
     *
     * @return void
     */
    public function test_Get_Siren_With_Invalid_Siren(): void
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


//    /**
//     * A test to get a 404 response with a siren.
//     *
//     * @return void
//     */
//    public function test_Get_Siren_With_Non_existent_Siret()
//    {
//        $administrator = $this->createAdminUser();
//
//        $siret = '11111111111111';
//        $response = $this->actingAs($administrator)->get(route('check-siret', $siret));
//        $response->assertStatus(404)
//            ->assertJson([
//                'status' => 'An error has occurred...',
//                'message' => 'Siret not found'
//            ]);
//    }

//    /**
//     * A test to get a 200 response with valid siret with a mock.
//     *
//     * @return void
//     */
//    public function test_Get_Siren_With_existent_Siret(): void
//    {
//        $administrator = $this->createAdminUser();
//        // mock the InseeController class
//        $this->withoutExceptionHandling();
//        $mock = Mockery::mock(InseeController::class);
//        $mock->allows('getSiret')->andReturns('status');
//        $response = $this->actingAs($administrator)->get(route('check-siret', '32929709700035'));
//        $response->assertStatus(200);
//    }

    /**
     * A test to get check siret validation.
     *
     * @return void
     */
    public function test_Get_Siret_With_Invalid_Siret(): void
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

//    /**
//     * A test to get a 404 response with a siret.
//     *
//     * @return void
//     */
//    public function test_Get_Siret_With_Non_existent_Siret()
//    {
//        $administrator = $this->createAdminUser();
//
//        $siret = '11111111111111';
//        $response = $this->actingAs($administrator)->get(route('check-siret', $siret));
//        $response->assertStatus(404)
//            ->assertJson([
//                'status' => 'An error has occurred...',
//                'message' => 'Siret not found'
//            ]);
//    }
}
