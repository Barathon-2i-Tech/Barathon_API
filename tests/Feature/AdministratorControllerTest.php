<?php


namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Status;
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
     * A test to check if the update is really on an real user
     *
     * @return void
     */
    public function test_to_check_on_update_if_really_a_user(): void
    {
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->put(route('administrator.update', 850), [
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
        $response->assertJson(['message' => 'User not found']);
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
            'last_name' => 'Musk',
            'first_name' => 'Elon',
            'email' => 'barathon.m2i+admin@gmail.com'])
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
     */
    public function test_to_check_validation_on_updated(): void
    {
        $administrator = $this->createAdminUser();
        $user = $this->createAdminUser();

        $this->actingAs($administrator)->put(route('administrator.update', $user->user_id), [])
            ->assertStatus(302);
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





}
