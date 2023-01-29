<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\User;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class OwnerController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function getOwnerList(): JsonResponse
    {
        try {
            $owners = User::with(['owner', 'owner.owner_status'])
                ->whereHas('owner', function ($query) {
                    $query->whereNotNull('owner_id');
                })
                ->get();
            return $this->success($owners, "Owner List");
        }
        catch (Exception $error) {
            Log::error($error);
            return $this->error(null,$error->getMessage(), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'siren' => 'required|string|max:255',
            'kbis' => 'required|string',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => "https://picsum.photos/180",
        ]);

        $owner = Owner::create([
            'siren' => $request->siren,
            'kbis' => $request->kbis,
            'status_id' => 3,
        ]);

        $user->owner_id = $owner->owner_id;
        $user->save();

        return $this->success([
            'userLogged' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ], "Owner Created");
    }

    /**
     * Display the specified resource.
     *
     * @param Owner $owner
     * @return void
     */
    public function show(Owner $owner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Owner $owner
     * @return Response
     */
    public function edit(Owner $owner): Response
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Owner $owner
     * @return Response
     */
    public function update(Request $request, Owner $owner): Response
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Owner $owner
     * @return JsonResponse
     */
    public function destroy($user_id): JsonResponse
    {
        try {
            User::where('user_id',$user_id)->delete();
            return $this->success("", "Owner Deleted");
        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null,$error->getMessage(), 500);
        }
    }
}
