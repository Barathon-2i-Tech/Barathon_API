<?php

namespace Database\Factories;

use App\Models\Establishment;
use App\Models\Establishment_Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Establishment_Employee>
 */
class Establishment_EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $establishment = Establishment::query()->first();
        $employee = User::where('employee_id', '!=', null)->first();

        return [
            'establishment_id' => $establishment->establishment_id,
            'employee_id' => $employee->employee_id,
        ];
    }
}
