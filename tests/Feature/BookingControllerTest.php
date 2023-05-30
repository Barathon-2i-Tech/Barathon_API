<?php

namespace Tests\Feature;

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
                    "isFav",
                    "booking_id",
                ]
            ]];

        $user = $this->createBarathonienUser();

        $response = $this->actingAs($user)->postJson(route('barathonien.postBooking'), [
            'user_id' => 1,
            'event_id' => 2,
            'isFav' => false,
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

        $response = $this->actingAs($user)->delete(route('barathonien.deleteBooking', ["id" => 1]))->assertOk();

        $response->assertJsonStructure($structure);
    }
}
