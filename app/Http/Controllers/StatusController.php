<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StatusController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of all status about owners.
     */
    public function ownerStatus(): JsonResponse
    {
        try {
            $status = Status::where('comment->code', 'LIKE', 'OWNER%')
                ->get();

            return $this->success($status, 'Status List');
        } catch (Exception $error) {
            Log::error($error);

            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Display a listing of all status about establishments.
     */
    public function establishmentStatus(): JsonResponse
    {
        try {
            $status = Status::where('comment->code', 'LIKE', 'ESTABL%')
                ->get();
            return $this->success($status, "Status List");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
