<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    /**
     * A test to get all categories
     *
     * @return void
     */
    public function test_get_all_administrators(): void
    {
        //$structure = self::STRUCTURE;

        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('categories.establishment.all'))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Categories List']);

    }
}
