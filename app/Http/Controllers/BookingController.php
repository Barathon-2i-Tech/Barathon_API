<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

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

    /**
     * Get the event and the user details.
     */
    public function getEventandUser($idevent, $idBarathonien): JsonResponse
    {
        $booking = Booking::
            with('event')
            ->with('user')
            ->where([["user_id", '=', $idBarathonien], ['event_id', '=', $idevent]])
            ->first();

        return $this->success([
            'booking' => $booking,
        ], 'booking send');
    }

    /**
     * valide Ticket.
     */
    public function valideTicket(Request $request, $id): JsonResponse
    {
        $book = Booking::where("booking_id", "=", $id)->first();
        $establishment = Event::find($book->event_id)->with("establishments")->first()->establishments;
        
        $request->validate([
            'code' => 'required|string',
        ]);

        if (Crypt::decryptString($establishment->validation_code) == $request->code) {
            $book->ticket = true;
            $book->save();

            return $this->success([], 'booking validate');

        }else {
            return $this->error(null, "wrong code", 403);
        }

    }
}
