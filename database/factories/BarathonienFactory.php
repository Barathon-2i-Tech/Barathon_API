<?php

namespace Database\Factories;

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
        return [
            'birthday' => fake(),
            'address'=>fake()->address,
            'postal_code'=>fake()->postcode(),
            'city'=>fake()->city(),
            'avatar' => "https://picsum.photos/180"
        ];
    }
}
