<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Exception;


class OwnerController extends Controller
{
    use HttpResponses;

    private const STRINGVALIDATION = 'required|string|max:255';
    private const OWNERNOTFOUND = "Owner not found";
    private const USERNOTFOUND = "User not found";

    /**
     * Display a listing of all owners
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

            if ($owners->isEmpty()) {
                return $this->error(null, "No owners found", 404);
            }

            return $this->success($owners, "Owner List");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }


    /**
     * Store a newly created owner in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        $request->validate([
            'first_name' => self::STRINGVALIDATION,
            'last_name' => self::STRINGVALIDATION,
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'siren' => 'required|string|size:9',
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
            'status_id' => 3, // 3 = pending
        ]);

        $user->owner_id = $owner->owner_id;
        $user->save();

        return $this->success([
            'userLogged' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ], "Owner Created");
    }

    /**
     * Display the specified owner.
     *
     * @param $userId
     * @return JsonResponse
     */
    public function show($userId): JsonResponse
    {
        try {
            $owner = DB::table('users')
                ->join('owners', 'users.owner_id', '=', 'owners.owner_id')
                ->join('status', 'owners.status_id', '=', 'status.status_id')
                ->select('users.*', 'owners.*', 'status.*')
                ->where('users.user_id', $userId)
                ->get();

            if ($owner->isEmpty()) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }
            return $this->success($owner, "Owner Details");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }


    /**
     * Update the specified owner in storage.
     *
     * @param Request $request
     * @param $userId
     * @return JsonResponse
     */
    public function update(Request $request, $userId): JsonResponse
    {
        try {
            //get the user given in parameter
            $user = User::find($userId);
            if ($user === null) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }

            // check if the user is an owner
            if ($user->owner_id === null) {
                return $this->error(null, "Owner not found", 404);
            }


            // validate the request
            $request->validate([
                'first_name' => self::STRINGVALIDATION,
                'last_name' => self::STRINGVALIDATION,
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users')->ignore($user), // Ignore the user given in parameter
                ],
                'phone' => 'string|max:13',
            ]);


            $userData = $request->only(['first_name', 'last_name', 'email']);
            // Check if the data given in parameter are different from the data in database
            foreach ($userData as $field => $value) {
                if ($user->{$field} !== $value) {
                    $user->{$field} = $value;
                }
            }
            $user->save();

            $owner = Owner::find($user->owner_id);

            $ownerData = $request->only(['phone']);
            // Check if the data given in parameter are different from the data in database
            foreach ($ownerData as $field => $value) {
                if ($owner->{$field} !== $value) {
                    $owner->{$field} = $value;
                }
            }
            $owner->save();

            $userChanges = $user->getChanges();
            $ownerChanges = $owner->getChanges();

            if (empty($userChanges) && empty($ownerChanges)) {
                return $this->success(null, "Owner not updated");
            }

            return $this->success([$user, $owner], "Owner Updated");


        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Deleting the owner ( softDelete )
     *
     * @param $userId
     * @return JsonResponse
     */
    public function destroy($userId): JsonResponse
    {
        try {

            //check if the user exist
            $user = User::withTrashed()->where('user_id', $userId)->first();
            if ($user === null) {
                return $this->error(null, self::USERNOTFOUND, 404);
            }
            //check if the user is an owner
            if ($user->owner_id === null) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }
            //check if the user is already deleted
            if ($user->deleted_at !== null) {
                return $this->error(null, "Owner already deleted", 404);
            }
            //delete the user
            User::where('user_id', $userId)->delete();
            return $this->success(null, "Owner Deleted");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the owner
     *
     * @param $userId
     * @return JsonResponse
     */
    public function restore($userId): JsonResponse
    {
        try {
            //check if the user exist
            $user = User::withTrashed()->where('user_id', $userId)->first();
            if ($user === null) {
                return $this->error(null, self::USERNOTFOUND, 404);
            }
            //check if the user is an owner
            if ($user->owner_id === null) {
                return $this->error(null, self::OWNERNOTFOUND, 404);
            }
            //check if the user is already restored
            if ($user->deleted_at === null) {
                return $this->error(null, "Owner already restored", 404);
            }
            User::withTrashed()->where('user_id', $userId)->restore();
            return $this->success(null, "Owner Restored");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
