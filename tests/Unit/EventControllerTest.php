<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_get_event_user_by_city()
    {

        $structure = [
            "status",
            "message",
            "data" => [
                "event"
            ]];

        $user = $this->createBarathonienUser();
        
       $response = $this->actingAs($user)->get(route('barathonien.eventByUserCity', ["id" => $user->user_id]))
       ->assertOk();

       $response->assertJsonStructure($structure);

    }

    public function test_get_event_user_by_city_check_if_not_barathonien()
    {

        $structure = [
            "status",
            "message",
            "data"];

        $user = $this->createAdminUser();
        
       $response = $this->actingAs($user)->get(route('barathonien.eventByUserCity', ["id" => $user->user_id]))
       ->assertStatus(500);

       $response->assertJsonStructure($structure);

    }

    public function test_get_event_book_by_user()
    {

        $structure = [
            "status",
            "message",
            "data" => [
                "bookings"
            ]];

        $user = $this->createBarathonienUser();
        
       $response = $this->actingAs($user)->get(route('barathonien.eventBookByUser', ["id" => $user->user_id]))
       ->assertOk();

       $response->assertJsonStructure($structure);

    }

    public function test_get_event_book_by_user_check_if_not_barathonien()
    {

        $structure = [
            "status",
            "message",
            "data"];

        $user = $this->createAdminUser();
        
       $response = $this->actingAs($user)->get(route('barathonien.eventBookByUser', ["id" => $user->user_id]))
       ->assertStatus(500);

       $response->assertJsonStructure($structure);

    }
}

