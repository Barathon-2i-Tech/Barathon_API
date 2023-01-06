<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'address' => '9 place Camille Georges',
                'postal_code' => '69002',
                'city' => 'Lyon',
            ],
            [
                'address' => '269 rue jean jaures',
                'postal_code' => '69007',
                'city' => 'Lyon',
            ],
            [
                'address' => '69 avenue tony garnier',
                'postal_code' => '69007',
                'city' => 'Lyon',
            ],

        ];

        foreach ($datas as $data) {
            Address::create($data);
        }
    }
}
