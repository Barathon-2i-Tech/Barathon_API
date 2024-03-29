<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if employee model exist.
     *
     */
    public function test_employee_model_exist()
    {
        $employee = Employee::all()->first();
        $this->assertModelExists($employee);
    }

}
