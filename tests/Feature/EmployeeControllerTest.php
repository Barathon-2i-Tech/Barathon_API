<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
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
            "hiring_date",
            "dismissal_date",
            "establishment_name",
        ]]
    ];

    /**
     * A test to get all employees
     *
     * @return void
     */
    public function test_get_all_employees(): void
    {
        $structure = self::STRUCTURE;

        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('employee.list'))
            ->assertOk();
        $response->assertJsonStructure($structure);
        $response->assertJson(['message' => 'Employees List']);

    }


    /**
     * A test to get an employee by id
     *
     * @return void
     */
    public function test_get_an_employee_by_id(): void
    {

        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $response = $this->actingAs($administrator)->get(route('employee.show', $employee->user_id))
            ->assertOk();

        $response->assertJsonStructure(self::STRUCTURE);
        $response->assertJson(['message' => 'Employee']);
    }

    /**
     * A test to get a 404 error when employee not found
     *
     * @return void
     */
    public function test_get_404_error_employee_not_found(): void
    {
        $professional = $this->createOwnerUser();
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('employee.show', $professional->user_id))
            ->assertNotFound();

        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'No employee found']);
    }

    /**
     * A test creating an employee with non-existing establishment.
     *
     * @return void
     */
    public function test_create_employee_with_non_existing_establishment(): void
    {
        $adminUser = $this->createAdminUser();

        $response = $this->actingAs($adminUser)->post(route('user.register.employee'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'hiring_date' => '2023-02-24',
            'establishment_id' => 999,
        ]);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Establishment not found']);
    }

    /**
     * A test to update an employee
     *
     * @return void
     */
    public function test_to_update_an_employee(): void
    {
        $employee = $this->createEmployeeUser();
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->put(route('employee.update', $employee->user_id), [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.fr',
            'phone' => '0101010101'])
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Employee Updated']);
    }

    /**
     * A test to check if the update is really on an employee
     *
     * @return void
     */
    public function test_to_check_on_update_if_really_an_employee(): void
    {
        $administrator = $this->createAdminUser();
        $professional = $this->createOwnerUser();
        $response = $this->actingAs($administrator)->put(route('employee.update', $professional->user_id), [
            'first_name' => 'test',
            'last_name' => 'test',
            'email' => 'test@test.fr',
            'phone' => '0101010101'])
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'No employee found']);
    }

    /**
     * A test to check if the employee is updated
     *
     * @return void
     */
    public function test_to_check_if_employee_is_updated(): void
    {
        $employee = $this->createEmployeeUser();
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->put(route('employee.update', $employee->user_id), [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'barathon.m2i+employe@gmail.com',
            ])
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Employee not updated']);
    }

    /**
     * A test to check the validation on updated
     *
     * @return void
     */
    public function test_to_check_validation_on_updated(): void
    {
        $employee = $this->createEmployeeUser();
        $administrator = $this->createAdminUser();

        $this->actingAs($administrator)->put(route('employee.update', $employee->user_id), [])
            ->assertStatus(302);
    }

    /**
     * A test to delete an employee
     *
     * @return void
     */
    public function test_to_delete_an_employee(): void
    {
        $employee = $this->createEmployeeUser();
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->delete(route('employee.delete', $employee->user_id))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Employee Deleted']);
    }

    /**
     * A test to delete an employee who doesn't exist
     *
     * @return void
     */
    public function test_to_delete_an_employee_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $professional = $this->createOwnerUser();
        $response = $this->actingAs($administrator)->delete(route('employee.delete', $professional->user_id))
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
        $response = $this->actingAs($administrator)->delete(route('employee.delete', 450))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to check if an employee is already deleted
     *
     * @return void
     */
    public function test_to_check_if_employee_already_deleted(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $employee->delete();
        $response = $this->actingAs($administrator)->delete(route('employee.delete', $employee->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Employee already deleted']);
    }

    /**
     * A test to restore a user who doesn't exist
     *
     * @return void
     */
    public function test_to_restore_a_user_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('employee.restore', 450))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to restore an employee who doesn't exist
     *
     * @return void
     */
    public function test_to_restore_an_employee_who_does_not_exist(): void
    {
        $administrator = $this->createAdminUser();
        $barathonien = $this->createBarathonienUser();
        $response = $this->actingAs($administrator)->get(route('employee.restore', $barathonien->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'User not found']);
    }

    /**
     * A test to check if an employee is already restored
     *
     * @return void
     */
    public function test_to_check_if_employee_already_restored(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();

        $response = $this->actingAs($administrator)->get(route('employee.restore', $employee->user_id))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Employee already restored']);
    }

    /**
     * A test to restore an employee
     *
     * @return void
     */
    public function test_to_restore_a_employee(): void
    {
        $administrator = $this->createAdminUser();
        $employee = $this->createEmployeeUser();
        $employee->delete();

        $response = $this->actingAs($administrator)->get(route('employee.restore', $employee->user_id))
            ->assertOk();
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Employee Restored']);
    }
}
