<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Establishment;
use App\Models\Owner;
use App\Models\Status;
use Illuminate\Database\Seeder;

class EstablishmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ownerId = Owner::all('owner_id')->first();
        $establValid = Status::where('comment->code', 'ESTABL_VALID')->first();
        $address2 = Address::where('address_id', 2)->first();
        $address3 = Address::where('address_id', 3)->first();

        $datas = [
            [
                'trade_name' => 'Fait Foif',
                'siret' => fake()->siret(),
                'address_id' => $address2->address_id,
                'logo' => fake()->imageUrl(180, 180, 'Establishment logo', false),
                'phone' => fake()->phoneNumber(),
                'email' => 'etablissement@mail.fr',
                'website' => 'www.google.fr',
                'opening' => [
                    'Lundi' => 'fermer',
                    'Mardi' => '17h00 - 01h00',
                    'Mercredi' => '17h00 - 01h00',
                    'Jeudi' => '17h00 - 01h00',
                    'Vendredi' => '17h00 - 01h00',
                    'Samedi' => '17h00 - 01h00',
                    'Dimanche' => '17h00 - 01h00',
                ],
                'owner_id' => $ownerId->owner_id,
                'status_id' => $establValid->status_id,
            ],
            [
                'trade_name' => 'Le Fantôme de l\'Opéra',
                'siret' => fake()->siret(),
                'address_id' => $address3->address_id,
                'logo' => fake()->imageUrl(180, 180, 'Establishment logo', false),
                'phone' => fake()->phoneNumber(),
                'email' => 'fantome.opera@mail.fr',
                'website' => 'www.lefantomedelopera.fr',
                'opening' => [
                    'Lundi' => 'fermer',
                    'Mardi' => '17h00 - 01h00',
                    'Mercredi' => '17h00 - 01h00',
                    'Jeudi' => '17h00 - 01h00',
                    'Vendredi' => '17h00 - 01h00',
                    'Samedi' => '17h00 - 01h00',
                    'Dimanche' => '17h00 - 01h00',
                ],
                'owner_id' => $ownerId->owner_id,
                'status_id' => $establValid->status_id,
            ],
        ];
        foreach ($datas as $data) {
            Establishment::create($data);
        }
    }
}
