<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            StatusSeeder::class,
            AddressSeeder::class,
            CategorySeeder::class,
            EmployeeSeeder::class,
            BarathonienSeeder::class,
            AdministratorSeeder::class,
            OwnerSeeder::class,
            UserSeeder::class,
            EstablishmentSeeder::class,
            CategoryEstablishmentSeeder::class,
            EstablishmentEmployeeSeeder::class,
            EventSeeder::class
        ]);
    }
}
