<?php

namespace App\Http\Controllers;

use App\Models\Barathonien;
use App\Models\Address;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BarathonienController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $today = new Carbon();
        $minor = $today->subYears(18);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'birthday' => 'required|date|before:'.$minor,
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
     * Display the specified resource.
     *
     * @param  \App\Models\Barathonien  $barathonien
     * @return \Illuminate\Http\Response
     */
    public function show(Barathonien $barathonien)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Barathonien  $barathonien
     * @return \Illuminate\Http\Response
     */
    public function edit(Barathonien $barathonien)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Barathonien  $barathonien
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Barathonien $barathonien)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Barathonien  $barathonien
     * @return \Illuminate\Http\Response
     */
    public function destroy(Barathonien $barathonien)
    {
        //
    }
}
