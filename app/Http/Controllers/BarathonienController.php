<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Barathonien;
use App\Models\User;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class BarathonienController extends Controller
{
    use HttpResponses;

    /**
     * Display all barathonien.
     * @return JsonResponse
     */
    public function getBarathonienList(): JsonResponse
    {
        try {
            $barathoniens = User::with(['barathonien', 'barathonien.address'])
                ->whereHas('barathonien', function ($query) {
                    $query->whereNotNull('barathonien_id');
                })
                ->get();

            if ($barathoniens->isEmpty()) {
                return $this->error(null, "No barathonien found", 404);
            }

            return $this->success($barathoniens, "Barathonien List");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {

            $today = new Carbon();
            $minor = $today->subYears(18);

            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'birthday' => 'required|date|before:' . $minor,
                'address' => 'min:5|required|string|max:255',
                'postal_code' => 'required|string|size:5',
                'city' => 'required|string|max:255',
            ]);

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'avatar' => "https://picsum.photos/180",
            ]);

            $address = Address::create([
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'city' => $request->city
            ]);

            $barathonien = Barathonien::create([
                'birthday' => $request->birthday,
                'address_id' => $address->address_id
            ]);


            $user->barathonien_id = $barathonien->barathonien_id;
            $user->save();

            return $this->success([
                'userLogged' => $user,
                'token' => $user->createToken('API Token')->plainTextToken
            ], "Barathonien Created", 201);

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Display the specified barathonien.
     *
     * @param $user_id
     * @return JsonResponse
     */
    public function show($user_id): JsonResponse
    {
        try {
            $barathonien = User::with(['barathonien', 'barathonien.address'])
                ->whereHas('barathonien', function ($query) use ($user_id) {
                    $query->where('users.user_id', $user_id);
                })
                ->get();

            if ($barathonien->isEmpty())
                return $this->error(null, "Barathonien not found", 404);

            return $this->success($barathonien, "Barathonien");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Update the specified barathonien in database.
     *
     * @param Request $request
     * @param $user_id
     * @return JsonResponse
     */
    public function update(Request $request, $user_id): JsonResponse
    {
        try {
            // Get the user given in parameter
            $user = User::findOrFail($user_id);

            // Check if the user is a barathonien
            if ($user->barathonien_id === null)
                return $this->error(null, "Barathonien not found", 404);

            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    Rule::unique('users')->ignore($user), // Ignore the user given in parameter
                ],
                'address' => 'min:5|required|string|max:255',
                'postal_code' => 'required|string|size:5',
                'city' => 'required|string|max:255',
            ]);

            $dataBarathonien = $request->only(['first_name', 'last_name', 'email']);

            // Check if the data given in parameter are different from the data in database
            foreach ($dataBarathonien as $field => $value) {
                if ($user->{$field} !== $value) {
                    $user->{$field} = $value;
                }
            }
            $user->save();

            // Get the barathonien profile linked to the user
            $barathonien = Barathonien::where('barathonien_id', $user->barathonien_id)->first();

            // Get the address linked to the barathonien profile
            $address = Address::where('address_id', $barathonien->address_id)->first();

            $dataAddress = $request->only(['address', 'postal_code', 'city']);

            // Check if the data given in parameter are different from the data in database
            foreach ($dataAddress as $field => $value) {
                if ($address->{$field} !== $value) {
                    $address->{$field} = $value;
                }
            }
            $address->save();

            $userChanges = $user->getChanges();
            $addressChanges = $address->getChanges();


            // Check if the user has changed
           // if (empty($userChanges) && empty($addressChanges))
             //   return $this->success([$user, $address], "Barathonien not updated");


            // Return the updated user and address
            return $this->success([$user, $address], "Barathonien Updated");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Deleting the barathonien ( softDelete )
     *
     * @param $user_id
     * @return JsonResponse
     */
    public function destroy($user_id): JsonResponse
    {
        try {

            //check if the user exist
            $user = User::withTrashed()->where('user_id', $user_id)->first();
            if ($user === null)
                return $this->error(null, "User not found", 404);

            //check if the user is a barathonien
            if ($user->barathonien_id === null)
                return $this->error(null, "Barathonien not found", 404);

            //check if the user is already deleted
            if ($user->deleted_at !== null)
                return $this->error(null, "Barathonien already deleted", 404);

            //delete the user
            User::where('user_id', $user_id)->delete();
            return $this->success(null, "Barathonien Deleted");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the barathonien
     *
     * @param $user_id
     * @return JsonResponse
     */
    public function restore($user_id): JsonResponse
    {
        try {
            //check if the user exist
            $user = User::withTrashed()->where('user_id', $user_id)->first();
            if ($user === null)
                return $this->error(null, "User not found", 404);

            //check if the user is a barathonien
            if ($user->barathonien_id === null)
                return $this->error(null, "Barathonien not found", 404);

            //check if the user is already restored
            if ($user->deleted_at === null)
                return $this->error(null, "Barathonien already restored", 404);

            User::withTrashed()->where('user_id', $user_id)->restore();
            return $this->success(null, "Barathonien Restored");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
