<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use App\Models\Employee;
use App\Models\Owner;
use Illuminate\Http\Request;
use App\Http\Requests\LoginUserRequest;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\Barathonien;

class ApiAuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->validated($request->only(['email', 'password']));

        if(!Auth::attempt($request->only(['email', 'password']))) {
            return $this->error('', 'Credentials do not match', 401);
        }

        $user = User::where('email', $request->email)->first();


        if($user->barathonien_id != null) {
            $barathonienAvatar = Barathonien::where("barathonien_id",$user->barathonien_id )->get('avatar');
        } else {
            $barathonienAvatar = null;
        }

        if($user->owner_id != null) {
            $ownerAvatar = Owner::where("owner_id",$user->owner_id )->get('avatar');
        } else {
            $ownerAvatar = null;
        }

        if($user->employee_id != null) {
            $employeeAvatar = Employee::where("employee_id",$user->employee_id )->get('avatar');
        } else {
            $employeeAvatar = null;
        }

        if($user->administrator_id != null) {
            $adminAvatar = Administrator::where("administrator_id",$user->administrator_id )->get('avatar');
        } else {
            $adminAvatar = null;
        }


        return $this->success([
            'userLogged' => $user,
            'barathonienAvatar' => $barathonienAvatar,
            'ownerAvatar' => $ownerAvatar,
            'employeeAvatar' => $employeeAvatar,
            'adminAvatar' => $adminAvatar,
            'token' => $user->createToken('API Token')->plainTextToken
        ]);
    }

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->success([
            'userLogged' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ]);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        Auth::user()->tokens()->delete();
        $message = "You have successfully been logged out and your tokens has been removed";
        return $this->success([],$message );
    }
}
