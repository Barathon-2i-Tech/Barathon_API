<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Models\Administrator;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ApiAuthController extends Controller
{
    use HttpResponses;

    private const TOKEN_NAME = 'API Token';


    public function login(LoginUserRequest $request): JsonResponse
    {
        $request->validated();

        if (!auth()->attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user->administrator_id !== null) {
            $admin = Administrator::where('administrator_id', $user->administrator_id)->first();

            return $this->success([
                'userLogged' => $user,
                'superAdmin' => $admin->superAdmin,
                'token' => $user->createToken(self::TOKEN_NAME)->plainTextToken,
            ]);
        }

        return $this->success([
            'userLogged' => $user,
            'token' => $user->createToken(self::TOKEN_NAME)->plainTextToken,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'avatar' => 'https://picsum.photos/180',
        ]);

        return $this->success([
            'userLogged' => $user,
            'token' => $user->createToken(self::TOKEN_NAME)->plainTextToken,
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();
        $message = 'You have successfully been logged out and your tokens has been removed';

        return $this->success([], $message);
    }

     /**
     * Update owner password.
     */
    public function updateUserPassword(Request $request, int $userId): JsonResponse
    {
        // Check if the user exists
        $user = User::find($userId);
        if ($user === null) {
            return $this->error(null, 'User not found', 404);
        }

        // Validate the request data
        $request->validate([
            'password' => 'required|string',
            'new_password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'password.required' => 'L\'ancien mot de passe est obligatoire.',
            'new_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'new_password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        // Check if the old password is correct
        if (!Hash::check($request->input('password'), $user->password)) {
            return $this->error(null, 'L\'ancien mot de passe est incorrect.', 401);
        }

        // Update the user's password
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return $this->success(null, 'Mot de passe mis à jour');
    }
}
