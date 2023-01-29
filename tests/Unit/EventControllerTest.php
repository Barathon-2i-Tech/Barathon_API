<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

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
}