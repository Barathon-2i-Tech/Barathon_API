<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Booking;
use App\Models\Establishment;
use App\Models\Event;
use App\Models\Owner;
use App\Models\Status;
use App\Models\User;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class EventController extends Controller
{
    use HttpResponses;

    private const NOT_BARATHONIEN = 'the User is not a barathonien';
    private const EVENT_NOT_FOUND = "Event not found";

    /**
     * Display all event by establishment ID.
     */
    public function getEventsByEstablishmentId(Request $request, int $establishmentId): JsonResponse
    {
        // Check if the current authenticated user is the owner of the establishment
        $user = $request->user();
        $establishment = Establishment::find($establishmentId);

        if ($user->owner_id !== $establishment->owner_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
    public function show(Request $request, int $establishmentId, int $eventId): JsonResponse
    {
        // Check if the current authenticated user is the owner of the establishment
        $user = $request->user();
        $establishment = Establishment::find($establishmentId);

        if ($user->owner_id !== $establishment->owner_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the specific event from the establishment
        $event = Event::select('events.*', 'establishments.*')
            ->join('establishments', 'establishments.establishment_id', '=', 'events.establishment_id')
            ->where('events.establishment_id', '=', $establishmentId)
            ->where('events.event_id', '=', $eventId)
            ->first();

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
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'event_name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_event' => 'required|date',
            'end_event' => 'required|date',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price' => 'nullable|numeric',
            'capacity' => 'nullable|integer',
            'establishment_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);
        // Validate that the start and end event dates are not in the past
        if (Carbon::parse($request->input('start_event'))->isPast() || Carbon::parse($request->input('end_event'))->isPast()) {
            return $this->error(null, "L'événement ne peut avoir lieu dans le passé", 400);
        }

        $eventPending = Status::where('comment->code', 'EVENT_PENDING')->first();

        $eventPosterPath = null;

        if ($request->hasFile('poster')) {
            $eventPosterPath = $request->file('poster')->storePublicly('posters', 'public');
            // Ajout du chemin complet
            $eventPosterPath = env('APP_URL') . Storage::url($eventPosterPath);
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
    public function update(Request $request, int $establishmentId, int $eventId): JsonResponse
    {
        $user = $request->user();
        $establishment = Establishment::find($request->input('establishment_id'));

        $event = Event::where('establishment_id', $establishmentId)
            ->findOrFail($eventId);

        // Ensure that the user trying to modify the event is the same user who created the event
        if ($user->owner_id !== $establishment->owner_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
            'event_update_id' => 'nullable|string',
        ]);

        // Validate that the start and end event dates are not in the past
        if (Carbon::parse($request->input('start_event'))->isPast() || Carbon::parse($request->input('end_event'))->isPast()) {
            return $this->error(null, "L'événement ne peut avoir lieu dans le passé", 400);
        }

        // Handle poster file upload if a new poster is present in the request
        if ($request->hasFile('poster')) {
            $request->validate([
                'poster' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $posterPath = $request->file('poster')->store('posters', 'public');
            // add path in db
            $posterPath = env('APP_URL') . Storage::url($posterPath);
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
        if ($event->deleted_at !== null) {
            return $this->error(null, "Event already deleted", 404);
        }

        //get the owner of the establishment
        $owner = User::join('establishments', 'users.owner_id', '=', 'establishments.owner_id')
            ->join('events', 'establishments.establishment_id', '=', 'events.establishment_id')
            ->where('events.event_id', $eventId)
            ->first(['users.*']);

        // if the autenticated user is not the owner of the establishment
            if ($user->user_id !== $owner->user_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

        $event->delete();
        return $this->success(null, "Event Deleted successfully");
    }

    /**
     * Restore the specified event from database.
     */
    public function restore(int $eventId): JsonResponse
    {
        $event = Event::withTrashed()->where('event_id', $eventId)->first();

        if ($event === null) {
            return $this->error(null, self::EVENT_NOT_FOUND, 404);
        }
        if ($event->deleted_at === null) {
            return $this->error(null, "Event already restored", 404);
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
    //************************* administrator part *************************//

    /**
     * Display a listing of all events
     */
    public function getEventList(): JsonResponse
    {
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
        $eventToValidate = Event::where('status_id', 9)->count();
        return $this->success($eventToValidate, 'Event to validate');
    }

    /**
     * Validate the event
     */
    public function validateEvent(int $eventId, int $statusCode): jsonResponse
    {
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
