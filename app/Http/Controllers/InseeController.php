<?php

namespace App\Http\Controllers;

use App\Models\Siren;
use App\Models\Siret;
use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InseeController extends Controller
{
    use HttpResponses;

    private const BASE_URL = 'https://api.insee.fr/entreprises/sirene/V3/';
    private string $apiKey;
    private const UNAUTHORIZED_ACTION = "This action is unauthorized.";

    private string $inseeConsumerKey;
    private string $inseeConsumerSecret;

    public function __construct()
    {
        // initialise the API key for the INSEE API SIRENE
        $this->inseeConsumerKey = config('services.insee.CONSUMER_KEY');
        $this->inseeConsumerSecret = config('services.insee.CONSUMER_SECRET');

        $this->apiKey = base64_encode($this->inseeConsumerKey . ':' . $this->inseeConsumerSecret);
    }

    /**
     * Check if the host (Insee API) is resolvable.
     */
    public function checkHost(): bool
    {
        //define host
        $host = 'api.insee.fr';
        $isHostResolvable = false;

        //define cache storage time (1 hour)
        $expiration= 3600;

        //get IP address from host
        $ipAddress = cache()->remember($host, $expiration, function () use ($host) {
            return gethostbyname($host);
        });

        // if we get the IP address, the host is resolvable
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
        // create a new Guzzle client
        $client = new Client();

        // send a POST request to the INSEE API to get an access token
        $result = $client->post('https://api.insee.fr/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic ' . $this->apiKey,
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
            'verify' => false // to avoid SSL certificate verification, -k option in curl
        ]);

        // get the access token from the response
        $result = json_decode($result->getBody());

        return $result->access_token;
    }

    /**
     * Check the status code return by the INSEE API SIRENE.
     *
     */
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
        //check if user is admin
        $user = Auth::user();
        if ($user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

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

        // if host is available
        if ($hostAvailable) {
            $tokenGenerated = $this->generateToken();

            // sending http get request with the token generated
            $client = new Client();
            $response = $client->get(self::BASE_URL . 'siren/' . $sirenToCheck . '?champs=identificationStandardUniteLegale', [
                'headers' => [
                    'Accept' => "application/json",
                    'Authorization' => 'Bearer ' . $tokenGenerated,
                ],
                'http_errors' => false, // manage errors manually
            ]);

            // checking response return by INSEE API SIRENE
            if ($response->getStatusCode() !== 200) {
                return $this->checkStatusCodeFromApi($response);
            } else {
                // getting the body of the HTTP response and decoding it to JSON
                $dataFetch = json_decode($response->getBody());
                // returning a JSON response with the company information
                return $this->success(['local' => false, 'response' => $dataFetch->uniteLegale], 'Siren found');
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
        //check if user is admin
        $user = Auth::user();
        if ($user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        // format siren
        $siretToCheck = str_replace(' ', '', $siret);

        $validator = Validator::make(['siret' => $siretToCheck], [
            'siret' => 'required|numeric|digits:14',
        ], [
            'siret.required' => 'Le numero SIRET est obligatoire',
            'siret.numeric' => 'Le numero SIRET doit etre compose de chiffres',
            'siret.digits' => 'Le numero SIRET doit etre compose de 14 chiffres',
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
            $response = $client->get(self::BASE_URL . 'siret/' . $siretToCheck . '?champs=identificationStandardEtablissement', [
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
                return $this->success(['local' => false, 'response' => $dataFetch->etablissement], 'Siret found');
            }
        } else {
            // Call getSiretFromLocal as a fallback
            return $this->getSiretFromLocal($siret);
        }
    }

    /**
     * Retrieve information about a company using its SIREN number.
     * This method is used as a fallback if the INSEE API is not available.
     */
    public function getSirenFromLocal(string $siren): JsonResponse
    {
        $response = Siren::join('siret', 'siren.siren', '=', 'siret.siren')
            ->select(
                'siren.siren',
                'siren.sexeUniteLegale',
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
                'siret.codePaysEtrangerEtablissement',
                'siret.codePostalEtablissement',
                'siret.complementAdresseEtablissement',
                'siret.distributionSpecialeEtablissement',
                'siret.indiceRepetitionEtablissement',
                'siret.libelleCedexEtablissement',
                'siret.libelleCommuneEtablissement',
                'siret.libelleCommuneEtrangerEtablissement',
                'siret.libellePaysEtrangerEtablissement',
                'siret.libelleVoieEtablissement',
                'siret.numeroVoieEtablissement',
                'siret.typeVoieEtablissement'
            )
            ->where('siren.siren', $siren)
            ->where('siret.etablissementSiege', true)
            ->get();

        if ($response->isEmpty()) {
            return $this->error(null, 'Siren not found in local database', 404);
        } else {
            return $this->success(['local' => true, 'response' => $response[0]], "Siren found from local database");
        }
    }

    /**
     * Retrieve information about a company using its SIRET number from local database.
     * This method is used as a fallback if the INSEE API is not available.
     */
    public function getSiretFromLocal(string $siret)
    {
        $response = Siret::join('siren', 'siret.siren', '=', 'siren.siren')
            ->select(
                'siret.siren',
                'siret.siret',
                'siret.etablissementSiege',
                'siret.etatAdministratifEtablissement',
                'siret.denominationUsuelleEtablissement',
                'siret.enseigne1Etablissement',
                'siret.enseigne2Etablissement',
                'siret.enseigne3Etablissement',
                'siret.codePaysEtrangerEtablissement',
                'siret.codePostalEtablissement',
                'siret.complementAdresseEtablissement',
                'siret.distributionSpecialeEtablissement',
                'siret.indiceRepetitionEtablissement',
                'siret.libelleCedexEtablissement',
                'siret.libelleCommuneEtablissement',
                'siret.libelleCommuneEtrangerEtablissement',
                'siret.libellePaysEtrangerEtablissement',
                'siret.libelleVoieEtablissement',
                'siret.numeroVoieEtablissement',
                'siret.typeVoieEtablissement',
                'siren.denominationUniteLegale'
            )
            ->where('siret.siret', $siret)
            ->get();

        if ($response->isEmpty()) {
            return $this->error(null, 'Siret not found in local database', 404);
        } else {
            return $this->success(['local' => true, 'response' => $response[0]], "siret found from local database");
        }
    }
}
