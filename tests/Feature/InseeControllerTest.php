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

        $response = $this->actingAs($administrator)->get(route('check-siren-local', ['siren' => '005420120']))
            ->assertOk();

        $response->assertJsonStructure($structure);
        $response->assertJsonPath('message', 'Siren found from local database');
        $response->assertJsonPath('data.local', true);
    }

    /**
     * A test to get a 200 response from the database with valid siren.
     */
    public function test_Get_404_error_With_existent_Siren_In_Database(): void
    {
        $structure = [
            "status",
            "message",
            "data"
        ];

        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('check-siren-local', ['siren' => '000000000']))
            ->assertNotFound();

        $response->assertJsonStructure($structure);
        $response->assertJsonPath('message', 'Siren not found in local database');
    }


    /**
     * A test to get check siren validation.
     *
     * @return void
     */
    public function test_Get_Siret_With_Invalid_Siret(): void
    {
        $administrator = $this->createAdminUser();

        $siret = '1234567891023A';
        $response = $this->actingAs($administrator)->get(route('check-siret', $siret));
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'An error has occurred...',
                'message' => 'Le numero SIRET doit etre compose de chiffres',
            ]);
    }

    /**
     * A test to get a 200 response from the database with valid siren.
     */
    public function test_Get_Siret_With_existent_Siret_In_Database(): void
    {
        $structure = [
            "status",
            "message",
            "data" => [
                "local",
                "response" => [
                    "siren",
                    "siret",
                    "etablissementSiege",
                    "etatAdministratifEtablissement",
                    "denominationUsuelleEtablissement",
                    "enseigne1Etablissement",
                    "enseigne2Etablissement",
                    "enseigne3Etablissement",
                    "codePaysEtrangerEtablissement",
                    "codePostalEtablissement",
                    "complementAdresseEtablissement",
                    "distributionSpecialeEtablissement",
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
                    "typeVoieEtablissement",
                    "denominationUniteLegale"
                ]
            ]
        ];

        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('check-siret-local', ['siret' => '53096293500025']))
            ->assertOk();

        $response->assertJsonStructure($structure);
        $response->assertJsonPath('message', 'Siret found from local database');
        $response->assertJsonPath('data.local', true);
    }

    /**
     * A test to get a 200 response from the database with valid siren.
     */
    public function test_Get_404_error_With_existent_Siret_In_Database(): void
    {
        $structure = [
            "status",
            "message",
            "data"
        ];

        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('check-siret-local', ['siret' => '00000000000000']))
            ->assertNotFound();

        $response->assertJsonStructure($structure);
        $response->assertJsonPath('message', 'Siret not found in local database');
    }

}
