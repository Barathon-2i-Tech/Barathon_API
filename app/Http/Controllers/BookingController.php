<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'event_id' => 'required|integer',
            'isFav' => 'required|boolean',
        ]);

        $book = Booking::create([
            'user_id' => $request->user_id,
            'event_id' => $request->event_id,
            'isFav' => $request->isFav,
        ]);

        return $this->success([
            'booking' => $book,
        ], 'Booking created');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $book = Booking::find($id);

        if ($book == null) {
            return $this->error('error', "Booking doesn't exist", 500);
        } else {
            $book->delete();

            return $this->success([], 'Booking deleted');
        }
    }
}
