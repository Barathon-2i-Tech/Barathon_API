<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Establishment;
use App\Models\Owner;
use App\Models\Status;
use App\Models\User;
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
        $establValid = Status::where('comment->code', 'ESTABL_VALID')->first();
        $establPending = Status::where('comment->code', 'ESTABL_PENDING')->first();

        $FaifFoifAddress = Address::create([
            'address' => "69 avenue tony Garnier",
            'postal_code' => "69007",
            'city' => "Lyon"
        ]);
        $fantomeOperaAddress = Address::create([
            'address' => "19 rue royale",
            'postal_code' => "69001",
            'city' => "Lyon"
        ]);
        $cafeLumiereAddress = Address::create([
            'address' => "137 Rue de la République",
            'postal_code' => "69150",
            'city' => "Decines Charpieu"
        ]);

        $ownerRothschild = User::where ('last_name', 'Rothschild')->first();

        $ownerJacobs = User::where ('last_name', 'Jacobs')->first();

        $datas = [
            [
                'trade_name' => 'Fait Foif',
                'siret' => fake()->siret(),
                'address_id' => $FaifFoifAddress->address_id,
                'logo' => fake()->imageUrl(180, 180, 'Establishment logo', false),
                'phone' => fake()->phoneNumber(),
                'email' => 'barathon.m2i+establishment@gmail.com',
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
                'owner_id' => $ownerRothschild->owner_id,
                'status_id' => $establValid->status_id,
            ],
            [
                'trade_name' => 'Le Fantôme de l\'Opéra',
                'siret' => '53096293500025',
                'address_id' => $fantomeOperaAddress->address_id,
                'logo' => fake()->imageUrl(180, 180, 'Establishment logo', false),
                'phone' => '0437920388',
                'email' => 'barathon.m2i+establishment2@gmail.com',
                'website' => 'www.lefantomedelopera.fr',
                'opening' => [
                    'Lundi' => 'fermer',
                    'Mardi' => '18h00 - 01h00',
                    'Mercredi' => '18h00 - 01h00',
                    'Jeudi' => '18h00 - 01h00',
                    'Vendredi' => '18h00 - 01h00',
                    'Samedi' => '18h00 - 01h00',
                    'Dimanche' => 'fermer',
                ],
                'owner_id' => $ownerJacobs->owner_id,
                'status_id' => $establPending->status_id,
            ],
            [
                'trade_name' => 'LE CAFE LUMIERE',
                'siret' => '83154825000024',
                'address_id' => $cafeLumiereAddress->address_id,
                'logo' => fake()->imageUrl(180, 180, 'Establishment logo', false),
                'phone' => "0472055598",
                'email' => 'barathon.m2i+establishment3@gmail.com',
                'website' => 'https://le-cafe-lumiere.business.site/',
                'opening' => [
                    'Lundi' => '07h00 - 21h30',
                    'Mardi' => '07h00 - 21h30',
                    'Mercredi' => '07h00 - 21h30',
                    'Jeudi' => '07h00 - 21h30',
                    'Vendredi' => '07h00 - 21h30',
                    'Samedi' => '10h30 - 20h00',
                    'Dimanche' => '08h00 - 18h00',
                ],
                'owner_id' => $ownerRothschild->owner_id,
                'status_id' => $establPending->status_id,
            ],
        ];
        foreach ($datas as $data) {
            Establishment::create($data);
        }
    }
}
