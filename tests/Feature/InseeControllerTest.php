<?php

namespace Tests\Feature;


use App\Http\Controllers\InseeController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InseeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initial Http::fake for every test, this will be overriden in specific tests
        Http::fake([
            'https://api.insee.fr/entreprises/sirene/V3/siren/*' => Http::response([], 200),
        ]);
    }

    protected function tearDown(): void
    {
        // Reset shared instance of InseeController
        $this->app->forgetInstance(InseeController::class);

        parent::tearDown();
        Http::fake();

    }

    /**
     * A test to check if the access token is created
     */
    public function test_generateToken_Returns_Valid_Access_Token(): void
    {
        $inseeApi = new InseeController();
        $accessToken = $inseeApi->generateToken();

        $this->assertNotEmpty($accessToken);
        $this->assertIsString($accessToken);

    }

    /**
     * A test to check if the host is resolvable
     */
    public function test_checkHost_Returns_True(): void
    {
        $inseeApi = new InseeController();
        $isHostResolvable = $inseeApi->checkHost();
        $this->assertTrue($isHostResolvable);
    }

    /**
     * A test to get a 200 response from the database with valid siren.
     */
    public function test_Get_Siren_With_existent_Siren_In_Database(): void
    {
        $structure = [
            "status",
            "message",
            "data" => [
                "local",
                "response" => [
                    "siren",
                    "sexeUniteLegale",
                    "denominationUniteLegale",
                    "denominationUsuelle1UniteLegale",
                    "denominationUsuelle2UniteLegale",
                    "denominationUsuelle3UniteLegale",
                    "etatAdministratifUniteLegale",
                    "nomUniteLegale",
                    "nomUsageUniteLegale",
                    "prenom1UniteLegale",
                    "prenom2UniteLegale",
                    "prenomUsuelUniteLegale",
                    "codePaysEtrangerEtablissement",
                    "codePostalEtablissement",
                    "complementAdresseEtablissement",
                    "distributionSpecialeEtablissement",
                    "indiceRepetitionEtablissement",
                    "libelleCedexEtablissement",
                    "libelleCommuneEtablissement",
                    "libelleCommuneEtrangerEtablissement",
                    "libellePaysEtrangerEtablissement",
                    "libelleVoieEtablissement",
                    "numeroVoieEtablissement",
                    "typeVoieEtablissement"
                ]
            ]
        ];

        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('check-siren-local', ['siren' => '838506756']))
            ->assertOk();

        $response->assertJsonStructure($structure);
        $response->assertJsonPath('message', 'Siren found from local database');
        $response->assertJsonPath('data.local', true);
    }

    /**
     * A test to get a 200 response with valid siren with http fake.
     */
    public function test_Get_Siren_With_existent_Siren(): void
    {
        $siren = '838506756';
        $mockedResponse = [
            "status" => "Request was successful.",
            "message" => "Siren found",
            "data" => [
                "local" => false,
                "response" => [
                    "siren" => "838506756",
                    "dateCreationUniteLegale" => "2018-04-15",
                    "sexeUniteLegale" => "M",
                    "prenom1UniteLegale" => "EDDY",
                    "trancheEffectifsUniteLegale" => null,
                    "dateDernierTraitementUniteLegale" => "2020-03-12T14:31:10",
                    "categorieEntreprise" => "PME",
                    "periodesUniteLegale" => [
                        [
                            "dateFin" => null,
                            "dateDebut" => "2018-04-15",
                            "etatAdministratifUniteLegale" => "A",
                            "nomUniteLegale" => "MANDRAN",
                            "nomUsageUniteLegale" => null,
                            "denominationUniteLegale" => null,
                            "denominationUsuelle1UniteLegale" => null,
                            "categorieJuridiqueUniteLegale" => "1000",
                            "activitePrincipaleUniteLegale" => "95.11Z",
                            "nicSiegeUniteLegale" => "00017"
                        ]
                    ]
                ]
            ]
        ];

        Http::fake([
            'https://api.insee.fr/entreprises/sirene/V3/siren/838506756?champs=identificationStandardUniteLegale' => Http::response($mockedResponse, 200),
        ]);

        $controller = new InseeController();

        $response = $controller->getSiren($siren);

        $responseData = $response->getData(true);

        $this->assertEquals('Siren found', $responseData['message']);
        $this->assertEquals($mockedResponse['data']['response'], $responseData['data']['response']);
        $this->assertEquals(false, $responseData['data']['local']);

    }


