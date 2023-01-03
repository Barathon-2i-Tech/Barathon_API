<?php

namespace Database\Factories;

use App\Models\Owner;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Owner>
 */
class OwnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $OWNER_VALID = Status::where('comment->code', 'OWNER_VALID')->first();

        return [
            'siren' => fake()->siren(),
            'avatar' => "https://picsum.photos/180",
            'kbis' => 'chemin/kbis.pdf',
            'active' => true,
            'status_id' => $OWNER_VALID->status_id
        ];
    }
}
