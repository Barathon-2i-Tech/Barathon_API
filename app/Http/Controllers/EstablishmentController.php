<?php

namespace App\Http\Controllers;
use App\Models\Address;
use App\Models\Status;
use App\Traits\HttpResponses;
use App\Models\Establishment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\File;


class EstablishmentController extends Controller
{
use HttpResponses;
/**
 * Display a listing of establishment.
 *
 * @return JsonResponse
 *
 */
public function getEstablishmentList($owner_id): JsonResponse
{
    try {
        // get all establishments from the owner

      $establishments = Establishment::select('establishments.*', 'addresses.*', 'owners.*', 'status.*')
            ->join('addresses', 'addresses.address_id', '=', 'establishments.address_id')
            ->join('owners', 'owners.owner_id', '=', 'establishments.owner_id')
            ->join('status', 'status.status_id', '=', 'establishments.status_id')
            ->where('owners.owner_id', '=', $owner_id)
            ->get();

        // if the establishments list is empty
        if ($establishments->isEmpty())
            return $this->error(null, "Establishment not found", 404);

        // return the establishments list
        return $this->success($establishments, "Establishment List");

    } catch (Exception $error) {
        Log::error($error);
        return $this->error(null, $error->getMessage(), 500);
    }
}

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request, $owner_id)
    {
        try {
            $request->validate([
                'trade_name' => 'required|string|max:255',
                'siret' => 'required|string|max:14', // 14 characters for a SIRET
                'logo' => File::image(),
                'phone' => 'required|string' ,
                'email' => 'email|string',
                'website' => 'string',
                'opening' => 'json',
                'address' => 'min:5|required|string|max:255',
                'postal_code' => 'required|string|size:5',
                'city' => 'required|string|max:255',
            ]);

            $ESTABL_PENDING = Status::where('comment->code', 'ESTABL_PENDING')->first()->status_id;
            $address = Address::create([
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'city' => $request->city
            ]);

            $establishment = Establishment::create([
                'trade_name' => $request->trade_name,
                'siret' => $request->siret,
                'logo' => 'https://picsum.photos/180',
                'phone' => $request->phone,
                'email' => $request->email,
                'website' => $request->website,
                'opening' => $request->opening,
                'owner_id' => $owner_id,
                'address_id' => $address->address_id,
                'status_id' => $ESTABL_PENDING
            ]);


            $establishment->save();

            return $this->success([
                $establishment
            ], "Establishment created", 201);

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 422);
        }
    }

/**
 * Display the specified resource.
 *
 * @param $owner_id
 * @param $establishment_id
 * @return JsonResponse
 */
public function show($owner_id, $establishment_id): JsonResponse
{

    try {
        // get all establishments from the owner
        $establishments = Establishment::with(['owner', 'address', 'establishmentStatus'])
            ->where('owner_id', $owner_id)
            ->where('establishment_id', $establishment_id)
            ->get();

        // if the establishments list is empty
        if ($establishments->isEmpty())
            return $this->error(null, "Establishment not found", 404);

        // return the establishments list
        return $this->success($establishments, "Establishment");

    } catch (Exception $error) {
        Log::error($error);
        return $this->error(null, $error->getMessage(), 500);
    }
}


/**
 * Update the specified resource in storage.
 *
 * @param Request $request
 * @param $owner_id
 * @param $establishment_id
 * @return JsonResponse
 */
public function update(Request $request, $owner_id, $establishment_id)
{
    try {
        // Get the establishment given in parameter
        $establishment = Establishment::where('owner_id', $owner_id)
        ->findOrFail($establishment_id);

        $request->validate([
            'trade_name' => 'required|string|max:255',
            'siret' => 'required|string|max:14', // 14 characters for a SIRET
            'logo' => File::image(),
            'phone' => 'required|string' ,
            'email' => 'email|string',
            'website' => 'string',
            'opening' => 'json',
            'address' => 'min:5|required|string|max:255',
            'postal_code' => 'required|string|size:5',
            'city' => 'required|string|max:255',
           // 'status_id' =>'required|integer'
        ]);


        $dataEstablishment = $request->only(['trade_name', 'siret', 'logo', 'phone', 'email', 'website', 'opening']);

        // Check if the data given in parameter are different from the data in database
        foreach ($dataEstablishment as $field => $value) {
            if ($establishment->{$field} !== $value) {
                $establishment->{$field} = $value;
            }
        }
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
        dump($establishmentChanges);
        dump($addressChanges);

        // Check if the establishment data has changed
        if (empty($establishmentChanges) && empty($addressChanges)) {
            return $this->success([$establishment, $address], "Establishment not updated");
        }

        // Return the updated establishment and address
        return $this->success([$establishment, $address], "Establishment Updated");

    } catch (Exception $error) {
        Log::error($error);
        return $this->error(null, $error->getMessage(), 500);
    }
}

    /**
     * Deleting the establishment ( softDelete )
     *
     * @param $owner_id
     * @param $establishment_id
     * @return JsonResponse
     */
    public function destroy($owner_id, $establishment_id): JsonResponse
    {
        try {
            $establishment = Establishment::withTrashed()->where('owner_id', $owner_id)
                ->where('establishment_id', $establishment_id)
                ->first();

            if ($establishment === null)
                return $this->error(null, "Establishment not found", 404);

            if ($establishment->deleted_at !== null)
                return $this->error(null, "Establishment already deleted", 404);

            $establishment->delete();
            return $this->success(null, "Establishment Deleted successfully");
        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the establishment
     *
     * @param $owner_id
     * @param $establishment_id
     * @return JsonResponse
     */
    public function restore($owner_id, $establishment_id): JsonResponse
    {
        try {
            $establishment = Establishment::withTrashed()->where('owner_id', $owner_id)
                ->where('establishment_id', $establishment_id)
                ->first();

            if ($establishment === null)
                return $this->error(null, "Establishment not found", 404);

            if ($establishment->deleted_at === null)
                return $this->error(null, "Establishment already restored", 404);

            $establishment->restore();
            return $this->success(null, "Establishment Restored successfully");
        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }


}
