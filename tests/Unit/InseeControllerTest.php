<?php

namespace Tests\Unit;

use App\Http\Controllers\InseeController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InseeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

   /**
    * A test to check if the access token is created
    * FIXME: API DOWN
    */

//    public function test_generateToken_Returns_Valid_Access_Token(): void
//    {
//        $inseeApi = new InseeController();
//        $accessToken = $inseeApi->generateToken();
//
//        $this->assertNotEmpty($accessToken);
//        $this->assertIsString($accessToken);
//
//    }

    /**
     * A test to get a 200 response with valid siren with http fake.
     *
     * @return void
     */
    public function test_Get_Siren_With_existent_Siren(): void
    {
        Http::fake([
            'http://localhost/api/check-siren/530962935' => Http::response([
                'status' => 'Request was successful.',
                'message' => 'Siren found'
            ], 200),
        ]);

        $response = Http::get('http://localhost/api/check-siren/530962935');

        $responseJson = $response->json();
        $this->assertSame('Request was successful.', $responseJson['status']);
        $this->assertSame('Siren found', $responseJson['message']);
        $this->assertSame(200, $response->status());
    }


    /**
     * A test to get a 401 response with valid siren with http fake.
     *
     * @return void
     */
    public function test_Get_401_error_With_existent_Siren(): void
    {
        Http::fake([
            'http://localhost/api/check-siren/530962935' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Unauthorized'
            ], 401),
        ]);

        $response = Http::get('http://localhost/api/check-siren/530962935');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Unauthorized', $responseJson['message']);
        $this->assertSame(401, $response->status());
    }

    /**
     * A test to get a 403 response with valid siren with http fake.
     *
     * @return void
     */
    public function test_Get_403_error_With_existent_Siren(): void
    {
        Http::fake([
            'http://localhost/api/check-siren/530962935' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Access forbidden'
            ], 403),
        ]);

        $response = Http::get('http://localhost/api/check-siren/530962935');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Access forbidden', $responseJson['message']);
        $this->assertSame(403, $response->status());
    }

    /**
     * A test to get a 404 response with valid siren with http fake.
     *
     * @return void
     */
    public function test_Get_404_error_With_existent_Siren(): void
    {
        Http::fake([
            'http://localhost/api/check-siren/530962935' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Access forbidden'
            ], 404),
        ]);

        $response = Http::get('http://localhost/api/check-siren/530962935');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Access forbidden', $responseJson['message']);
        $this->assertSame(404, $response->status());
    }

    /**
     * A test to get a 429 response with valid siren with http fake.
     *
     * @return void
     */
    public function test_Get_429_error_With_existent_Siren(): void
    {
        Http::fake([
            'http://localhost/api/check-siren/530962935' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Too many requests'
            ], 429),
        ]);

        $response = Http::get('http://localhost/api/check-siren/530962935');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Too many requests', $responseJson['message']);
        $this->assertSame(429, $response->status());
    }

    /**
     * A test to get a 500 response with valid siren with http fake.
     *
     * @return void
     */
    public function test_Get_500_error_With_existent_Siren(): void
    {
        Http::fake([
            'http://localhost/api/check-siren/530962935' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Internal server error'
            ], 500),
        ]);

        $response = Http::get('http://localhost/api/check-siren/530962935');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Internal server error', $responseJson['message']);
        $this->assertSame(500, $response->status());
    }

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
                'message' => '{"siren":["Le numero SIREN doit etre compose de chiffres","Le numero SIREN doit etre compose de 9 chiffres"]}',
            ]);
    }

    /**
     * A test to get a 200 response with valid siret with http fake.
     *
     * @return void
     */
    public function test_Get_Siret_With_existent_Siret(): void
    {
        Http::fake([
            'http://localhost/api/check-siret/53096293500025' => Http::response([
                'status' => 'Request was successful.',
                'message' => 'Siret found'
            ], 200),
        ]);

        $response = Http::get('http://localhost/api/check-siret/53096293500025');

        $responseJson = $response->json();
        $this->assertSame('Request was successful.', $responseJson['status']);
        $this->assertSame('Siret found', $responseJson['message']);
        $this->assertSame(200, $response->status());
    }

    /**
     * A test to get a 401 response with valid siren with http fake.
     *
     * @return void
     */
    public function test_Get_401_error_With_existent_Siret(): void
    {
        Http::fake([
            'http://localhost/api/check-siret/53096293500025' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Unauthorized'
            ], 401),
        ]);

        $response = Http::get('http://localhost/api/check-siret/53096293500025');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Unauthorized', $responseJson['message']);
        $this->assertSame(401, $response->status());
    }

    /**
     * A test to get a 403 response with valid siren with http fake.
     *
     * @return void
     */
    public function test_Get_403_error_With_existent_Siret(): void
    {
        Http::fake([
            'http://localhost/api/check-siret/53096293500025' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Access forbidden'
            ], 403),
        ]);

        $response = Http::get('http://localhost/api/check-siret/53096293500025');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Access forbidden', $responseJson['message']);
        $this->assertSame(403, $response->status());
    }

    /**
     * A test to get a 404 response with valid siret with http fake.
     *
     * @return void
     */
    public function test_Get_404_error_With_existent_Siret(): void
    {
        Http::fake([
            'http://localhost/api/check-siret/53096293500025' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Access forbidden'
            ], 404),
        ]);

        $response = Http::get('http://localhost/api/check-siret/53096293500025');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Access forbidden', $responseJson['message']);
        $this->assertSame(404, $response->status());
    }

    /**
     * A test to get a 429 response with valid siret with http fake.
     *
     * @return void
     */
    public function test_Get_429_error_With_existent_Siret(): void
    {
        Http::fake([
            'http://localhost/api/check-siret/53096293500025' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Too many requests'
            ], 429),
        ]);

        $response = Http::get('http://localhost/api/check-siret/53096293500025');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Too many requests', $responseJson['message']);
        $this->assertSame(429, $response->status());
    }

    /**
     * A test to get a 500 response with valid siret with http fake.
     *
     * @return void
     */
    public function test_Get_500_error_With_existent_Siret(): void
    {
        Http::fake([
            'http://localhost/api/check-siret/53096293500025' => Http::response([
                'status' => 'An error has occurred...',
                'message' => 'Internal server error'
            ], 500),
        ]);

        $response = Http::get('http://localhost/api/check-siret/53096293500025');

        $responseJson = $response->json();
        $this->assertSame('An error has occurred...', $responseJson['status']);
        $this->assertSame('Internal server error', $responseJson['message']);
        $this->assertSame(500, $response->status());
    }

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
}
