<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;


class EstablishmentController extends Controller
{
    use HttpResponses;

    private const ESTABLISHMENT_NOT_FOUND = "Establishment not found";



    /**
     * Deleting the establishment ( softDelete )
     */
    public function destroy(int $ownerId, int $establishmentId): JsonResponse
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
     */
    public function restore(int $ownerId, int $establishmentId): JsonResponse
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
                ->withTrashed()
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
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Get how many establishment need to be validated
     */

    public function getEstablishmentToValidate(): JsonResponse
    {
        try {
            $establishmentToValidate = Establishment::where('status_id', 6)->count();

            return $this->success($establishmentToValidate, "Establishments to validate");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
