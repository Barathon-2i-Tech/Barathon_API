<?php


namespace Tests\Feature;

use App\Models\Event;
use App\Models\Owner;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
            'email' => 'barathon.m2i+owner@gmail.com'])
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
            ->assertStatus(302);
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

    /**
     * A test to change the status to validate of a owner
     *
     */
    public function test_to_change_the_status_to_validate_of_a_owner(): void
    {
        $administrator = $this->createAdminUser();
        $ownerPending = Status::where('comment->code', 'OWNER_PENDING')->first();
        $owner = Owner::create([
            'siren' => '123456789',
            'kbis' => 'kbis.pdf',
            'phone' => '0606060606',
            'company_name' => 'My company',
            'status_id' => $ownerPending->status_id,
        ]);

        $response = $this->actingAs($administrator)->put(route('pro.validation', [$owner->owner_id, 1]))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Status updated']);
    }

    /**
     * A test to change the status of a owner on a non existing owner
     *
     */
    public function test_to_change_the_status_of_a_owner_on_non_existing_owner(): void
    {
        $administrator = $this->createAdminUser();
        $fakeOwnerId = 1000;

        $response = $this->actingAs($administrator)->put(route('pro.validation', [$fakeOwnerId, 1]))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner not found']);
    }

    /**
     * A test to change the status of a owner on a validated owner
     */
    public function test_to_change_the_status_of_a_owner_on_a_validated_owner(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();

        $response = $this->actingAs($administrator)->put(route('pro.validation', [$owner->owner_id, 1]))
            ->assertStatus(409);

        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner with same status']);
    }

    /**
     * A test to change throw a error when validated the owner
     *
     */
    public function test_to_throw_a_error_when_validated_owner(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();

        $this->actingAs($administrator)->put(route('pro.validation', [$owner->owner_id, 124]))
            ->assertStatus(500);
    }

    /**
     * A test to get how many owners need to be validated
     *
     */
    public function test_to_get_how_many_owners_need_to_be_validated(): void
    {
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('admin.pro-to-validate'))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner to validate']);
    }
}
