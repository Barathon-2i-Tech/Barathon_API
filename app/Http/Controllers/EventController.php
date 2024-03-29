<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Booking;
use App\Models\Establishment;
use App\Models\Event;
use App\Models\Status;
use App\Models\User;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class EventController extends Controller
{
    use HttpResponses;

    private const NOT_BARATHONIEN = 'the User is not a barathonien';
    private const API_GOUV = "https://api-adresse.data.gouv.fr/search/";
    private const EVENT_NOT_FOUND = "Event not found";

    private const UNAUTHORIZED_ACTION = "This action is unauthorized.";

    /**
     * Display all event by establishment ID.
     */
    public function getEventsByEstablishmentId(Request $request, int $establishmentId): JsonResponse
    {
        // Check if the current authenticated user is the owner of the establishment
        $user = $request->user();
        $establishment = Establishment::find($establishmentId);

        if ($user->owner_id !== $establishment->owner_id) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }


        $events = DB::table('events')
            ->join('establishments', 'events.establishment_id', '=', 'establishments.establishment_id')
            ->join('status', 'events.status_id', '=', 'status.status_id')
            ->where('establishments.establishment_id', $establishmentId)
            ->where('events.deleted_at', null)
            ->select('events.*', 'establishments.*', 'status.status_id', 'status.comment')
            ->get();

        if ($events->isEmpty()) {
            return $this->error(null, 'No event found', 404);
        }
        return $this->success($events, 'Events List');
    }

    /**
     * Display the specified event.
     */
    public function show(Request $request, int $eventId): JsonResponse
    {
        // Check if the current authenticated user is the owner of the establishment
        $user = $request->user();

        // Get the specific event from the establishment
        $event = Event::select('events.*', 'establishments.*')
            ->join('establishments', 'establishments.establishment_id', '=', 'events.establishment_id')
            ->where('events.event_id', '=', $eventId)
            ->first();

        if ($user->owner_id !== $event->establishment->owner_id) {
            return $this->error(null, "Unauthorized", 401);
        }


        // If the event is not found
        if (!$event) {
            return $this->error(null, self::EVENT_NOT_FOUND, 404);
        }
        // Return the event
        return $this->success($event, "Event");
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $establishment = Establishment::find($request->input('establishment_id'));

        if ($user->owner_id !== $establishment->owner_id) {
            return $this->error(null, "Unauthorized", 401);
        }

        $request->validate([
            'event_name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_event' => 'required|date',
            'end_event' => 'required|date',
            'price' => 'nullable|numeric',
            'capacity' => 'nullable|integer',
            'establishment_id' => 'required|integer',
            'user_id' => 'required|integer',
        ],[
            'event_name.required' => 'The event name is required',
            'event_name.string' => 'The event name must be a string',
            'event_name.max' => 'The event name must not exceed 255 characters',
            'description.required' => 'The event description is required',
            'description.string' => 'The event description must be a string',
            'start_event.required' => 'The event start date is required',
            'start_event.date' => 'The event start date must be a date',
            'end_event.required' => 'The event end date is required',
            'end_event.date' => 'The event end date must be a date',
            'price.numeric' => 'The event price must be a number',
            'capacity.integer' => 'The event capacity must be an integer',
            'establishment_id.required' => 'The establishment id is required',
            'establishment_id.integer' => 'The establishment id must be an integer',
            'user_id.required' => 'The user id is required',
            'user_id.integer' => 'The user id must be an integer',
        ]);

        // Validate that the start and end event dates are not in the past
        if (Carbon::parse($request->input('start_event'))->isPast() || Carbon::parse($request->input('end_event'))->isPast()) {
            return $this->error(null, "The event can not take place in the past", 400);
        }

        $eventPending = Status::where('comment->code', 'EVENT_PENDING')->first();

        $eventPosterPath = null;

        if ($request->hasFile('poster')) {
            $eventPosterPath = $request->file('poster')->storePublicly('posters', 'public');
            // Ajout du chemin complet
            $eventPosterPath = config('app.url') . Storage::url($eventPosterPath);
        }


        $event = Event::create([
            'event_name' => $request->input('event_name'),
            'description' => $request->input('description'),
            'start_event' => $request->input('start_event'),
            'end_event' => $request->input('end_event'),
            'poster' => $eventPosterPath,
            'price' => $request->input('price'),
            'capacity' => $request->input('capacity'),
            'establishment_id' => $request->input('establishment_id'),
            'user_id' => $request->input('user_id'),
            'status_id' => $eventPending->status_id,
            'event_update_id' => null
        ]);


        $event->save();

        return $this->success([
            'event' => $event
        ], "event created", 201);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $eventId): JsonResponse
    {
        $user = $request->user();
        $establishment = Establishment::findOrFail($request->input('establishment_id'));

        $event = Event::findOrFail($eventId);

        // Ensure that the user trying to modify the event is the same user who created the event
        if ($user->owner_id !== $establishment->owner_id) {
            return $this->error(null, "Unauthorized", 401);
        }

        $request->validate([
            'event_name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_event' => 'required|date',
            'end_event' => 'required|date',
            'price' => 'nullable|numeric',
            'capacity' => 'nullable|integer',
            'establishment_id' => 'required|integer',
            'user_id' => 'required|integer',
            'event_update_id' => 'nullable|integer',
        ], [
            'event_name.required' => 'The event name is required',
            'event_name.string' => 'The event name must be a string',
            'event_name.max' => 'The event name must not exceed 255 characters',
            'description.required' => 'The event description is required',
            'description.string' => 'The event description must be a string',
            'start_event.required' => 'The event start date is required',
            'start_event.date' => 'The event start date must be a date',
            'end_event.required' => 'The event end date is required',
            'end_event.date' => 'The event end date must be a date',
            'price.numeric' => 'The event price must be a number',
            'capacity.integer' => 'The event capacity must be an integer',
            'establishment_id.required' => 'The establishment id is required',
            'establishment_id.integer' => 'The establishment id must be an integer',
            'user_id.required' => 'The user id is required',
            'user_id.integer' => 'The user id must be an integer',
            'event_update_id.integer' => 'The event update id must be an integer',
        ]);

        // Validate that the start and end event dates are not in the past
        if (Carbon::parse($request->input('start_event'))->isPast() || Carbon::parse($request->input('end_event'))->isPast()) {
            return $this->error(null, "The event can not take place in the past", 400);
        }

        // Handle poster file upload if a new poster is present in the request
        if ($request->hasFile('poster')) {
            $request->validate([
                'poster' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $posterPath = $request->file('poster')->store('posters', 'public');
            // add path in db
            $posterPath = config('app.url') . Storage::url($posterPath);
        } else {
            $posterPath = $event->poster;
        }

        $eventPending = Status::where('comment->code', 'EVENT_PENDING')->first();

        $updatedEvent = new Event([
            'event_name' => $request->input('event_name'),
            'description' => $request->input('description'),
            'start_event' => $request->input('start_event'),
            'end_event' => $request->input('end_event'),
            'poster' => $posterPath,
            'price' => $request->input('price'),
            'capacity' => $request->input('capacity'),
            'establishment_id' => $request->input('establishment_id'),
            'status_id' => $eventPending->status_id,
            'user_id' => $request->input('user_id'),
            'event_update_id' => $event->event_id,
        ]);

        $updatedEvent->save();
        $event->delete();

        return $this->success([
            "event" => $updatedEvent
        ], "Event Updated", 200);
    }


    /**
     * Remove the specified event from database.
     */
    public function destroy(Request $request, int $eventId): JsonResponse
    {
        $user = $request->user();

        $event = Event::withTrashed()->where('event_id', $eventId)->first();

        if ($event === null) {
            return $this->error(null, self::EVENT_NOT_FOUND, 404);
        }

        // Check if the current authenticated user is the owner of the establishment or an administrator
        if (!($user->owner_id === $event->establishment->owner_id || $user->administrator_id !== null)) {
            return $this->error(null, "Unauthorized", 401);
        }

        if ($event->deleted_at !== null) {
            return $this->error(null, "Event already deleted", 404);
        }

        $event->delete();
        return $this->success(null, "Event Deleted successfully");
    }

    /**
     * Restore the specified event from database.
     */
    public function restore(int $eventId): JsonResponse
    {
        $user = Auth::user();

        $event = Event::withTrashed()->where('event_id', $eventId)->first();

        if ($event === null) {
            return $this->error(null, self::EVENT_NOT_FOUND, 404);
        }
        if ($event->deleted_at === null) {
            return $this->error(null, "Event already restored", 404);
        }

        if ($user->administrator_id === null) {
            return $this->error(null, "Unauthorized", 401);
        }

        $event->restore();
        return $this->success(null, "Event Restored successfully");

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

        $dateNow = date('Y-m-j H:i:s');

        $establishments = Establishment::with('address')->get();

        //For each establishment take all events and add location with API GOUV for location
        for ($i=0; $i < count($establishments); $i++) {
            $events = Event::where(
                    [['start_event', '>=', $dateNow], ['establishment_id', '=', $establishments[$i]->establishment_id]]
                )
                ->get();
            
            $client = new Client();
            $res = $client->get(self::API_GOUV . "?q=" . $establishments[$i]->address->address . ", " . $establishments[$i]->address->postal_code . ", " . $establishments[$i]->address->city . "&type=housenumber&autocomplete=1");

            if ($res->getStatusCode() == 200) {
                $establishments[$i]->latitude = json_decode($res->getBody())->features[0]->geometry->coordinates[1];
                $establishments[$i]->longitude = json_decode($res->getBody())->features[0]->geometry->coordinates[0];
            }

            $establishments[$i]->events = $events;
            
        }

        $dateOneWeek = strtotime($dateNow);
        $dateOneWeek = strtotime("+7 day", $dateOneWeek);
        $dateOneWeek = date('Y-m-j H:i:s', $dateOneWeek);

        $allEvents = Event::where([['start_event', '>=', $dateNow], ['start_event', '<=', $dateOneWeek]])->get();

        //For each events add location with API GOUV for location
        for ($i=0; $i < count($allEvents); $i++) {
            $establishment = Establishment::with('address')->find($allEvents[$i]->establishment_id)->first();
            $client = new Client();
            $res = $client->get(self::API_GOUV . "?q=" . $establishment->address->address . ", " . $establishment->address->postal_code . ", " . $establishment->address->city . "&type=housenumber&autocomplete=1");

            if ($res->getStatusCode() == 200) {

                $allEvents[$i]->latitude = json_decode($res->getBody())->features[0]->geometry->coordinates[1];
                $allEvents[$i]->longitude = json_decode($res->getBody())->features[0]->geometry->coordinates[0];
            }

        }

        // Return all establishments
        return $this->success([
            'establishments' => $establishments,
            'events' => $allEvents
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
        $event = Event::with('establishment')->where('event_id', '=', $idEvent)->get();

        //get the address of event
        $address = Address::find($event[0]->establishment->address_id);

        $event[0]->establishment->address = $address;

        return $this->success([
            'booking' => $booking,
            'event' => $event,
        ]);
    }
    //************************* administrator part *************************//

    /**
     * Display a listing of all events
     */
    public function getEventList(): JsonResponse
    {
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $events = DB::table("events")
            ->join("establishments", "events.establishment_id", "=", "establishments.establishment_id")
            ->join("status", "events.status_id", "=", "status.status_id")
            ->where('events.deleted_at', '=', null)
            ->select("events.*", "establishments.trade_name", "status.comment")
            ->orderBy('events.start_event', 'asc')
            ->get();
        if ($events->isEmpty()) {
            return $this->error(null, 'No events found', 404);
        }

        return $this->success($events, 'Event List');
    }

    /**
     * Display the specified event with history.
     */
    public function showEventWithHistory(int $eventId): JsonResponse
    {
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $event = Event::leftJoin('establishments', 'events.establishment_id', '=', 'establishments.establishment_id')
            ->where('events.event_id', '=', $eventId)
            ->orderByRaw('COALESCE(events.updated_at, events.created_at) DESC')
            ->select('events.*', 'establishments.trade_name')
            ->first();

        if (!$event) {
            return $this->error(null, self::EVENT_NOT_FOUND, 404);
        }

        $eventHistory = array($event);

        while ($event->event_update_id != null) {
            $event = Event::withTrashed()->find($event->event_update_id);
            $eventHistory[] = $event;
        }
        return $this->success($eventHistory, "Event found");
    }

    /**
     * Get how many events need to be validated
     */
    public function getEventsToValidate(): JsonResponse
    {
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $eventToValidate = Event::where('status_id', 9)->count();
        return $this->success($eventToValidate, 'Event to validate');
    }

    /**
     * Validate the event
     */
    public function validateEvent(int $eventId, int $statusCode): jsonResponse
    {
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $event = Event::find($eventId);

        if (!$event) {
            return $this->error(null, "Event not found", 404);
        }

        if ($event->status_id === $statusCode) {
            return $this->error(null, 'Event with same status', 409);
        }

        $event->status_id = $statusCode;
        $event->save();

        return $this->success(null, 'Status updated');
    }
}
