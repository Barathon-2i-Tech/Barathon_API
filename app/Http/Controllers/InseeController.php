<?php

namespace App\Http\Controllers;

use App\Models\Siren;
use App\Models\Siret;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InseeController extends Controller
{
    use HttpResponses;

    private const BASE_URL = 'https://api.insee.fr/entreprises/sirene/V3/';
    private string $apiKey;

    public function __construct()
    {
        // initialise the API key for the INSEE API SIRENE
        $this->apiKey = base64_encode(env('INSEE_CONSUMER_KEY') . ':' . env('INSEE_CONSUMER_SECRET'));
    }

    /**
     * Check if the host (Insee API) is resolvable.
     *
     * @return bool
     */
    public function checkHost(): bool
    {
      // $host = 'api.insee.fr';
        $host = 'google'; //only for testing purpose
        $isHostResolvable = false;
        $ipAddress = gethostbyname($host);

        if ($ipAddress !== $host) {
            $isHostResolvable = true;
        }

        return $isHostResolvable;
    }

    /**
     * Generate a valid access token for the SIRENE API of INSEE.
     * (subscription required to use this API)
     */
    public function generateToken(): string
    {
        $client = new Client();

        $result = $client->post('https://api.insee.fr/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . $this->apiKey,
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $result = json_decode($result->getBody());
        return $result->access_token;
    }

    public function checkStatusCodeFromApi($response): JsonResponse
    {
        return match ($response->getStatusCode()) {
            401 => $this->error(null, 'Unauthorized', 401),
            403 => $this->error(null, 'Access forbidden', 403),
            404 => $this->error(null, 'Not found', 404),
            429 => $this->error(null, 'Too many requests', 429),
            default => $this->error(null, 'Internal server error', $response->getStatusCode()),
        };
    }

    /**
     * Retrieve information about a company using SIREN number.
     */
    public function getSiren(string $siren): JsonResponse
    {
        // removing blank space
        $sirenToCheck = str_replace(' ', '', $siren);

        //validating Siren
        $validator = Validator::make(['siren' => $sirenToCheck], [
            'siren' => 'required|numeric|digits:9',
        ], [
            'siren.required' => 'Le numero SIREN est obligatoire',
            'siren.numeric' => 'Le numero SIREN doit etre compose de chiffres',
            'siren.digits' => 'Le numero SIREN doit etre compose de 9 chiffres',
        ]);

        // returning a error if validation fail
        if ($validator->fails()) {
            return $this->error(null, $validator->errors(), 400);
        }

        // check if host is available
        $hostAvailable = $this->checkHost();

        if ($hostAvailable) {

            //generate token
            $tokenGenerated = $this->generateToken();

            // sending http get request with the token generated
            $client = new Client();
            $response = $client->get(self::BASE_URL . 'siren/' . $sirenToCheck, [
                'headers' => [
                    'Accept' => "application/json",
                    'Authorization' => 'Bearer ' . $tokenGenerated,
                ],
                'http_errors' => false,
            ]);

            // checking response return by INSEE API SIRENE
            if ($response->getStatusCode() !== 200) {
                return $this->checkStatusCodeFromApi($response);
            } else {
                // getting the body of the HTTP response and decoding it to JSON
                $dataFetch = json_decode($response->getBody());
                // returning a JSON response with the company information
                return $this->success($dataFetch->uniteLegale, 'Siren found');
            }
        } else {
            // Call getSirenFromLocal as a fallback
            return $this->getSirenFromLocal($siren);
        }

    }


    /**
     * Retrieve information about a company using its SIRET number.
     *
     */
    public function getSiret(string $siret): JsonResponse
    {
        // format siren
        $siretToCheck = str_replace(' ', '', $siret);

        $validator = Validator::make(['siret' => $siretToCheck], [
            'siret' => 'required|numeric|digits:14',
        ]);

        if ($validator->fails()) {
            return $this->error(null, $validator->errors()->first(), 400);
        }

        // check if host is available
        $hostAvailable = $this->checkHost();
        if ($hostAvailable) {
            //generate token
            $tokenGenerated = $this->generateToken();

            $client = new Client();
            $response = $client->get(self::BASE_URL . 'siret/' . $siretToCheck, [
                'headers' => [
                    'Accept' => "application/json",
                    'Authorization' => 'Bearer ' . $tokenGenerated,
                ],
                'http_errors' => false,
            ]);


            // Check errors return by INSEE API SIRENE
            if ($response->getStatusCode() !== 200) {
                return $this->checkStatusCodeFromApi($response);
            } else {
                $dataFetch = json_decode($response->getBody());
                return $this->success($dataFetch->etablissement, 'Siret found');
            }
        } else {
            // Call getSiretFromLocal as a fallback
            return $this->getSiretFromLocal($siret);
        }
    }

    public function getSirenFromLocal(string $siren): JsonResponse
    {
        $response = DB::connection('pgsql_db_sirene')->table('siren')
            ->join('siret', 'siren.siren', '=', 'siret.siren')
            ->select(
                'siren.siren',
                'siren.denominationUniteLegale',
                'siren.denominationUsuelle1UniteLegale',
                'siren.denominationUsuelle2UniteLegale',
                'siren.denominationUsuelle3UniteLegale',
                'siren.etatAdministratifUniteLegale',
                'siren.nomUniteLegale',
                'siren.nomUsageUniteLegale',
                'siren.prenom1UniteLegale',
                'siren.prenom2UniteLegale',
                'siren.prenomUsuelUniteLegale',
                'siret.codecedexetablissement',
                'siret.codepaysetrangeretablissement',
                'siret.codepostaletablissement',
                'siret.complementadresseetablissement',
                'siret.distributionspecialeetablissement',
                'siret.indicerepetitionetablissement',
                'siret.libellecedexetablissement',
                'siret.libellecommuneetablissement',
                'siret.libellecommuneetrangeretablissement',
                'siret.libellepaysetrangeretablissement',
                'siret.libellevoieetablissement',
                'siret.numerovoieetablissement',
                'siret.typevoieetablissement'
            )
            ->where('siren.siren', $siren)
            ->where('siret.etablissementsiege', true)
            ->get();
        if (empty($response)) {
            return $this->error(null, 'Siren not found in local database', 404);
        } else {
            return $this->success($response, "Siren found from local database");
        }
    }

    public function getSiretFromLocal(string $siret)
    {
        $response = Siret::where('siret', $siret)
            ->select(
                'siren', 'etatadministratifetablissement', 'denominationusuelleetablissement', 'enseigne1etablissement',
                'enseigne2etablissement', 'enseigne3etablissement', 'codecedexetablissement', 'codecommuneetablissement',
                'codepaysetrangeretablissement', 'codepostaletablissement', 'complementadresseetablissement',
                'distributionspecialeetablissement', 'indicerepetitionetablissement', 'libellecedexetablissement',
                'libellecommuneetablissement', 'libellecommuneetrangeretablissement', 'libellepaysetrangeretablissement',
                'libellevoieetablissement', 'numerovoieetablissement', 'typevoieetablissement')
            ->get();
        if ($response->isEmpty()) {
            return $this->error(null, 'Siret not found in local database', 404);
        } else {
            return $this->success($response, "siret found from local database");
        }
    }
}
