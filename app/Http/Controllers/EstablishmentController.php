<?php

namespace App\Http\Controllers;
use App\Traits\HttpResponses;
use App\Models\Establishment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;


class EstablishmentController extends Controller
{
use HttpResponses;
/**
 * Display a listing of the resource.
 *
 * @return JsonResponse
 *
 * @Middleware("auth:sanctum")
 */
public function getEstablishmentList($owner_id): JsonResponse
{
    try {
        // get all establishments from the owner
        $establishments = Establishment::with(['owner', 'address', 'establishmentStatus'])
            ->where('owner_id', $owner_id)
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
 * @return Response
 */
public function store(Request $request)
{
    //
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
 * @return Response
 */
public function update(Request $request, $owner_id, $establishment_id)
{
    //
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
