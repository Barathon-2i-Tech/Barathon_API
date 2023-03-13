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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Status $status)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Status $status)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Status $status)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Status $status)
    {
        //
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
            Log::error($error);

            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
