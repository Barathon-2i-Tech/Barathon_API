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
        $categoryBarACocktails = Category::where('category_details->label', 'Bar à cocktails')->first();
        $categoryBarSportif = Category::where('category_details->label', 'Sportif')->first();
        $categoryBarAVin = Category::where('category_details->label', 'Bar à vin')->first();
        $categoryBarKaraoke = Category::where('category_details->label', 'Karaoké')->first();

        $establishmentFaitFoif = Establishment::where('trade_name', 'Fait Foif')->first();
        $establishmentFantomeOpera = Establishment::where('trade_name', 'Le Fantôme de l\'Opéra')->first();
        $establishmentCafeLumiere = Establishment::where('trade_name', 'LE CAFE LUMIERE')->first();


        $datas = [
            [
                'category_id' => $categoryBarACocktails->category_id,
                'establishment_id' => $establishmentFaitFoif->establishment_id,
            ],
            [
                'category_id' => $categoryBarKaraoke->category_id,
                'establishment_id' => $establishmentFaitFoif->establishment_id,
            ],
            [
                'category_id' => $categoryBarSportif->category_id,
                'establishment_id' => $establishmentCafeLumiere->establishment_id,
            ],
            [
                'category_id' => $categoryBarAVin->category_id,
                'establishment_id' => $establishmentFantomeOpera->establishment_id,
            ],
        ];
        Category_Establishment::create($datas[0]);
    }
}
