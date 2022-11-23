<?php

namespace Database\Seeders;


use App\Models\Employee;
use App\Models\Establishment;
use App\Models\Establishment_Employee;
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
        $establishment = Establishment::where('trade_name','Fait Foif')->first();


        $datas = [
            [
                'employee_id' => $employee->employee_id,
                'establishment_id' => $establishment->establishment_id,
            ]
        ];
        Establishment_Employee::create($datas[0]);
    }
}
