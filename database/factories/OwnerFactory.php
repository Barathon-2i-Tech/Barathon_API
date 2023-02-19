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
        $ownerValid = Status::where('comment->code', 'OWNER_VALID')->first();

        return [
            'siren' => fake()->siren(),
            'kbis' => 'chemin/kbis.pdf',
            'status_id' => $ownerValid->status_id
        ];
    }
}
