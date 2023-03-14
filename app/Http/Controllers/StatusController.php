<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;

class StatusController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of all status about establishments.
     *
     * @return JsonResponse
     */
    public function establishmentStatus()
    {
        try {
            $status = Status::where('comment->code', 'LIKE', 'ESTABL%')
                ->get();
            return $this->success($status, "Status List");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Display a listing of all status about owners.
     *
     * @return JsonResponse
     */
    public function ownerStatus()
    {
        try {
            $status = Status::where('comment->code', 'LIKE', 'OWNER%')
                ->get();

            return $this->success($status, 'Status List');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
