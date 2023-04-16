<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;


    /**
     * A test to check if we can have the top 10 categories used by events.
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
