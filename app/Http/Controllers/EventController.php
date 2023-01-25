<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Booking;
use App\Models\Establishment;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Eloquent\Support\Collection;
use Carbon\Carbon;

class EventController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }

    public function getEventByUserCity($id){
        // Get the user
        $user = User::find($id);

        // Get the user city
        if($user->barathoniens == null){        
            return $this->error("error", "the User is not a barathonien", 500);
        }else{
            $city = $user->barathoniens->city;
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

    public function getEventBookingByUser($id){
        // Get the event booking by user
        $user = User::find($id);
        
        if($user->barathoniens == null){
            return $this->error("error", "the User is not a barathonien", 500);
        }

        $bookings = Booking::with('event')->where('user_id', '=', $user->user_id)->get()->groupBy(function ($val){
            return Carbon::parse($val->event->start_event)->format('d-m-Y');
        });

        return $this->success([
            'bookings' => $bookings,
        ]);

    }
}
