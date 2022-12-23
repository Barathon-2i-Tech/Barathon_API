<?php

namespace Database\Seeders;


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
        $owner_id = Owner::all('owner_id')->first();
        $ESTABL_VALID = Status::where('comment->code', 'ESTABL_VALID')->first();

        $datas = [
            [
                'trade_name'=>'Fait Foif',
                'siret'=>fake()->siret(),
                'address'=>'267 Rue Marcel Mérieux',
                'postal_code'=>'69007',
                'city'=>'Lyon',
                'logo'=> fake()->imageUrl(180,180,"Establishment logo",false,),
                'phone'=>fake()->phoneNumber(),
                'email'=>'etablissement@mail.fr',
                'website'=>'www.google.fr',
                'opening'=> [
                    'Lundi' => 'fermer',
                    'Mardi' => '17h00 - 01h00',
                    'Mercredi' => '17h00 - 01h00',
                    'Jeudi' => '17h00 - 01h00',
                    'Vendredi' => '17h00 - 01h00',
                    'Samedi' => '17h00 - 01h00',
                    'Dimanche' => '17h00 - 01h00',
                ],
                'checked'=>true,
                'owner_id'=>$owner_id->owner_id,
                'status_id'=>$ESTABL_VALID->status_id,
            ],
            [
                'trade_name'=>'Le Fantôme de l\'Opéra',
                'siret'=>fake()->siret(),
                'address'=>'19 rue Royale',
                'postal_code'=>'69001',
                'city'=>'Lyon',
                'logo'=> fake()->imageUrl(180,180,"Establishment logo",false,),
                'phone'=>fake()->phoneNumber(),
                'email'=>'fantome.opera@mail.fr',
                'website'=>'www.lefantomedelopera.fr',
                'opening'=> [
                    'Lundi' => 'fermer',
                    'Mardi' => '17h00 - 01h00',
                    'Mercredi' => '17h00 - 01h00',
                    'Jeudi' => '17h00 - 01h00',
                    'Vendredi' => '17h00 - 01h00',
                    'Samedi' => '17h00 - 01h00',
                    'Dimanche' => '17h00 - 01h00',
                ],
                'checked'=>true,
                'owner_id'=>$owner_id->owner_id,
                'status_id'=>$ESTABL_VALID->status_id,
            ]
            ,
            [
                'trade_name'=>'Shrubbery',
                'siret'=>fake()->siret(),
                'address'=>'17 Rue d\'Inkermann',
                'postal_code'=>'69100',
                'city'=>'Villeurbanne',
                'logo'=> fake()->imageUrl(180,180,"Establishment logo",false,),
                'phone'=>fake()->phoneNumber(),
                'email'=>'Shrubbery@mail.fr',
                'website'=>'facebook.com/LeShrubbery',
                'opening'=> [
                    'Lundi' => 'fermer',
                    'Mardi' => '17h00 - 01h00',
                    'Mercredi' => '17h00 - 01h00',
                    'Jeudi' => '17h00 - 01h00',
                    'Vendredi' => '17h00 - 01h00',
                    'Samedi' => '17h00 - 01h00',
                    'Dimanche' => '17h00 - 01h00',
                ],
                'checked'=>true,
                'owner_id'=>$owner_id->owner_id,
                'status_id'=>$ESTABL_VALID->status_id,
            ]
        ];
        foreach ($datas as $data) {
            Establishment::create($data);
        }
    }
}
