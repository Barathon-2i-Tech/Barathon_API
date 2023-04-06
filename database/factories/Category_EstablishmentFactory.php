<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Category_Establishment;
use App\Models\Establishment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category_Establishment>
 */
class Category_EstablishmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $establishment = Establishment::query()->first();
        $category = Category::query()->first();

        return [
            'establishment_id' => $establishment->establishment_id,
            'category_id' => $category->category_id,
        ];
    }
}
