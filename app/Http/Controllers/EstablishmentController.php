<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Establishment;
use App\Models\Status;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EstablishmentController extends Controller
{
    use HttpResponses;

    private const ESTABLISHMENT_NOT_FOUND = "Establishment not found";
    private const STRING_VALIDATION = 'required|string|max:255';
    private const PHONEVALIDATION = ['regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'];
    private const NULLABLE_STRING_VALIDATION = 'nullable|string|max:255';
    private const ADDRESS_ERROR = "L\'adresse est invalide";


    /**
     * Display a listing of establishment.
     */
    public function getEstablishmentListByOwnerId($ownerId): JsonResponse
    {
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

        // return the establishments list
        return $this->success($establishments, "Establishment List");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $ownerId): JsonResponse
    {
        $request->validate([
            'trade_name' => self::STRING_VALIDATION,
            'siret' => 'required|string|size:14|unique:establishments',
            'logo' => self::NULLABLE_STRING_VALIDATION,
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

        $address = Address::create([
            'address' => $request->input('address'),
            'postal_code' => $request->input('postal_code'),
            'city' => $request->input('city'),
        ]);

        $opening = json_decode($request->input('opening'), true);

        $establishment = Establishment::create([
            'trade_name' => $request->input('trade_name'),
            'siret' => $request->input('siret'),
            'logo' => $request->input('logo'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'website' => $request->input('website'),
            'opening' => $opening,
            'owner_id' => $ownerId,
            'address_id' => $address->address_id,
            'status_id' => $establPending
        ]);

        $establishmentPending = Status::where('comment->code', 'ESTABL_PENDING')->first();
        $establishment->status_id = $establishmentPending->status_id;
        $establishment->save();

        return $this->success([
            $establishment
        ], "Establishment created", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($ownerId, $establishmentId): JsonResponse
    {
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
    }


    /**
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request, $ownerId, $establishmentId): JsonResponse
    {
        // Get the establishment given in parameter
        $establishment = Establishment::where('owner_id', $ownerId)
            ->findOrFail($establishmentId);

        $request->validate([
            'trade_name' => self::STRING_VALIDATION,
            'logo' => self::NULLABLE_STRING_VALIDATION,
            'phone' => 'required|string',
            'email' => 'nullable|email|string',
            'website' => self::NULLABLE_STRING_VALIDATION,
            'opening' => 'nullable|json',
            'address' => 'min:5|required|string|max:255',
            'postal_code' => 'required|string|size:5',
            'city' => self::STRING_VALIDATION,
        ], [
            'email.email' => "L\'email doit être au format email",
            'phone.regex' => "Le numéro de téléphone doit être au format 00 00 00 00 00 ou +33 0 00 00 00 00",
            'opening.json' => "Le format de l\'ouverture doit être au format JSON",
            'address.min' => self::ADDRESS_ERROR,
            'address.max' => self::ADDRESS_ERROR,
        ]);


        $dataEstablishment = $request->only(
            ['trade_name', 'siret', 'logo', 'phone', 'email', 'website', 'opening']);

        // Décoder la valeur de 'opening'
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
    }

    /**
     * Deleting the establishment ( softDelete )
     *
     */
    public function destroy($ownerId, $establishmentId): JsonResponse
    {
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
    }

    /**
     * Restoring the establishment
     *
     */
    public function restore($ownerId, $establishmentId): JsonResponse
    {
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
    }

    /**
     *Get all establishments for the admin part
     *
     */

    public function getAllEstablishments(): JsonResponse
    {
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
    }

    /**
     * Validate the establishment
     *
     */
    public function validateEstablishment($establishmentId, $statusCode): jsonResponse
    {
        //parse the status code
        $statusCode = intval($statusCode);

        $establishment = Establishment::find($establishmentId);

        if ($establishment === null) {
            return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
        }

        if ($establishment->status_id == $statusCode) {
            return $this->error(null, 'Establishment already validated', 404);
        }

        $establishment->status_id = $statusCode;
        $establishment->save();
        return $this->success(null, "Validation updated");
    }

    /**
     * Get how many establishment need to be validated
     */

    public function getEstablishmentToValidate(): JsonResponse
    {
        $establishmentToValidate = Establishment::where('status_id', 6)->count();

        return $this->success($establishmentToValidate, "Establishments to validate");
    }
}
