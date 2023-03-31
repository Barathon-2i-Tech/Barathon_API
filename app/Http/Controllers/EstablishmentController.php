<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Establishment;
use App\Models\Status;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EstablishmentController extends Controller
{
    use HttpResponses;

    private const ESTABLISHMENT_NOT_FOUND = "Establishment not found";
    private const STRING_VALIDATION = 'required|string|max:255';
    private const ADDRESS_ERROR = "L\'adresse est invalide";
    private const PHONEVALIDATION = ['regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'];
    private const NULLABLE_STRING_VALIDATION = 'nullable|string|max:255';


    /**
     * Display a listing of establishment.
     *
     * @return JsonResponse
     *
     */
    public function getEstablishmentListByOwnerId($ownerId): JsonResponse
    {
        try {
            // get all establishments from the owner

            $establishments = DB::table('establishments')
                ->join('status', 'establishments.status_id', '=', 'status.status_id')
                ->join('addresses', 'establishments.address_id', '=', 'addresses.address_id')
                ->where('establishments.owner_id', $ownerId)
                ->select('establishments.*', 'addresses.*', 'status.status_id', 'status.comment')
                ->get();

            // if the establishments list is empty
            if ($establishments->isEmpty()) {
                return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
            }

            // Add the correct URL prefix to the logo_url


            foreach ($establishments as $establishment) {
                $establishment->logo_url = env('APP_URL').Storage::url($establishment->logo);
            }

            // return the establishments list
            return $this->success($establishments, "Establishment List");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $ownerId
     * @return JsonResponse
     */
    public function store(Request $request, $ownerId): JsonResponse
    {
        

        $request->validate([
            'trade_name' => self::STRING_VALIDATION,
            'siret' => 'required|string|size:14|unique:establishments',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => self::PHONEVALIDATION,
            'email' => 'nullable|email|string',
            'website' => self::NULLABLE_STRING_VALIDATION,
            'opening' => 'nullable|json',
            'address' => 'min:5|required|string|max:255',
            'postal_code' => 'required|string|size:5',
            'city' => self::STRING_VALIDATION,
        ], [
            'siret.unique' => 'Le siret doit etre unique',
            'siret.size' => 'Le siret doit contenir 14 caractères',
            'postal_code.size' => 'Le code postal doit contenir 5 caractères',
            'email.email' => "L\'email doit être au format email",
            'phone.regex' => "Le numéro de téléphone doit être au format 00 00 00 00 00 ou +33 0 00 00 00 00",
            'opening.json' => "Le format de l\'ouverture doit être au format JSON",
            'address.min' => self::ADDRESS_ERROR,
            'address.max' => self::ADDRESS_ERROR,

        ]);
           
            $establPending = Status::where('comment->code', 'ESTABL_PENDING')->first()->status_id;


            $establishmentLogoPath = null;

            if ($request->hasFile('logo')) {
                $establishmentLogoPath = $request->logo->store('logos', 'public');
            }

            $address = Address::create([
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'city' => $request->city
            ]);

            // Decodage de la valeur de la clé "opening" en JSON
            $opening = json_decode($request->opening, true);


            $establishment = Establishment::create([
                'trade_name' => $request->trade_name,
                'siret' => $request->siret,
                'logo' => $establishmentLogoPath,
                'phone' => $request->phone,
                'email' => $request->email,
                'website' => $request->website,
                'opening' => $opening,
                'owner_id' => $ownerId,
                'address_id' => $address->address_id,
                'status_id' => $establPending
            ]);

            $establishmentPending = Status::where('comment->code', 'ESTABL_PENDING')->first();
            $establishment->status_id = $establishmentPending->status_id;
            $establishment->save();
            $establishment->logo_url = env('APP_URL').Storage::url($establishment->logo);

            return $this->success([
                $establishment
            ], "Establishment created", 201);

    }

    /**
     * Display the specified resource.
     *
     * @param $ownerId
     * @param $establishmentId
     * @return JsonResponse
     */
    public function show($ownerId, $establishmentId): JsonResponse
    {

        try {
            // get all establishments from the owner
            $establishments = Establishment::select('establishments.*', 'addresses.*', 'owners.*', 'status.*')
                ->join('addresses', 'addresses.address_id', '=', 'establishments.address_id')
                ->join('owners', 'owners.owner_id', '=', 'establishments.owner_id')
                ->join('status', 'status.status_id', '=', 'establishments.status_id')
                ->where('owners.owner_id', '=', $ownerId)
                ->where('establishments.establishment_id', '=', $establishmentId)
                ->get();

            // if the establishments list is empty
            if ($establishments->isEmpty()) {
                return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
            }
            // return the establishments list
            return $this->success($establishments, "Establishment");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $ownerId
     * @param $establishmentId
     * @return JsonResponse
     */
    public function update(Request $request, $ownerId, $establishmentId): JsonResponse
{
    try {
        // Get the establishment given in parameter
        $establishment = Establishment::where('owner_id', $ownerId)
            ->findOrFail($establishmentId);

        $request->validate([
            'trade_name' => self::STRING_VALIDATION,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'phone' => 'required|string',
            'email' => 'nullable|email|string',
            'website' => 'nullable|string',
            'opening' => 'nullable|json',
            'address' => 'min:5|required|string|max:255',
            'postal_code' => 'required|string|size:5',
            'city' => self::STRING_VALIDATION,
        ]);

        if ($request->hasFile('logo')) {
            // Delete the old logo if it exists
            if ($establishment->logo) {
                Storage::disk('public')->delete($establishment->logo);
            }

            // Store the new logo and update the establishment's logo field
            $establishmentLogoPath = $request->logo->store('logos', 'public');
            $establishment->logo = $establishmentLogoPath;
        }

        $dataEstablishment = $request->only(
            ['trade_name', 'logo', 'phone', 'email', 'website', 'opening']);

        // Decode the 'opening' value
        $dataEstablishment['opening'] = json_decode($dataEstablishment['opening'], true);

        // Check if the data given in parameter are different from the data in database
        foreach ($dataEstablishment as $field => $value) {
            if ($establishment->{$field} !== $value) {
                $establishment->{$field} = $value;
            }
        }
        $establishmentPending = Status::where('comment->code', 'ESTABL_PENDING')->first();
        $establishment->status_id = $establishmentPending->status_id;
        $establishment->save();

        // Get the address linked to the establishment
        $address = Address::where('address_id', $establishment->address_id)->first();

        $dataAddress = $request->only(['address', 'postal_code', 'city']);

        // Check if the data given in parameter are different from the data in database
        foreach ($dataAddress as $field => $value) {
            if ($address->{$field} !== $value) {
                $address->{$field} = $value;
            }
        }
        $address->save();

        $establishmentChanges = $establishment->getChanges();
        $addressChanges = $address->getChanges();

        // Check if the establishment data has changed
        if (empty($establishmentChanges) && empty($addressChanges)) {
            return $this->success([$establishment, $address], "Establishment not updated");
        }

        // Return the updated establishment and address
        return $this->success([$establishment, $address], "Establishment Updated");

    } catch (Exception $error) {
        return $this->error(null, $error->getMessage(), 500);
    }
}

    /**
     * Deleting the establishment ( softDelete )
     *
     * @param $ownerId
     * @param $establishmentId
     * @return JsonResponse
     */
    public function destroy($ownerId, $establishmentId): JsonResponse
    {
        try {
            $establishment = Establishment::withTrashed()->where('owner_id', $ownerId)
                ->where('establishment_id', $establishmentId)
                ->first();

            if ($establishment === null) {
                return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
            }
            if ($establishment->deleted_at !== null) {
                return $this->error(null, "Establishment already deleted", 404);
            }

            $establishment->delete();
            return $this->success(null, "Establishment Deleted successfully");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the establishment
     *
     * @param $ownerId
     * @param $establishmentId
     * @return JsonResponse
     */
    public function restore($ownerId, $establishmentId): JsonResponse
    {
        try {
            $establishment = Establishment::withTrashed()->where('owner_id', $ownerId)
                ->where('establishment_id', $establishmentId)
                ->first();

            if ($establishment === null) {
                return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
            }

            if ($establishment->deleted_at === null) {
                return $this->error(null, "Establishment already restored", 404);
            }
            $establishment->restore();
            return $this->success(null, "Establishment Restored successfully");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     *Get all establishments for the admin part
     *
    */

    public function getAllEstablishments(): JsonResponse
    {
        try {
            $establishments = Establishment::select('establishments.*', 'addresses.*', 'owners.*', 'status.*')
                ->join('addresses', 'addresses.address_id', '=', 'establishments.address_id')
                ->join('owners', 'owners.owner_id', '=', 'establishments.owner_id')
                ->join('status', 'status.status_id', '=', 'establishments.status_id')
                ->withTrashed() // get the establishments deleted
                ->get();

            if ($establishments->isEmpty()) {
                return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
            }
            return $this->success($establishments, "Establishments");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Validate the establishment
     *
     * @param $establishmentId
     * @param $statusCode
     * @return JsonResponse
     */
    public function validateEstablishment($establishmentId, $statusCode): jsonResponse
    {
        try {
            //parse the status code
            $statusCode = intval($statusCode);

            $establishment = Establishment::find($establishmentId);

            if ($establishment === null) {
                return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
            }

            if ($establishment->status_id === $statusCode) {
                return $this->error(null, 'Establishment already validated', 404);
            }

            $establishment->status_id = $statusCode;
            $establishment->save();
            return $this->success(null, "Validation updated");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Get how many establishment need to be validated
     * @return JsonResponse
     */

    public function getEstablishmentToValidate(): JsonResponse
    {
        try {
            $establishmentToValidate = Establishment::where('status_id', 6)->count();

            return $this->success($establishmentToValidate, "Establishments to validate");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
