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
        ], "Barathonien Created");
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
            $barathonien = DB::table('users')
                ->join('barathoniens', 'users.barathonien_id', '=', 'barathoniens.barathonien_id')
                ->join('addresses', 'barathoniens.address_id', '=', 'addresses.address_id')
                ->select('users.*', 'barathoniens.*', 'addresses.*')
                ->where('users.user_id', '=', $user_id)
                ->first();

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

            // Return the updated user and address
            return $this->success([$user, $address], "Barathonien Updated");

        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Deleting the barathonien
     *
     * @param $user_id
     * @return JsonResponse
     */
    public function destroy($user_id): JsonResponse
    {
        try {
            User::where('user_id', $user_id)->delete();
            return $this->success("", "Barathonien Deleted");
        } catch (Exception $error) {
            Log::error($error);
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
