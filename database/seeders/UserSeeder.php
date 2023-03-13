<?php

namespace Database\Seeders;

use App\Models\Administrator;
use App\Models\Barathonien;
use App\Models\Employee;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $barathonienId = Barathonien::all('barathonien_id')->first();
        $administratorId = Administrator::all('administrator_id')->first();
        $ownerId = Owner::all('owner_id')->first();
        $employeeId = Employee::all('employee_id')->first();
        $avatar = 'https://picsum.photos/180';

        $datas = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'barathonien@mail.fr',
                'password' => Hash::make('azertyuiop'),
                'avatar' => $avatar,
                'owner_id' => null,
                'barathonien_id' => $barathonienId->barathonien_id,
                'administrator_id' => null,
                'employee_id' => null,
            ],
            [
                'last_name' => 'Musk',
                'first_name' => 'Elon',
                'email' => 'admin@mail.fr',
                'password' => Hash::make('azertyuiop'),
                'avatar' => $avatar,
                'owner_id' => null,
                'barathonien_id' => null,
                'administrator_id' => $administratorId->administrator_id,
                'employee_id' => null,
            ],
            [
                'last_name' => 'Rothschild',
                'first_name' => 'Benjamin',
                'email' => 'owner@mail.fr',
                'password' => Hash::make('azertyuiop'),
                'avatar' => $avatar,
                'owner_id' => $ownerId->owner_id,
                'barathonien_id' => null,
                'administrator_id' => null,
                'employee_id' => null,
            ],
            [
                'last_name' => 'Doe',
                'first_name' => 'Jane',
                'email' => 'employee@mail.fr',
                'password' => Hash::make('azertyuiop'),
                'avatar' => $avatar,
                'owner_id' => null,
                'barathonien_id' => null,
                'administrator_id' => null,
                'employee_id' => $employeeId->employee_id,
            ],
            [
                'last_name' => 'Smith',
                'first_name' => 'John',
                'email' => 'j.smith@mail.fr',
                'password' => Hash::make('azertyuiop'),
                'avatar' => $avatar,
                'owner_id' => null,
                'barathonien_id' => null,
                'administrator_id' => null,
                'employee_id' => 2,
            ],

        ];

        foreach ($datas as $data) {
            User::create($data);
        }
    }
}
