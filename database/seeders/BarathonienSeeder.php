<?php

namespace Database\Seeders;

use App\Models\Barathonien;
use Illuminate\Database\Seeder;

class BarathonienSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Barathonien::factory()
            ->count(1)
            ->create();
    }
}
