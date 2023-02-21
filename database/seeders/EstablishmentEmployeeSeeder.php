<?php

namespace Database\Seeders;


use App\Models\Employee;
use App\Models\Establishment;
use App\Models\Establishment_Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class EstablishmentEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $employee = Employee::all('employee_id')->first();
        $estabFaitFoif = Establishment::where('trade_name', 'Fait Foif')->first();
        $estabFantome = Establishment::where('trade_name', 'Le Fantôme de l\'Opéra')->first();
        $employeeSmith = User::where('last_name', 'Smith')->first();

        $datas = [
            [
                'employee_id' => $employee->employee_id,
                'establishment_id' => $estabFaitFoif->establishment_id,
            ],
            [
                'employee_id' => $employeeSmith->employee_id,
                'establishment_id' => $estabFantome->establishment_id,
            ],
        ];
        foreach ($datas as $data) {
            Establishment_Employee::create($data);
        }
    }
}
