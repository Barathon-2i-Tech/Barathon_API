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
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class BarathonienController extends Controller
{
    use HttpResponses;

    private const STRINGVALIDATION = 'required|string|max:255';
    private const BARATHONIENNOTFOUND = 'Barathonien not found';
    private const USERNOTFOUND = 'User not found';

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $today = new Carbon;
            $minor = $today->subYears(18);

            $request->validate([
                'first_name' => self::STRINGVALIDATION,
                'last_name' => self::STRINGVALIDATION,
                'email' => 'required|string|email|unique:users',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'birthday' => 'required|date|before:' . $minor,
                'address' => 'min:5|required|string|max:255',
                'postal_code' => 'required|string|size:5',
                'city' => self::STRINGVALIDATION,
            ], [
                'first_name.required' => 'Le prénom est obligatoire.',
                'last_name.required' => 'Le nom est obligatoire.',
                'email.required' => "L'adresse e-mail est obligatoire.",
                'email.email' => "L'adresse e-mail n'est pas valide.",
                'email.unique' => "L'adresse e-mail est déjà utilisée.",
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                'birthday.before' => 'Vous devez avoir plus de 18 ans pour vous inscrire',
                'birthday.required' => 'La date de naissance est obligatoire.',
                'birthday.date' => 'La date de naissance n\'est pas valide.',
                'address.required' => 'L\'adresse est obligatoire.',
                'address.min' => 'L\'adresse doit faire au moins 5 caractères.',
                'address.max' => 'L\'adresse doit faire au maximum 255 caractères.',
                'postal_code.required' => 'Le code postal est obligatoire.',
                'postal_code.size' => 'Le code postal doit faire 5 caractères.',
                'city.required' => 'La ville est obligatoire.',
                'city.max' => 'La ville doit faire au maximum 255 caractères.',
            ]);

            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'avatar' => 'https://picsum.photos/180',
            ]);

            $address = Address::create([
                'address' => $request->input('address'),
                'postal_code' => $request->input('postal_code'),
                'city' => $request->input('city'),
            ]);

            $barathonien = Barathonien::create([
                'birthday' => $request->input('birthday'),
                'address_id' => $address->address_id,
            ]);

            $user->barathonien_id = $barathonien->barathonien_id;
            $user->save();

            return $this->success([
                'userLogged' => $user,
                'token' => $user->createToken('API Token')->plainTextToken,
            ], 'Barathonien Created', 201);
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 422);
        }
    }

    /**
     * Display the specified barathonien.
     */
    public function show( $userId): JsonResponse
    {
        try {
            $barathonien = DB::table('users')
                ->join('barathoniens', 'users.barathonien_id', '=', 'barathoniens.barathonien_id')
                ->join('addresses', 'barathoniens.address_id', '=', 'addresses.address_id')
                ->select('users.*', 'barathoniens.*', 'addresses.*')
                ->where('users.user_id', $userId)
                ->get();

            if ($barathonien->isEmpty()) {
                return $this->error(null, self::BARATHONIENNOTFOUND, 404);
            }

            return $this->success($barathonien, 'Barathonien');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Update the specified barathonien in database.
     */
    public function update(Request $request,  $userId): JsonResponse
    {
        try {
            // Get the user given in parameter
            $user = User::find($userId);
            if ($user === null) {
                return $this->error(null, self::USERNOTFOUND, 404);
            }

            // Check if the user is a barathonien
            if ($user->barathonien_id === null) {
                return $this->error(null, self::BARATHONIENNOTFOUND, 404);
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
                'address' => 'min:5|required|string|max:255',
                'postal_code' => 'required|string|size:5',
                'city' => self::STRINGVALIDATION,
            ], [
                'first_name.required' => 'Le prénom est obligatoire.',
                'last_name.required' => 'Le nom est obligatoire.',
                'email.required' => "L'adresse e-mail est obligatoire.",
                'email.email' => "L'adresse e-mail n'est pas valide.",
                'email.unique' => "L'adresse e-mail est déjà utilisée.",
                'address.required' => 'L\'adresse est obligatoire.',
                'address.min' => 'L\'adresse doit faire au moins 5 caractères.',
                'address.max' => 'L\'adresse doit faire au maximum 255 caractères.',
                'postal_code.required' => 'Le code postal est obligatoire.',
                'postal_code.size' => 'Le code postal doit faire 5 caractères.',
                'city.required' => 'La ville est obligatoire.',
                'city.max' => 'La ville doit faire au maximum 255 caractères.',
            ]);

            $user->fill($request->only(['first_name', 'last_name', 'email']));
            $user->save();

            $barathonien = Barathonien::where('barathonien_id', $user->barathonien_id)->first();

            $address = Address::where('address_id', $barathonien->address_id)->first();
            $address->fill($request->only(['address', 'postal_code', 'city']));
            $address->save();

            $userChanges = $user->getChanges();
            $addressChanges = $address->getChanges();

            if (empty($userChanges) && empty($addressChanges)) {
                return $this->success([$user, $address], 'Barathonien not updated');
            }

            // Return the updated user and address
            return $this->success([$user, $address], 'Barathonien updated');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Deleting the barathonien ( softDelete )
     */
    public function destroy( $userId): JsonResponse
    {
        try {
            $user = User::withTrashed()
                ->where('user_id', $userId)
                ->whereNotNull('barathonien_id')
                ->first();

            if (!$user) {
                return $this->error(null, self::USERNOTFOUND, 404);
            }

            if ($user->deleted_at) {
                return $this->error(null, 'Barathonien already deleted', 404);
            }

            $user->delete();

            return $this->success(null, 'Barathonien Deleted');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Restoring the barathonien
     */
    public function restore( $userId): JsonResponse
    {
        try {
            $user = User::withTrashed()
                ->where('user_id', $userId)
                ->whereNotNull('barathonien_id')
                ->first();

            if (!$user) {
                return $this->error(null, self::USERNOTFOUND, 404);
            }

            if (!$user->deleted_at) {
                return $this->error(null, 'Barathonien already restored', 404);
            }

            $user->restore();

            return $this->success(null, 'Barathonien Restored');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /**
     * Display all barathonien.
     */
    public function getBarathonienList(): JsonResponse
    {
        try {
            $barathoniens = DB::table('users')
                ->join('barathoniens', 'users.barathonien_id', '=', 'barathoniens.barathonien_id')
                ->join('addresses', 'barathoniens.address_id', '=', 'addresses.address_id')
                ->select('users.*', 'barathoniens.*', 'addresses.*')
                ->get();

            if ($barathoniens->isEmpty()) {
                return $this->error(null, self::BARATHONIENNOTFOUND, 404);
            }

            return $this->success($barathoniens, 'Barathonien List');
        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }
}
