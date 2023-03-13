<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Category_Establishment;
use App\Models\Establishment;
use Illuminate\Database\Seeder;

class CategoryEstablishmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = Category::all('category_id')->first();
        $establishment = Establishment::where('trade_name', 'Fait Foif')->first();

        $datas = [
            [
                'category_id' => $category->category_id,
                'establishment_id' => $establishment->establishment_id,
            ],
        ];
        Category_Establishment::create($datas[0]);
    }
}
