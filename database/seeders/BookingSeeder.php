<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Booking::create(['user_id' => 1, 'event_id' => 1, 'ticket' => false]);

        Booking::create(['user_id' => 1, 'event_id' => 3, 'ticket' => false]);

        Booking::create(['user_id' => 1, 'event_id' => 5, 'ticket' => false]);

        Booking::create(['user_id' => 1, 'event_id' => 6, 'ticket' => false]);

        Booking::create(['user_id' => 1, 'event_id' => 7, 'ticket' => false]);
    }
}
