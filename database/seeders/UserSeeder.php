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

        $barathonien_id = Barathonien::all('barathonien_id')->first();
        $administrator_id = Administrator::all('administrator_id')->first();
        $owner_id = Owner::all('owner_id')->first();
        $employee_id = Employee::all('employee_id')->first();



        $datas = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'barathonien@mail.fr',
                'password' => Hash::make('azertyuiop'),
                'avatar' => "https://picsum.photos/180",
                'owner_id' => null,
                'barathonien_id' => $barathonien_id->barathonien_id,
                'administrator_id' => null,
                'employee_id' => null
            ],
            [
                'last_name' => 'Musk',
                'first_name' => 'Elon',
                'email' => 'admin@mail.fr',
                'password' => Hash::make('azertyuiop'),
                'avatar' => "https://picsum.photos/180",
                'owner_id' => null,
                'barathonien_id' => null,
                'administrator_id' => $administrator_id->administrator_id,
                'employee_id' => null
            ],
            [
                'last_name' => 'Rothschild',
                'first_name' => 'Benjamin',
                'email' => 'owner@mail.fr',
                'password' => Hash::make('azertyuiop'),
                'avatar' => "https://picsum.photos/180",
                'owner_id' => $owner_id->owner_id,
                'barathonien_id' => null,
                'administrator_id' => null,
                'employee_id' => null
            ],
            [
                'last_name' => 'Doe',
                'first_name' => 'Jane',
                'email' => 'employee@mail.fr',
                'password' => Hash::make('azertyuiop'),
                'avatar' => "https://picsum.photos/180",
                'owner_id' => null,
                'barathonien_id' => null,
                'administrator_id' => null,
                'employee_id' => $employee_id->employee_id
            ],

        ];

        foreach ($datas as $data) {
            User::create($data);
        }
    }
}
