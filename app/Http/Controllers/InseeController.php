<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InseeController extends Controller
{
    use HttpResponses;

    private string $apiKey;
    private const BASE_URL = 'https://api.insee.fr/entreprises/sirene/V3/';


    public function __construct()
    {
        // initialise the API key for the INSEE API SIRENE
        $this->apiKey = base64_encode(env('INSEE_CONSUMER_KEY') . ':' . env('INSEE_CONSUMER_SECRET'));
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

        //generating acces token
        $tokenGenerated = $this->generateToken();

        // instantiation of the Guzzle HTTP client
        $client = new Client();

        // sending http get request
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
    }
}
