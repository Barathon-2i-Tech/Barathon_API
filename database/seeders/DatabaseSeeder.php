<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            // Comment the line to not run the seeder
            StatusSeeder::class,
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
