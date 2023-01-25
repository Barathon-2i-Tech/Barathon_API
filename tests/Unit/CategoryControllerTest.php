<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;


    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_get_top_ten_categories()
    {

        $structure = [
            "status",
            "message",
            "data" => [
                "categories"
            ]];

        $user = $this->createBarathonienUser();
        $response = $this->actingAs($user)->get(route('barathonien.topCateg'))
        ->assertOk();

       $response->assertJsonStructure($structure);

    }
}
