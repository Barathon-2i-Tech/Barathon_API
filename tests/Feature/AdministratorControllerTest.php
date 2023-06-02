<?php

namespace Tests\Unit;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdministratorControllerTest extends TestCase
{
    use RefreshDatabase;

    private const STRUCTURE = [
        "status",
        "message",
        "data" => [[
            "user_id",
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
            "superAdmin",
        ]]
    ];

    /**
     * A test to get all administrators
     *
     * @return void
     */
    public function test_get_all_administrators(): void
    {
        $structure = self::STRUCTURE;

        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('administrator.list'))
            ->assertOk();
        $response->assertJsonStructure($structure);
        $response->assertJson(['message' => 'Administrators List']);

    }

    /**
     * A test to get a 404 no found on empty response all administrators
     *
     * @return void
     */
    public function test_get_all_administrators_with_empty_response(): void
    {

        $administrator = $this->createAdminUser();
        DB::table('users')->whereNotNull('administrator_id')->delete();
        $response = $this->actingAs($administrator)->get(route('administrator.list'))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Administrator not found']);
    }

    /**
     * A test to get an administrator by id
     *
     * @return void
     */
    public function test_get_a_administrator_by_id(): void
    {

        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('administrator.show', $administrator->user_id))
            ->assertOk();

        $response->assertJsonStructure(self::STRUCTURE);
        $response->assertJson(['message' => 'Administrator']);
    }

    /**
     * A test to get an 404 error when administrator not found
     *
     * @return void
     */
    public function test_get_404_error_administrator_not_found(): void
    {
        $professional = $this->createOwnerUser();
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('administrator.show', $professional->user_id))
            ->assertNotFound();

        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Administrator not found']);
    }

    /**
     * A test to get a 500 error on show method
     *
     * @return void
     */
    public function test_get_500_error_administrator_show_method(): void
    {
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('administrator.show', 'error'))
            ->assertStatus(500);

        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['status' => 'An error has occurred...']);
    }

    /**
     * A test to update an administrator
     *
     * @return void
     */
    public function test_to_update_a_administrator(): void
    {
        $user = $this->createAdminUser();
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($user)->put(route('administrator.update', $administrator->user_id), [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.fr',
            'superAdmin' => false])
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Administrator updated']);
    }

    /**
     * A test to check if the update is really on an administrator
     *
     * @return void
     */
    public function test_to_check_on_update_if_really_a_administrator(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $response = $this->actingAs($administrator)->put(route('administrator.update', $employee->user_id), [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.fr',
            'superAdmin' => false])
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Administrator not found']);
    }

    /**
     * A test to check if the administrator is updated with the same information as before
     *
     * @return void
     */
    public function test_to_check_if_administrator_is_updated(): void
    {
        $user = $this->createAdminUser();
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($user)->put(route('administrator.update', $administrator->user_id), [
            'first_name' => 'Elon',
            'last_name' => 'Musk',
            'email' => 'admin@mail.fr',
            'superAdmin' => true])
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Administrator not updated']);
    }

    /**
     * A test to check the validation on updated
     *
     * @return void
     */
    public function test_to_check_validation_on_updated(): void
    {
        $user = $this->createAdminUser();
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($user)->put(route('administrator.update', $administrator->user_id), [])
            ->assertStatus(500);
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['status' => 'An error has occurred...']);
    }

    /**
     * A test to delete an administrator
     *
     * @return void
     */
    public function test_to_delete_a_administrator(): void
    {
        $user = $this->createAdminUser();
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($user)->delete(route('administrator.delete', $administrator->user_id))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Administrator Deleted']);
    }

    /**
     * A test to delete an administrator who doesn't exist
     *
     * @return void
     */
    public function test_to_delete_a_administrator_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $response = $this->actingAs($administrator)->delete(route('administrator.delete', $employee->user_id))
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
        $response = $this->actingAs($administrator)->delete(route('administrator.delete', 450))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to check if an administrator is already deleted
     *
     * @return void
     */
    public function test_to_check_if_administrator_already_deleted(): void
    {
        $user = $this->createAdminUser();
        $administrator = $this->createAdminUser();
        $administrator->delete();
        $response = $this->actingAs($user)->delete(route('administrator.delete', $administrator->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Administrator already deleted']);
    }

    /**
     * A test to restore a user who doesn't exist
     *
     * @return void
     */
    public function test_to_restore_a_user_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('administrator.restore', 450))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to restore an administrator who doesn't exist
     *
     * @return void
     */
    public function test_to_restore_a_administrator_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $response = $this->actingAs($administrator)->get(route('administrator.restore', $employee->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to check if an administrator is already restored
     *
     * @return void
     */
    public function test_to_check_if_administrator_already_restored(): void
    {
        $user = $this->createAdminUser();
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($user)->get(route('administrator.restore', $administrator->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Administrator already restored']);
    }

    /**
     * A test to restore a administrator
     *
     * @return void
     */
    public function test_to_restore_a_administrator(): void
    {
        $user = $this->createAdminUser();
        $administrator = $this->createAdminUser();
        $administrator->delete();

        $response = $this->actingAs($user)->get(route('administrator.restore', $administrator->user_id))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Administrator Restored']);
    }

    /**
     * A test to change the status to validate of a owner
     *
     */
    public function test_to_change_the_status_to_validate_of_a_owner(): void
    {
        $administrator = $this->createAdminUser();
        $owner = Owner::create([
            'siren' => '123456789',
            'kbis' => 'kbis.pdf',
            'phone' => '0606060606',
            'company_name' => 'My company',
            'status_id' => 3,
        ]);

        $response = $this->actingAs($administrator)->put(route('pro.validation', [$owner->owner_id, 1]))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Validation updated']);
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
     * todo : fix this test
     */
    public function test_to_change_the_status_of_a_owner_on_a_validated_owner(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();

        $response = $this->actingAs($administrator)->put(route('pro.validation', [$owner->owner_id, 1]))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Owner already validated']);
    }

    /**
     * A test to change throw a error when validated the owner
     *
     */
    public function test_to_throw_a_error_when_validated_owner(): void
    {
        $administrator = $this->createAdminUser();
        $owner = $this->createOwnerUser();

        $response = $this->actingAs($administrator)->put(route('pro.validation', [$owner->owner_id, 124]))
            ->assertStatus(500);
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
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
