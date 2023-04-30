<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Booking;
use App\Models\Establishment;
use App\Models\Event;
use App\Models\User;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class EventController extends Controller
{
    use HttpResponses;

    private const NOT_BARATHONIEN = 'the User is not a barathonien';
    private const API_GOUV = "https://api-adresse.data.gouv.fr/search/";

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
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Event $event)
    {
        //
    }

    /**
     * Get the 4 first event to display on the home page
     */
    public function getEventsByUserCity($id): JsonResponse
    {
        // Get the user
        $user = User::find($id);

        // Get the user city
        if ($user->barathonien_id == null) {
            return $this->error('error', self::NOT_BARATHONIEN, 500);
        } else {
            $city = $user->barathonien->city;
        }

        // Get all establishment id in the city
        $establishments = Establishment::all()->where('city', '=', $city)->modelKeys();

        // Get 4th first event from the establishments by date now
        $dateNow = date('Y-m-j H:i:s');

        $allEvents = Event::where('start_event', '>=', $dateNow)
            ->whereIn('establishment_id', $establishments)
            ->skip(0)
            ->take(4)
            ->get();

        // Return all events
        return $this->success([
            'event' => $allEvents,
        ]);
    }

    /**
     * Get the events with location
     */
    public function getEventsLocation(): JsonResponse
    {
        // Get all establishment id in the city
        $establishments = Establishment::with('address')->get();

        // Get 4th first event from the establishments by date now
        $dateNow = date('Y-m-j H:i:s');

        $allEvents = Event::where('start_event', '>=', $dateNow)->get();

        for ($i=0; $i < count($allEvents); $i++) { 
            $establishment = Establishment::with('address')->find($allEvents[$i]->establishment_id)->first();
            $client = new Client();
            $res = $client->get(self::API_GOUV . "?q=" . $establishment->address->address . ", " . $establishment->address->postal_code . ", " . $establishment->address->city . "&type=housenumber&autocomplete=1");

            if($res->getStatusCode() == 200){

                $allEvents[$i]->latitude = json_decode($res->getBody())->features[0]->geometry->coordinates[1];
                $allEvents[$i]->longitude = json_decode($res->getBody())->features[0]->geometry->coordinates[0];
            }
            
        }

        // Return all events
        return $this->success([
            'event' => $allEvents,
        ]);
    }

    /**
     * Get all events booking by the user
     */
    public function getEventsBookingByUser($id): JsonResponse
    {
        $user = User::find($id);

        //Check if the user is a barathonien
        if ($user->barathonien_id == null) {
            return $this->error('error', self::NOT_BARATHONIEN, 500);
        }
        // Get the event booking by user
        $bookings = Booking::with('event')->where('user_id', '=', $user->user_id)->get()->groupBy(function ($val) {
            return Carbon::parse($val->event->start_event)->format('d-m-Y');
        });

        return $this->success([
            'bookings' => $bookings,
        ]);
    }

    /**
     * Get an event by the user's choice
     */
    public function getEventByUserChoice($idEvent, $idUser): JsonResponse
    {
        $user = User::find($idUser);

        //Check if the user is a barathonien
        if ($user->barathonien_id == null) {
            return $this->error('error', self::NOT_BARATHONIEN, 500);
        }

        // Get the event booking by user
        $booking = Booking::where('user_id', '=', $user->user_id)->Where('event_id', '=', $idEvent)->get();

        // get the event with his establishment
        $event = Event::with('establishments')->where('event_id', '=', $idEvent)->get();

        //get the address of event
        $address = Address::find($event[0]->establishments->address_id);

        $event[0]->establishments->address = $address;

        return $this->success([
            'booking' => $booking,
            'event' => $event,
        ]);
    }
}
