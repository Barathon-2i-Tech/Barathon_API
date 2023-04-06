<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AdministratorController extends Controller
{
    use HttpResponses;

    private const USER_NOT_FOUND = 'User not found';
    private const ADMINISTRATOR_NOT_FOUND = 'Administrator not found';
    private const STRINGVALIDATION = 'required|string|max:255';

    /**
     * Store a newly created administrator in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => self::STRINGVALIDATION,
            'last_name' => self::STRINGVALIDATION,
            'superAdmin' => 'boolean', // Accept 0 or 1 only with postman
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom est obligatoire.',
            'email.required' => "L'adresse e-mail est obligatoire.",
            'email.email' => "L'adresse e-mail n'est pas valide.",
            'email.unique' => "L'adresse e-mail est déjà utilisée.",
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'avatar' => 'https://picsum.photos/180',
        ]);

        $admin = Administrator::create([
            'superAdmin' => $request->input('superAdmin'),
        ]);

        $user->administrator_id = $admin->administrator_id;
        $user->save();

        return $this->success([
            'userLogged' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,
        ], 'Admin Created');
    }

    /**
     * Display the specified administrator.
     */
    public function show(int $userId): JsonResponse
    {
        $administrator = DB::table('users')
            ->join('administrators', 'users.administrator_id', '=', 'administrators.administrator_id')
            ->select('users.*', 'administrators.*')
            ->where('users.user_id', $userId)
            ->get();

        if ($administrator->isEmpty()) {
            return $this->error(null, self::ADMINISTRATOR_NOT_FOUND, 404);
        }

        return $this->success($administrator, 'Administrator');
    }

    /**
     * Update the specified administrator in storage.
     */
    public function update(Request $request, int $userId): JsonResponse
    {
        $user = User::find($userId);
        if ($user === null) {
            return $this->error(null, self::USER_NOT_FOUND, 404);
        }

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
            'superAdmin' => 'boolean',
        ], [
                'first_name.required' => 'Le prénom est obligatoire.',
                'last_name.required' => 'Le nom est obligatoire.',
                'email.required' => "L'adresse e-mail est obligatoire.",
                'email.email' => "L'adresse e-mail n'est pas valide.",
                'email.unique' => "L'adresse e-mail est déjà utilisée.",
            ]
        );

        $user->fill($request->only(['first_name', 'last_name', 'email']));
        $user->save();

        $administrator = Administrator::where('administrator_id', $user->administrator_id)->first();

        $administrator->fill($request->only(['superAdmin']));
        $administrator->save();

        $userChanges = $user->getChanges();
        $administratorChanges = $administrator->getChanges();

        if (empty($userChanges) && empty($administratorChanges)) {
            return $this->success($user, 'Administrator not updated');
        }

        // Return the updated user
        return $this->success($user, 'Administrator updated');
    }

    /**
     * Deleting the administrator ( softDelete )
     */
    public function destroy(int $userId): JsonResponse
    {
        $user = User::withTrashed()
            ->where('user_id', $userId)
            ->whereNotNull('administrator_id')
            ->first();

        if (!$user) {
            return $this->error(null, self::USER_NOT_FOUND, 404);
        }

        if ($user->deleted_at) {
            return $this->error(null, 'Administrator already deleted', 404);
        }

        $user->delete();

        return $this->success(null, 'Administrator Deleted');
    }

    /**
     * Restoring the administrator
     */
    public function restore(int $userId): JsonResponse
    {
        $user = User::withTrashed()
            ->where('user_id', $userId)
            ->whereNotNull('administrator_id')
            ->first();

        if (!$user) {
            return $this->error(null, self::USER_NOT_FOUND, 404);
        }

        if (!$user->deleted_at) {
            return $this->error(null, 'Administrator already restored', 404);
        }

        $user->restore();

        return $this->success(null, 'Administrator Restored');
    }

    /**
     * Display a listing of all administrator.
     */
    public function getAdministratorList(): JsonResponse
    {
        $administrators = DB::table('users')
            ->join('administrators', 'users.administrator_id', '=', 'administrators.administrator_id')
            ->select('users.*', 'administrators.*')
            ->get();

        if ($administrators->isEmpty()) {
            return $this->error(null, self::ADMINISTRATOR_NOT_FOUND, 404);
        }

        return $this->success($administrators, 'Administrators List');
    }
}
