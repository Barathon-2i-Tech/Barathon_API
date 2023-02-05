<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Establishment;
use App\Models\Event;
use App\Models\Address;
use App\Models\User;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Eloquent\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Event $event
     * @return Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Event $event
     * @return Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Event $event
     * @return Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Event $event
     * @return Response
     */
    public function destroy(Event $event)
    {
        //
    }

    /**
     * Get the 4 first event to display on the home page
     *
     * @param $id
     * @return JsonResponse
     */
    public function getEventsByUserCity($id){
        // Get the user
        $user = User::find($id);

        // Get the user city
        if($user->barathonien_id == null){
            return $this->error("error", "the User is not a barathonien", 500);
        }else{
            $city = $user->barathonien->city;
        }

        // Get all establishment id in the city
        $establishments = Establishment::all()->where("city", "=", $city)->modelKeys();

        // Get 4th first event from the establishments by date now
        $date_now = date("Y-m-j H:i:s");

        $allEvents = Event::where('start_event', '>=', $date_now)->whereIn('establishment_id', $establishments)->skip(0)->take(4)->get();

        // Return all events
        return $this->success([
            'event' => $allEvents,
        ]);

    }

    /**
     * Get all events booking by the user
     *
     * @param $id
     * @return JsonResponse
     */
    public function getEventsBookingByUser($id){

        $user = User::find($id);

        //Check if the user is a barathonien
        if($user->barathonien_id == null){
            return $this->error("error", "the User is not a barathonien", 500);
        }
        // Get the event booking by user
        $bookings = Booking::with('event')->where('user_id', '=', $user->user_id)->get()->groupBy(function ($val){
            return Carbon::parse($val->event->start_event)->format('d-m-Y');
        });

        return $this->success([
            'bookings' => $bookings,
        ]);

    }

    /**
     * Get an event by the user's choice
     *
     * @param $idevent, $iduser
     * @return JsonResponse
     */
    public function getEventByUserChoice($idevent, $iduser){

        $user = User::find($iduser);

        //Check if the user is a barathonien
        if($user->barathonien_id == null){
            return $this->error("error", "the User is not a barathonien", 500);
        }

        // Get the event booking by user
        $booking = Booking::where('user_id', '=', $user->user_id)->Where('event_id', '=', $idevent)->get();

        // get the event with his establishment
        $event = Event::with('establishments')->where('event_id', '=', $idevent)->get();

        //get the address of event
        $address = Address::find($event[0]->establishments->address_id);

        $event[0]->establishments->address = $address;

        return $this->success([
            'booking' => $booking,
            'event' => $event,
        ]);

    }
}
