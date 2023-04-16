<?php


use App\Models\Category_Establishment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Category_establishmentModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if category_establishment model exist.
     *
     */
    public function test_category_establishment_model_exist()
    {
        $categoryEstablishment = Category_Establishment::factory()->create();
        $this->assertModelExists($categoryEstablishment);

    }


}
