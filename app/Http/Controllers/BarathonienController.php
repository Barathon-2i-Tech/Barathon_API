<?php

namespace App\Http\Controllers;

use App\Models\Barathonien;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'birthday' => 'required|date',
            'adress' => 'required|string|max:255',
            'postal_code' => 'required|string|max:5',
            'city' => 'required|string|max:255',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $barathonien = Barathonien::create([
            'birthday' => $request->birthday,
            'address' => $request->adress,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
        ]);

        $user->barathonien_id = $barathonien->barathonien_id;
        $user->save();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
