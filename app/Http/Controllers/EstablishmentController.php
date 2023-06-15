<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Establishment;
use App\Models\Status;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EstablishmentController extends Controller
{
    use HttpResponses;

    private const ESTABLISHMENT_NOT_FOUND = "Establishment not found";
    private const UNAUTHORIZED_ACTION = "This action is unauthorized.";
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
    public function getEstablishmentListByOwnerId(Request $request, int $ownerId): JsonResponse
    {
        $user = $request->user();

        if ($user->owner_id !== $ownerId) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        // get all establishments from the owner
        $establishments = DB::table('establishments')
            ->join('status', 'establishments.status_id', '=', 'status.status_id')
            ->join('addresses', 'establishments.address_id', '=', 'addresses.address_id')
            ->where('establishments.owner_id', $ownerId)
            ->where('establishments.deleted_at', null)
            ->select('establishments.*', 'addresses.*', 'status.status_id', 'status.comment')
            ->get();

        // if the establishments list is empty
        if ($establishments->isEmpty()) {
            return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
        }

        foreach ($establishments as $establishment) {
            $establishment->validation_code = Crypt::decryptString($establishment->validation_code);
        }

        // return the establishments list
        return $this->success($establishments, "Establishment List");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param int $ownerId
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(Request $request, int $ownerId): JsonResponse
    {
        // catch authentificated user
        $user = $request->user();

        if ($user->owner_id !== $ownerId) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

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
            $establishmentLogoPath = $request->file('logo')->storePublicly('logos', 'public');

            $establishmentLogoPath = config('app.url') . Storage::url($establishmentLogoPath);
        }

        $address = Address::create([
            'address' => $request->input('address'),
            'postal_code' => $request->input('postal_code'),
            'city' => $request->input('city')
        ]);

        // Decodage de la valeur de la clé "opening" en JSON
        $opening = json_decode($request->opening, true);

        $validationCode = Crypt::encryptString(random_int(1000, 9999));

        $establishment = Establishment::create([
            'trade_name' => $request->input('trade_name'),
            'siret' => $request->input('siret'),
            'logo' => $establishmentLogoPath,
            'phone' => $request->input('phone'),
            'email' => $request->email,
            'website' => $request->input('website'),
            'opening' => $opening,
            'owner_id' => $ownerId,
            'address_id' => $address->address_id,
            'validation_code' => $validationCode,
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
     *
     * @param $ownerId
     * @param $establishmentId
     * @return JsonResponse
     */
    public function show(Request $request, int $ownerId, int $establishmentId): JsonResponse
    {

        // Récupérez l'utilisateur authentifié
        $user = $request->user();
        if (!($user->owner_id === $ownerId || $user->administrator_id !== null)) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }


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
     */
    public function update(Request $request, int $establishmentId): JsonResponse
    {
        $user = $request->user();
        // Find the establishment by its owner ID and establishment ID
        $establishment = Establishment::findOrFail($establishmentId);

        if ($user->owner_id !== $establishment->owner_id) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $request->validate([
            'trade_name' => self::STRING_VALIDATION,
            'siret' => 'required', 'string', 'size:14', Rule::unique('establishments')->ignore($establishment),
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

        // Handle logo file upload if a new logo is present in the request

        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Delete the old logo if it exists
            if ($establishment->logo && Storage::disk('public')->exists($establishment->logo)) {
                Storage::disk('public')->delete($establishment->logo);
            }

            // Store the new logo and update the establishment object with the new logo path
            $logoPath = $request->file('logo')->storePublicly('logos', 'public');
            $logoPath = config('app.url') . Storage::url($logoPath);
            $establishment->logo = $logoPath;
            $establishment->save();
        }

        // Get the data to update the establishment object and decode the opening hours
        $dataEstablishment = $request->only([
            'trade_name',
            'siret',
            'phone',
            'email',
            'opening'
        ]);
        $dataEstablishment['opening'] = json_decode($dataEstablishment['opening'], true);

        // Update establishment fields if they have changed
        foreach ($dataEstablishment as $field => $value) {
            if ($establishment->{$field} !== $value) {
                $establishment->{$field} = $value;
            }
        }

        // Update the establishment's status to ESTABL_PENDING and save it to the database
        $establishmentPending = Status::where('comment->code', 'ESTABL_PENDING')->first();
        $establishment->status_id = $establishmentPending->status_id;
        $establishment->save();


        // Get the address linked to the establishment
        $address = Address::where('address_id', $establishment->address_id)->first();

        // Get the data to update the address object
        $dataAddress = $request->only(['address', 'postal_code', 'city']);

        // Update address fields if they have changed
        foreach ($dataAddress as $field => $value) {
            if ($address->{$field} !== $value) {
                $address->{$field} = $value;
            }
        }
        $address->save();

        // Get the changes made to the establishment and address data
        $establishmentChanges = $establishment->getChanges();
        $addressChanges = $address->getChanges();

        // Check if the establishment data has changed and return a response accordingly
        if (empty($establishmentChanges) && empty($addressChanges)) {
            return $this->success([$establishment, $address], "Establishment not updated");
        } else {
            // Refresh the establishment data from the database before returning
            $establishment->refresh();
            return $this->success([$establishment, $address], "Establishment Updated");
        }

    }


    /**
     * Deleting the establishment ( softDelete )
     */
    public function destroy(Request $request, int $establishmentId): JsonResponse
    {
        // Récupérez l'utilisateur authentifié
        $user = $request->user();

        $establishment = Establishment::withTrashed()->where('establishment_id', $establishmentId)
            ->first();

        if ($establishment === null) {
            return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
        }

        if (!($user->owner_id === $establishment->owner_id || $user->administrator_id !== null)) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        if ($establishment->deleted_at !== null) {
            return $this->error(null, "Establishment already deleted", 404);
        }

        $establishment->delete();
        return $this->success(null, "Establishment Deleted successfully");

    }

    /**
     * Restoring the establishment
     */
    public function restore(Request $request, int $establishmentId): JsonResponse
    {
        $user = $request->user();
        $establishment = Establishment::withTrashed()->where('establishment_id', $establishmentId)
            ->first();

        if ($user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

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
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

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
     */
    public function validateEstablishment(int $establishmentId, int $statusCode): jsonResponse
    {

        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $establishment = Establishment::find($establishmentId);

        if ($establishment === null) {
            return $this->error(null, self::ESTABLISHMENT_NOT_FOUND, 404);
        }

        if ($establishment->status_id === $statusCode) {
            return $this->error(null, 'Establishment with same status', 409);
        }

        $establishment->status_id = $statusCode;
        $establishment->save();
        return $this->success(null, "Status updated");

    }

    /**
     * Get how many establishment need to be validated
     */

    public function getEstablishmentToValidate(): JsonResponse
    {
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $establishmentToValidate = Establishment::where('status_id', 6)->count();
        return $this->success($establishmentToValidate, "Establishments to validate");
    }
}
