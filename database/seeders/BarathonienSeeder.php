<?php

namespace Database\Seeders;


use App\Models\Barathonien;
use Illuminate\Database\Seeder;

class BarathonienSeeder extends Seeder
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
                'birthday' => '1985-06-08',
                'address' => '69 avenue tony garnier',
                'postal_code' => '69007',
                'city' => 'Lyon',
                'avatar' => "https://picsum.photos/180"
            ]
        ];
        Barathonien::create($datas[0]);
    }
}
