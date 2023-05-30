<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;


    /**
     * A Test for check if we can get events by establishment ID
     */
    public function test_get_events_by_establishment_id()
    {
        $establishmentId = 1; // Replace with a valid establishment ID

        $structure = [
            "status",
            "message",
            "data" => [
                "*" => [
                    "event_id",
                    "event_name",
                    "description",
                    "start_event",
                    "end_event",
                    "poster",
                    "price",
                    "capacity",
                    "establishment_id",
                    "status_id",
                    "user_id",
                    "deleted_at",
                    "event_update_id",
                    "created_at",
                    "updated_at",
                    "trade_name",
                    "siret",
                    "address_id",
                    "logo",
                    "phone",
                    "email",
                    "website",
                    "opening",
                    "owner_id",
                    "comment",
                ]
            ]
        ];

        $owner = $this->createOwnerUser();

        $response = $this->actingAs($owner)->get(route('pro.eventsByEstablishmentId', ["establishmentId" => $establishmentId]))
            ->assertOk();

        $response->assertJsonStructure($structure);
    }

    /**
     * A Test for check if we can show a specific event
     */
    public function test_show_specific_event()
    {
        $establishmentId = 1; // Replace with a valid establishment ID
        $eventId = 1; // Replace with a valid event ID

        $structure = [
            "status",
            "message",
            "data" => [
                "event_name",
                "description",
                "start_event",
                "end_event",
                "poster",
                "price",
                "capacity",
                "establishment_id",
                "status_id",
                "user_id",
                "deleted_at",
                "event_update_id",
                "created_at",
                "updated_at",
                "trade_name",
                "siret",
                "address_id",
                "logo",
                "phone",
                "email",
                "website",
                "opening",
                "owner_id"
            ]
        ];

        $owner = $this->createOwnerUser();

        $response = $this->actingAs($owner)->get(route('event.show', ["establishmentId" => $establishmentId, "eventId" => $eventId]))
            ->assertOk();

        $response->assertJsonStructure($structure);
    }

    /**
     * A Test for check if we can store an event
     */
    public function test_store_event()
    {
        // Prepare the event data
        $eventData = [
            'event_name' => 'Test Event',
            'description' => 'Test Event Description',
            'start_event' => '2023-06-01 18:00:00',
            'end_event' => '2023-06-01 22:00:00',
            'price' => 20,
            'capacity' => 100,
            'establishment_id' => 1, // Replace with a valid establishment ID
            'user_id' => 1, // Replace with a valid user ID
        ];

        $structure = [
            "status",
            "message",
            "data" => [
                "event"
            ]
        ];

        $owner = $this->createOwnerUser();

        $response = $this->actingAs($owner)->post(route('pro.postEvents'), $eventData)
            ->assertStatus(201);

        $response->assertJsonStructure($structure);
    }

/**
 * A Test for check if we can update an event
 */
public function test_update_event()
{
    $establishmentId = 1; // Replace with a valid establishment ID
    $eventId = 1; // Replace with a valid event ID

    // Prepare the updated event data
    $updatedEventData = [
        'event_name' => 'Updated Test Event',
        'description' => 'Updated Test Event Description',
        'start_event' => '2023-06-01 18:00:00',
        'end_event' => '2023-06-01 22:00:00',
        'price' => 25,
        'capacity' => 150,
        'establishment_id' => 1, // Replace with a valid establishment ID
        'user_id' => 1, // Replace with a valid user ID
    ];

    $structure = [
        "status",
        "message",
        "data" => [
            "event"
        ]
    ];

    $owner = $this->createOwnerUser();

    $response = $this->actingAs($owner)->put(route('pro.putEvent', ["establishmentId" => $establishmentId, "eventId" => $eventId]), $updatedEventData)
        ->assertOk();

    $response->assertJsonStructure($structure);
}

/**
 * A Test for check if we can delete an event
 */
public function test_delete_event()
{
    $eventId = 1; // Replace with a valid event ID

    $owner = $this->createOwnerUser();

    $response = $this->actingAs($owner)->delete(route('pro.event.delete', ["eventId" => $eventId]))
        ->assertOk();
        
    $response->assertJsonStructure([
        "status",
        "message"
    ]);
}

    /**
     * A Test for check if we can have the events with the same city than the user in parametter
     *
     */
    public function test_get_events_user_by_city()
    {

        $structure = [
            "status",
            "message",
            "data" => [
                "event"
            ]];

        $user = $this->createBarathonienUser();

        $response = $this->actingAs($user)->get(route('barathonien.eventsByUserCity', ["id" => $user->user_id]))
            ->assertOk();

        $response->assertJsonStructure($structure);

    }

    /**
     * A Test for check if a user other than an Barathonien can execute this route
     *
     */
    public function test_get_events_user_by_city_check_if_not_barathonien()
    {

        $structure = [
            "status",
            "message",
            "data"];

        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->get(route('barathonien.eventsByUserCity', ["id" => $user->user_id]))
            ->assertStatus(500);

        $response->assertJsonStructure($structure);

    }


    /**
     * A Test for check if we can have the events books by the user in parametter
     *
     */
    public function test_get_events_book_by_user()
    {

        $structure = [
            "status",
            "message",
            "data" => [
                "bookings"
            ]];

        $user = $this->createBarathonienUser();

        $response = $this->actingAs($user)->get(route('barathonien.eventsBookByUser', ["id" => $user->user_id]))
            ->assertOk();

        $response->assertJsonStructure($structure);

    }

    /**
     * A Test for check if a user other than an Barathonien can execute this route
     *
     */
    public function test_get_events_book_by_user_check_if_not_barathonien()
    {

        $structure = [
            "status",
            "message",
            "data"];

        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->get(route('barathonien.eventsBookByUser', ["id" => $user->user_id]))
            ->assertStatus(500);

        $response->assertJsonStructure($structure);

    }

    /**
     * A Test for check if we can have the event by the user's choice
     *
     */
    public function test_get_event_by_user_choice()
    {

        $structure = [
            "status",
            "message",
            "data" => [
                "booking",
                "event"
            ]];

        $user = $this->createBarathonienUser();

        $response = $this->actingAs($user)->get(route('barathonien.eventByUserChoice', ["idevent" => 1, "iduser" => $user->user_id]))
            ->assertOk();

        $response->assertJsonStructure($structure);

    }

    /**
     * A Test for check if a user other than an Barathonien can execute this route
     *
     */
    public function test_get_event_by_user_choice_check_if_not_barathonien()
    {

        $structure = [
            "status",
            "message",
            "data"];

        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->get(route('barathonien.eventByUserChoice', ["idevent" => 1, "iduser" => $user->user_id]))
            ->assertStatus(500);

        $response->assertJsonStructure($structure);

    }

}
