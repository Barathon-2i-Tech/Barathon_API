<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarathonienControllerTest extends TestCase
{
    use RefreshDatabase;


    /**
     * A test to get all barathoniens
     *
     * @return void
     */
    public function test_get_all_barathoniens()
    {
        $structure = [
            "status",
            "message",
            "data" => [
                [
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
                    "updated_at",
                    "barathonien" => [
                        "barathonien_id",
                        "birthday",
                        "address_id",
                        "address" => [
                            "address_id",
                            "address",
                            "postal_code",
                            "city"]
                    ]
                ]]
        ];

        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->get(route('barathonien.list'))
            ->dump()
            ->assertOk();
        $response->assertJsonStructure($structure);

    }

    /**
     * A test to get a 404 no found on empty response all barathoniens
     *
     * @return void
     */
    public function test_get_all_barathoniens_with_empty_response()
    {

        $user = $this->createAdminUser();
        $barathoniens = new Collection();



        $response = $this->actingAs($user)->get(route('barathonien.list'));
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => 'An error has occurred...',
            'message' => "No barathonien found",
            'data' => null
        ]);

    }
}
