<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponses;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SirenController extends Controller
{
    use HttpResponses;


    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = base64_encode(env('INSEE_CONSUMER_KEY') . ':' . env('INSEE_CONSUMER_SECRET'));
    }

    public function generateToken()
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

    public function getSiren($siren)
    {
        try {
            // format siren
            $sirenToCheck = str_replace(' ', '', $siren);

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
            $response = json_decode($response->getBody());
            return $this->success($response, 'Siren found');

        } catch (GuzzleException $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
