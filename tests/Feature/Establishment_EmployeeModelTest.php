<?php


use App\Models\Establishment_Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Establishment_EmployeeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if establishment_employee model exist.
     *
     */
    public function test_establishment_employee_model_exist()
    {
        $establishmentEmployee = Establishment_Employee::factory()->create();
        $this->assertModelExists($establishmentEmployee);
    }


}
