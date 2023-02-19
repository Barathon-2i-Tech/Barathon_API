<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Barathonien;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Barathonien>
 */
class BarathonienFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $addressId = Address::all('address_id')->first();

        return [
            'birthday' => fake()->dateTimeBetween('-30 years', '-18 years'),
            'address_id'=>$addressId->address_id
        ];
    }
}
