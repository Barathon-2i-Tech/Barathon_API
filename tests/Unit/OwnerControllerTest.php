<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;


class OwnerControllerTest extends TestCase
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
                "siren",
                "kbis",
                "phone",
                "status_id",
                "comment"]]
    ];

    /**
     * A test to get all owners
     *
     * @return void
     */
    public function test_get_all_owners(): void
    {
        $structure = self::STRUCTURE;

        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('owner.list'))
            ->assertOk();
        $response->assertJsonStructure($structure);
        $response->assertJson(['message' => 'Owner List']);
    }

    /**
     * A test to get a 404 no found on empty response all owners
     *
     * @return void
     */
    public function test_get_all_owners_with_empty_response(): void
    {

        $administrator = $this->createAdminUser();
        DB::table('users')->whereNotNull('owner_id')->delete();
        $response = $this->actingAs($administrator)->get(route('owner.list'))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'No owners found']);
    }

    /**
     * A test to get a owner by id
     *
     * @return void
     */
    public function test_get_a_owner_by_id(): void
    {
        $owner = $this->createOwnerUser();
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('owner.show', $owner->user_id))
            ->assertOk();

        $response->assertJsonStructure(self::STRUCTURE);
        $response->assertJson(['message' => 'Owner Details']);
    }

    /**
     * A test to get a 404 error when owner not found
     *
     * @return void
     */
    public function test_get_404_error_owner_not_found(): void
    {
        $barathonien = $this->createBarathonienUser();
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('owner.show', $barathonien->user_id))
            ->assertNotFound();

        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Owner not found']);
    }

    /**
     * A test to update a owner
     *
     * @return void
     */
    public function test_to_update_a_owner(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();
        $response = $this->actingAs($administrator)->put(route('owner.update', $owner->user_id), [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.fr',
            'phone' => '0102030405',])
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner Updated']);
    }

    /**
     * A test to check if the update is really on a owner
     *
     * @return void
     */
    public function test_to_check_on_update_if_really_a_owner(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $response = $this->actingAs($administrator)->put(route('owner.update', $employee->user_id), [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.fr',
            'phone' => '0102030405',])
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner not found']);
    }

    /**
     * A test to check if the owner is updated with the same information as before
     *
     * @return void
     */
    public function test_to_check_if_owner_is_updated(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();

        $response = $this->actingAs($administrator)->put(route('owner.update', $owner->user_id), [
            'first_name' => 'Benjamin',
            'last_name' => 'Rothschild',
            'email' => 'owner@mail.fr',
            'phone' => '0606060606',])
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner not updated']);
    }

    /**
     * A test to check the validation on updated
     *
     * @return void
     */
    public function test_to_check_validation_on_updated(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();

        $response = $this->actingAs($administrator)->put(route('owner.update', $owner->user_id), [])
            ->assertStatus(500);
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['status' => 'An error has occurred...']);
    }

    /**
     * A test to delete a owner
     *
     * @return void
     */
    public function test_to_delete_a_owner(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();

        $response = $this->actingAs($administrator)->delete(route('owner.delete', $owner->user_id))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner Deleted']);
    }

    /**
     * A test to delete a owner who doesn't exist
     *
     * @return void
     */
    public function test_to_delete_a_owner_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $response = $this->actingAs($administrator)->delete(route('owner.delete', $employee->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner not found']);
    }

    /**
     * A test to delete a user who doesn't exist
     *
     * @return void
     */
    public function test_to_delete_a_user_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->delete(route('owner.delete', 450))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to check if a owner is already deleted
     *
     * @return void
     */
    public function test_to_check_if_owner_already_deleted(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();
        $owner->delete();
        $response = $this->actingAs($administrator)->delete(route('owner.delete', $owner->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner already deleted']);
    }

    /**
     * A test to restore a user who doesn't exist
     *
     * @return void
     */
    public function test_to_restore_a_user_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('owner.restore', 450))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to restore a owner who doesn't exist
     *
     * @return void
     */
    public function test_to_restore_a_owner_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $barathonien = $this->createBarathonienUser();
        $response = $this->actingAs($administrator)->get(route('owner.restore', $barathonien->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner not found']);
    }

    /**
     * A test to check if a owner is already restored
     *
     * @return void
     */
    public function test_to_check_if_owner_already_restored(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();

        $response = $this->actingAs($administrator)->get(route('owner.restore', $owner->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner already restored']);
    }

    /**
     * A test to restore a owner
     *
     * @return void
     */
    public function test_to_restore_a_owner(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();
        $owner->delete();

        $response = $this->actingAs($administrator)->get(route('owner.restore', $owner->user_id))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner Restored']);
    }
}
