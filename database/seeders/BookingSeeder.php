<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Booking::create(["user_id" => 1, "event_id" => 1, "isFav" => false]);

        Booking::create(["user_id" => 1, "event_id" => 3, "isFav" => false]);

        Booking::create(["user_id" => 1, "event_id" => 5, "isFav" => false]);

        Booking::create(["user_id" => 1, "event_id" => 6, "isFav" => false]);

        Booking::create(["user_id" => 1, "event_id" => 7, "isFav" => false]);

    }
}
