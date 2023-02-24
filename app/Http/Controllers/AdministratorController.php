<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdministratorController extends Controller
{
    use HttpResponses;

    private const USER_NOT_FOUND = 'User not found';
    private const ADMINISTRATOR_NOT_FOUND = 'Administrator not found';
    private const STRINGVALIDATION = 'required|string|max:255';

    /**
     * Display a listing of all administrator.
     *
     * @return JsonResponse
     */
    public function getAdministratorList(): JsonResponse
    {
        try {
            $administrators = DB::table('users')
                ->join('administrators', 'users.administrator_id', '=', 'administrators.administrator_id')
                ->select('users.*', 'administrators.*')
                ->get();

            if ($administrators->isEmpty()) {
                return $this->error(null, self::ADMINISTRATOR_NOT_FOUND, 404);
            }

            return $this->success($administrators, 'Admnistrators List');
        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Store a newly created administrator in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {

        $request->validate([
            'first_name' => self::STRINGVALIDATION,
            'last_name' => self::STRINGVALIDATION,
            'superAdmin' => 'boolean', // Accept 0 or 1 only with postman
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => "https://picsum.photos/180",
        ]);

        $admin = Administrator::create([
            "superAdmin" => $request->superAdmin
        ]);

        $user->administrator_id = $admin->administrator_id;
        $user->save();

        return $this->success([
            'userLogged' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ], "Admin Created");
    }

    /**
     * Display the specified resource.
     *
     * @param $userId
     * @return JsonResponse
     */
    public
    function show($userId): JsonResponse
    {
        try {
            $administrator = DB::table('users')
                ->join('administrators', 'users.administrator_id', '=', 'administrators.administrator_id')
                ->select('users.*', 'administrators.*')
                ->where('users.user_id', $userId)
                ->get();

            if ($administrator->isEmpty()) {
                return $this->error(null, self::ADMINISTRATOR_NOT_FOUND, 404);
            }

            return $this->success($administrator, 'Administrator');
        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }


    /**
     * Update the specified administrator in storage.
     *
     * @param Request $request
     * @param $userId
     * @return JsonResponse
     */
    public function update(Request $request, $userId): JsonResponse
    {
        try {
            // Get the user given in parameter
            $user = User::find($userId);
            if ($user === null) {
                return $this->error(null, self::USER_NOT_FOUND, 404);
            }

            // Check if the user is an administrator
            if ($user->administrator_id === null) {
                return $this->error(null, self::ADMINISTRATOR_NOT_FOUND, 404);
            }
            $request->validate([
                'first_name' => self::STRINGVALIDATION,
                'last_name' => self::STRINGVALIDATION,
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users')->ignore($user), // Ignore the user given in parameter
                ],
                'superAdmin' => 'boolean'
            ]);
            $userData = $request->only(['first_name', 'last_name', 'email']);

            // Check if the data given in parameter are different from the data in database
            foreach ($userData as $field => $value) {
                if ($user->{$field} !== $value) {
                    $user->{$field} = $value;
                }
            }
            $user->save();

            //Get the administrator profile linked to the user
            $administrator = Administrator::where('administrator_id', $user->administrator_id)->first();

            $dataAdministrator = $request->only(['superAdmin']);
            // Check if the data given in parameter are different from the data in database

            if ($administrator->superAdmin !== $dataAdministrator['superAdmin']) {
                $administrator->superAdmin = $dataAdministrator['superAdmin'];
            }

            $administrator->save();
            $userChanges = $user->getChanges();
            $administratorChanges = $administrator->getChanges();

            if (empty($userChanges) && empty($administratorChanges)) {
                return $this->success($user, "Administrator not updated");
            }
            // Return the updated user
            return $this->success($user, "Administrator updated");


        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Deleting the administrator ( softDelete )
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
                return $this->error(null, self::USER_NOT_FOUND, 404);
            }
            //check if the user is a barathonien
            if ($user->administrator_id === null) {
                return $this->error(null, self::ADMINISTRATOR_NOT_FOUND, 404);
            }
            //check if the user is already deleted
            if ($user->deleted_at !== null) {
                return $this->error(null, "Administrator already deleted", 404);
            }
            //delete the user
            User::where('user_id', $userId)->delete();
            return $this->success(null, "Administrator Deleted");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the administrator
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
                return $this->error(null, self::USER_NOT_FOUND, 404);
            }
            //check if the user is a barathonien
            if ($user->administrator_id === null) {
                return $this->error(null, self::ADMINISTRATOR_NOT_FOUND, 404);
            }
            //check if the user is already restored
            if ($user->deleted_at === null) {
                return $this->error(null, "Administrator already restored", 404);
            }
            User::withTrashed()->where('user_id', $userId)->restore();
            return $this->success(null, "Administrator Restored");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
