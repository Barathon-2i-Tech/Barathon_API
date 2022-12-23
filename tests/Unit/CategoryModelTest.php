<?php


use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if category model exist.
     *
     */
    public function test_category_model_exist()
    {
        $category = Category::factory()->create();
        $this->assertModelExists($category);
    }


}
