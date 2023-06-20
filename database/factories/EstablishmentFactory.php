<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Establishment;
use App\Models\Owner;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

/**
 * @extends Factory<Establishment>
 */
class EstablishmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ownerId = Owner::all('owner_id')->first();
        $establValid = Status::where('comment->code', 'ESTABL_VALID')->first();
        $address = Address::all('address_id')->first();
        $code = 0000;

        return [
            'trade_name' => fake()->company,
            'siret' => fake()->siret(),
            'address_id' => 3,
            'logo' => fake()->imageUrl(180, 180, 'Establishment logo', false),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'website' => fake()->url(),
            'opening' => [
                'Lundi' => 'fermer',
                'Mardi' => '17h00 - 01h00',
                'Mercredi' => '17h00 - 01h00',
                'Jeudi' => '17h00 - 01h00',
                'Vendredi' => '17h00 - 01h00',
                'Samedi' => '17h00 - 01h00',
                'Dimanche' => '17h00 - 01h00',
            ],
            'validation_code' => Crypt::encryptString($code),
            'owner_id' => $ownerId->owner_id,
            'status_id' => $establValid->status_id,
        ];
    }
}
