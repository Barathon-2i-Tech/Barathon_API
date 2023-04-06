<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Traits\HttpResponses;
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
        $status = Status::where('comment->code', 'LIKE', 'ESTABL%')
            ->get();
        return $this->success($status, "Status List");
    }

    /**
     * Display a listing of all status about owners.
     */
    public function ownerStatus(): JsonResponse
    {
        $status = Status::where('comment->code', 'LIKE', 'OWNER%')
            ->get();

        return $this->success($status, 'Status List');
    }
}
