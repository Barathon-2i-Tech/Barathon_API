<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }


    // test for create a booking
    public function test_barathonien_can_create_book_an_event()
    {
        $structure = [
            "status",
            "message",
            "data" => [
                "booking" => [
                    "user_id",
                    "event_id",
                    "ticket",
                    "booking_id",
                ]
            ]];

        $user = $this->createBarathonienUser();

        $response = $this->actingAs($user)->postJson(route('barathonien.postBooking'), [
            'user_id' => 1,
            'event_id' => 2,
            'ticket' => false,
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->assertOk();

        $response->assertJsonStructure($structure);
    }

    // test for create a booking
    public function test_barathonien_can_delete_book_an_event()
    {
        $structure = [
            "status",
            "message",
            "data"];

        $user = $this->createBarathonienUser();

        $response = $this->actingAs($user)->get(route('pro.deletgetEventandUsereBooking', ["id" => 1]))->assertOk();

        $response->assertJsonStructure($structure);
    }

    // test for Get the event and the user details.
    public function test_barathonien_get_his_event_book()
    {
        $structure = [
            "status",
            "message",
            "data" => [
                "booking" => [
                    "booking_id",
                    "user_id",
                    "event_id",
                    "ticket",
                    "event" => [
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
                        "updated_at"
                    ],
                    "user" => [
                        "user_id",
                        "first_name",
                        "last_name",
                        "email",
                        "email_verified_at",
                        "avatar",
                        "owner_id",
                        "barathonien_id",
                        "administrator_id",
                        "employee_id",
                        "deleted_at",
                        "created_at",
                        "updated_at"
                    ],
                ],
            ],
        ];

        $user = $this->createOwnerUser();

        $response = $this->actingAs($user)->get(route('pro.getEventandUser', ["idEvent" => 1,"id" => 1]))->assertOk();

        $response->assertJsonStructure($structure);
    }

}
