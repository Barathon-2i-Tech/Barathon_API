<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BarathonienControllerTest extends TestCase
{
    use RefreshDatabase;

    private const  STRUCTURE = [
        "status",
        "message",
        "data" => [
            ["user_id",
                "first_name",
                "last_name",
                "email",
                "email_verified_at",
                "password",
                "avatar",
                "owner_id",
                "barathonien_id",
                "administrator_id",
                "employee_id",
                "remember_token",
                "deleted_at",
                "created_at",
                "updated_at",
                "birthday",
                "address_id",
                "address",
                "postal_code",
                "city"]]
    ];

    /**
     * A test to get all barathoniens
     *
     * @return void
     */
    public function test_get_all_barathoniens(): void
    {
        $structure = self::STRUCTURE;

        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('barathonien.list'))
            ->assertOk();
        $response->assertJsonStructure($structure);
        $response->assertJson(['message' => 'Barathonien List']);

    }

    /**
     * A test to get a barathonien by id
     *
     * @return void
     */
    public function test_get_a_barathonien_by_id(): void
    {
        $barathonien = $this->createBarathonienUser();
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('barathonien.show', $barathonien->user_id))
            ->assertOk();

        $response->assertJsonStructure(self::STRUCTURE);
    }

    /**
     * A test to get a 404 error when barathonien not found
     *
     * @return void
     */
    public function test_get_404_error_barathonien_not_found(): void
    {
        $professional = $this->createOwnerUser();
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('barathonien.show', $professional->user_id))
            ->assertNotFound();

        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Barathonien not found']);

    }

    /**
     * A test to update a barathonien
     *
     * @return void
     */
    public function test_to_update_a_barathonien(): void
    {
        $administrator = $this->createAdminUser();
        $barathonien = $this->createBarathonienUser();
        $response = $this->actingAs($administrator)->put(route('barathonien.update', $barathonien->user_id), [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.fr',
            'address' => 'address test',
            'postal_code' => '75000',
            'city' => 'Paris'])
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Barathonien updated']);
    }

    /**
     * A test to check if the update is really on a barathonien
     *
     * @return void
     */
    public function test_to_check_on_update_if_really_a_barathonien(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $response = $this->actingAs($administrator)->put(route('barathonien.update', $employee->user_id), [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.fr',
            'address' => 'address test',
            'postal_code' => '75000',
            'city' => 'Paris'])
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Barathonien not found']);
    }

    /**
     * A test to check if the update is really on a user
     *
     * @return void
     */
    public function test_to_check_on_update_if_really_a_user(): void
    {
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->put(route('barathonien.update', 450), [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.fr',
            'address' => 'address test',
            'postal_code' => '75000',
            'city' => 'Paris'])
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to check if the barathonien is updated with the same information as before
     *
     * @return void
     */
    public function test_to_check_if_barathonien_is_updated(): void
    {
        $administrator = $this->createAdminUser();
        $barathonien = $this->createBarathonienUser();

        $response = $this->actingAs($administrator)->put(route('barathonien.update', $barathonien->user_id), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'barathonien@mail.fr',
            'address' => '20 boulevard eugene deruelle',
            'postal_code' => '69003',
            'city' => 'Lyon'
        ])
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Barathonien not updated']);
    }

    /**
     * A test to check the validation on updated
     *
     * @return void
     */
    public function test_to_check_validation_on_updated(): void
    {
        $administrator = $this->createAdminUser();
        $barathonien = $this->createBarathonienUser();

        $this->actingAs($administrator)->put(route('barathonien.update', $barathonien->user_id), [])
            ->assertStatus(302);
    }

    /**
     * A test to delete a barathonien
     *
     * @return void
     */
    public function test_to_delete_a_barathonien(): void
    {
        $administrator = $this->createAdminUser();
        $barathonien = $this->createBarathonienUser();

        $response = $this->actingAs($administrator)->delete(route('barathonien.delete', $barathonien->user_id))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Barathonien Deleted']);
    }

    /**
     * A test to delete a barathonien who doesn't exist
     *
     * @return void
     */
    public function test_to_delete_a_barathonien_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $response = $this->actingAs($administrator)->delete(route('barathonien.delete', $employee->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to delete a user who doesn't exist
     *
     * @return void
     */
    public function test_to_delete_a_user_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->delete(route('barathonien.delete', 450))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to check if a barathonien is already deleted
     *
     * @return void
     */
    public function test_to_check_if_barathonien_already_deleted(): void
    {
        $administrator = $this->createAdminUser();
        $barathonien = $this->createBarathonienUser();
        $barathonien->delete();
        $response = $this->actingAs($administrator)->delete(route('barathonien.delete', $barathonien->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Barathonien already deleted']);
    }

    /**
     * A test to restore a user who doesn't exist
     *
     * @return void
     */
    public function test_to_restore_a_user_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('barathonien.restore', 450))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to restore a barathonien who doesn't exist
     *
     * @return void
     */
    public function test_to_restore_a_barathonien_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $response = $this->actingAs($administrator)->get(route('barathonien.restore', $employee->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to check if a barathonien is already restored
     *
     * @return void
     */
    public function test_to_check_if_barathonien_already_restored(): void
    {
        $administrator = $this->createAdminUser();
        $barathonien = $this->createBarathonienUser();

        $response = $this->actingAs($administrator)->get(route('barathonien.restore', $barathonien->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Barathonien already restored']);
    }

    /**
     * A test to restore a barathonien
     *
     * @return void
     */
    public function test_to_restore_a_barathonien(): void
    {
        $administrator = $this->createAdminUser();
        $barathonien = $this->createBarathonienUser();
        $barathonien->delete();

        $response = $this->actingAs($administrator)->get(route('barathonien.restore', $barathonien->user_id))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Barathonien Restored']);
    }
}
