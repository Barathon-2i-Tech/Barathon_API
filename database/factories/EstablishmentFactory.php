<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Establishment;
use App\Models\Owner;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $owner_id = Owner::all('owner_id')->first();
        $ESTABL_VALID = Status::where('comment->code', 'ESTABL_VALID')->first();
        $address = Address::all('address_id')->first();

        return [
            'trade_name'=>fake()->company,
            'siret'=>fake()->siret(),
            'address_id'=>$address->address_id,
            'logo'=> fake()->imageUrl(180,180,"Establishment logo",false,),
            'phone'=>fake()->phoneNumber(),
            'email'=>fake()->companyEmail(),
            'website'=>fake()->url(),
            'opening'=> [
                'Lundi' => 'fermer',
                'Mardi' => '17h00 - 01h00',
                'Mercredi' => '17h00 - 01h00',
                'Jeudi' => '17h00 - 01h00',
                'Vendredi' => '17h00 - 01h00',
                'Samedi' => '17h00 - 01h00',
                'Dimanche' => '17h00 - 01h00',
            ],
            'owner_id'=>$owner_id->owner_id,
            'status_id'=>$ESTABL_VALID->status_id,
        ];
    }




}
