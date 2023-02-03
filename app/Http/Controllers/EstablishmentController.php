<?php

namespace App\Http\Controllers;
use App\Models\Owner;
use App\Traits\HttpResponses;
use App\Models\Establishment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Establishment  $establishment
     * @return Response
     */
    public function show(Establishment $establishment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Establishment  $establishment
     * @return Response
     */
    public function edit(Establishment $establishment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Establishment  $establishment
     * @return Response
     */
    public function update(Request $request, Establishment $establishment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Establishment  $establishment
     * @return Response
     */
    public function destroy(Establishment $establishment)
    {
        //
    }

    /*public function getEstablishementsByOwnerEstablishment($id){
        // Get the user
        $user = User::find($id);

        //get owner
        $owner = Owner::find($id);


        // Get the user establishment
        if($user->owner == null){
            return $this->error("error", "the User is not a owner", 500);
        }

        $establishments = Establishment::where('$owner', $id)->get();


        // Return all events
        return $this->success([
            'establishments' => $establishments,
        ]);

    }*/
}