//    /**
//     * A test to get a 401 response with valid siren with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_401_error_With_existent_Siren(): void
//    {
//        $siren = '530962935';
//        $mockedResponse = [
//            "status" => "An error has occurred...",
//            "message" => "Unauthorized",
//            "data" => null
//        ];
//        Http::fake([
//            'https://api.insee.fr/entreprises/sirene/V3/siren/530962935?champs=identificationStandardUniteLegale' => Http::response($mockedResponse, 401),
//        ]);
//
//        $controller = new InseeController();
//
//        $response = $controller->getSiren($siren);
//
//        $responseData = $response->getData(true);
//
//        $this->assertEquals('Unauthorized', $responseData['message']);
//        $this->assertEquals($mockedResponse['data'], $responseData['data']);
//
//    }
//
//    /**
//     * A test to get a 403 response with valid siren with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_403_error_With_existent_Siren(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siren/530962935' => Http::response([
//                'status' => 'An error has occurred...',
//                'message' => 'Access forbidden'
//            ], 403),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siren/530962935');
//
//        $responseJson = $response->json();
//        $this->assertSame('An error has occurred...', $responseJson['status']);
//        $this->assertSame('Access forbidden', $responseJson['message']);
//        $this->assertSame(403, $response->status());
//    }
//
//    /**
//     * A test to get a 404 response with valid siren with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_404_error_With_existent_Siren(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siren/530962935' => Http::response([
//                'status' => 'An error has occurred...',
//                'message' => 'Access forbidden'
//            ], 404),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siren/530962935');
//
//        $responseJson = $response->json();
//        $this->assertSame('An error has occurred...', $responseJson['status']);
//        $this->assertSame('Access forbidden', $responseJson['message']);
//        $this->assertSame(404, $response->status());
//    }
//
//    /**
//     * A test to get a 429 response with valid siren with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_429_error_With_existent_Siren(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siren/530962935' => Http::response([
//                'status' => 'An error has occurred...',
//                'message' => 'Too many requests'
//            ], 429),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siren/530962935');
//
//        $responseJson = $response->json();
//        $this->assertSame('An error has occurred...', $responseJson['status']);
//        $this->assertSame('Too many requests', $responseJson['message']);
//        $this->assertSame(429, $response->status());
//    }
//
//    /**
//     * A test to get a 500 response with valid siren with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_500_error_With_existent_Siren(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siren/530962935' => Http::response([
//                'status' => 'An error has occurred...',
//                'message' => 'Internal server error'
//            ], 500),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siren/530962935');
//
//        $responseJson = $response->json();
//        $this->assertSame('An error has occurred...', $responseJson['status']);
//        $this->assertSame('Internal server error', $responseJson['message']);
//        $this->assertSame(500, $response->status());
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
                'message' => '{"siren":["Le numero SIREN doit etre compose de chiffres","Le numero SIREN doit etre compose de 9 chiffres"]}',
            ]);
    }

//    /**
//     * A test to get a 200 response with valid siret with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_Siret_With_existent_Siret(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siret/53096293500025' => Http::response([
//                'status' => 'Request was successful.',
//                'message' => 'Siret found'
//            ], 200),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siret/53096293500025');
//
//        $responseJson = $response->json();
//        $this->assertSame('Request was successful.', $responseJson['status']);
//        $this->assertSame('Siret found', $responseJson['message']);
//        $this->assertSame(200, $response->status());
//    }
//
//    /**
//     * A test to get a 401 response with valid siren with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_401_error_With_existent_Siret(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siret/53096293500025' => Http::response([
//                'status' => 'An error has occurred...',
//                'message' => 'Unauthorized'
//            ], 401),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siret/53096293500025');
//
//        $responseJson = $response->json();
//        $this->assertSame('An error has occurred...', $responseJson['status']);
//        $this->assertSame('Unauthorized', $responseJson['message']);
//        $this->assertSame(401, $response->status());
//    }
//
//    /**
//     * A test to get a 403 response with valid siren with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_403_error_With_existent_Siret(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siret/53096293500025' => Http::response([
//                'status' => 'An error has occurred...',
//                'message' => 'Access forbidden'
//            ], 403),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siret/53096293500025');
//
//        $responseJson = $response->json();
//        $this->assertSame('An error has occurred...', $responseJson['status']);
//        $this->assertSame('Access forbidden', $responseJson['message']);
//        $this->assertSame(403, $response->status());
//    }
//
//    /**
//     * A test to get a 404 response with valid siret with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_404_error_With_existent_Siret(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siret/53096293500025' => Http::response([
//                'status' => 'An error has occurred...',
//                'message' => 'Access forbidden'
//            ], 404),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siret/53096293500025');
//
//        $responseJson = $response->json();
//        $this->assertSame('An error has occurred...', $responseJson['status']);
//        $this->assertSame('Access forbidden', $responseJson['message']);
//        $this->assertSame(404, $response->status());
//    }
//
//    /**
//     * A test to get a 429 response with valid siret with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_429_error_With_existent_Siret(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siret/53096293500025' => Http::response([
//                'status' => 'An error has occurred...',
//                'message' => 'Too many requests'
//            ], 429),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siret/53096293500025');
//
//        $responseJson = $response->json();
//        $this->assertSame('An error has occurred...', $responseJson['status']);
//        $this->assertSame('Too many requests', $responseJson['message']);
//        $this->assertSame(429, $response->status());
//    }
//
//    /**
//     * A test to get a 500 response with valid siret with http fake.
//     *
//     * @return void
//     */
//    public function test_Get_500_error_With_existent_Siret(): void
//    {
//        Http::fake([
//            'http://localhost/api/check-siret/53096293500025' => Http::response([
//                'status' => 'An error has occurred...',
//                'message' => 'Internal server error'
//            ], 500),
//        ]);
//
//        $response = Http::get('http://localhost/api/check-siret/53096293500025');
//
//        $responseJson = $response->json();
//        $this->assertSame('An error has occurred...', $responseJson['status']);
//        $this->assertSame('Internal server error', $responseJson['message']);
//        $this->assertSame(500, $response->status());
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
}
