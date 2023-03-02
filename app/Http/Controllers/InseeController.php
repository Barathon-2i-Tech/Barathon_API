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

    protected string $apiKey;

    public function __construct()
    {
        // initialise the API key for the INSEE API SIRENE
        $this->apiKey = base64_encode(env('INSEE_CONSUMER_KEY') . ':' . env('INSEE_CONSUMER_SECRET'));
    }

    /**
     * Generate a valid access token for the SIRENE API of INSEE.
     * (subscription required to use this API)
     *
     * @return string  access token for the INSEE API SIRENE
     * @throws GuzzleException
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

    /**
     * Retrieve information about a company using its SIREN number.
     *
     * @param string $siren enterprise SIREN number
     * @return JsonResponse
     */
    public function getSiren(string $siren): JsonResponse
    {
        try {
            // format siren
            $sirenToCheck = str_replace(' ', '', $siren);

            $validator = Validator::make(['siren' => $sirenToCheck], [
                'siren' => 'required|numeric|digits:9',
            ]);

            if ($validator->fails()) {
                return $this->error(null, $validator->errors()->first(), 400);
            }

            //generate token
            $tokenGenerated = $this->generateToken();

            $client = new Client();
            $response = $client->get('https://api.insee.fr/entreprises/sirene/V3/siren/' . $sirenToCheck, [
                'headers' => [
                    'Accept' => "application/json",
                    'Authorization' => 'Bearer ' . $tokenGenerated,
                ],
                'http_errors' => false,
            ]);

            // Check errors return by INSEE API SIRENE
            if ($response->getStatusCode() !== 200) {
                return match ($response->getStatusCode()) {
                    401 => $this->error(null, 'Unauthorized', 401),
                    403 => $this->error(null, 'Access forbidden', 403),
                    404 => $this->error(null, 'Siren not found', 404),
                    429 => $this->error(null, 'Too many requests', 429),
                    500 => $this->error(null, 'Internal server error', 500),
                    default => $this->error(null, 'Unknown error', 500),
                };
            } else {
                $dataFetch = json_decode($response->getBody());
                return $this->success($dataFetch->uniteLegale, 'Siren found');
            }
        } catch (GuzzleException $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Retrieve information about a company using its SIRET number.
     *
     * @param string $siret enterprise SIRET number
     * @return JsonResponse
     */
    public function getSiret(string $siret): JsonResponse
    {
        try {
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
            $response = $client->get('https://api.insee.fr/entreprises/sirene/V3/siret/' . $siretToCheck, [
                'headers' => [
                    'Accept' => "application/json",
                    'Authorization' => 'Bearer ' . $tokenGenerated,
                ],
                'http_errors' => false,
            ]);

            // Check errors return by INSEE API SIRENE
            if ($response->getStatusCode() !== 200) {
                return match ($response->getStatusCode()) {
                    401 => $this->error(null, 'Unauthorized', 401),
                    403 => $this->error(null, 'Access forbidden', 403),
                    404 => $this->error(null, 'Siret not found', 404),
                    429 => $this->error(null, 'Too many requests', 429),
                    500 => $this->error(null, 'Internal server error', 500),
                    default => $this->error(null, 'Unknown error', 500),
                };
            } else {
                $dataFetch = json_decode($response->getBody());
                return $this->success($dataFetch, 'Siret found');
            }
        } catch (GuzzleException $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}


