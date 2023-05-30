<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private const  STRUCTURE = [
        "status",
        "message",
        "data" => [
            [
                "category_id",
                "category_details",
                "deleted_at",
            ]
        ]
    ];

    /**
     * A test to check if we can have the top 10 categories used by events.
     *
     * @return void
     */
    public function test_get_top_ten_categories()
    {

        $structure = [
            "status",
            "message",
            "data" => [
                "categories"
            ]];

        $user = $this->createBarathonienUser();
        $response = $this->actingAs($user)->get(route('barathonien.topCateg'))
            ->assertOk();

        $response->assertJsonStructure($structure);

    }

    /**
     * A test to get all establishment categories
     *
     * @return void
     */
    public function test_get_all_establishement_categories(): void
    {

        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('categories.establishment.all'))
            ->assertOk();
        $response->assertJsonStructure(self::STRUCTURE);
        $response->assertJson(['message' => 'Categories List']);

    }

    /**
     * A test to get a 404 no found on empty response all establishment categories
     *
     * @return void
     */
    public function test_get_all_establishment_categories_with_empty_response(): void
    {

        $administrator = $this->createAdminUser();
        DB::table('categories')->truncate();
        $response = $this->actingAs($administrator)->get(route('categories.establishment.all'))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'No establishment categories found']);
    }

    /**
     * A test to get all event categories
     *
     * @return void
     */
    public function test_get_all_event_categories(): void
    {

        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('categories.event.all'))
            ->assertOk();
        $response->assertJsonStructure(self::STRUCTURE);
        $response->assertJson(['message' => 'Categories List']);

    }

    /**
     * A test to get a 404 no found on empty response all event categories
     *
     * @return void
     */
    public function test_get_all_event_categories_with_empty_response(): void
    {

        $administrator = $this->createAdminUser();
        DB::table('categories')->truncate();
        $response = $this->actingAs($administrator)->get(route('categories.event.all'))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'No event categories found']);
    }

    /**
     * A test to get all  categories
     *
     * @return void
     */
    public function test_get_all_categories(): void
    {

        $administrator = $this->createAdminUser();
        $response = $this->actingAs($administrator)->get(route('categories.all'))
            ->assertOk();
        $response->assertJsonStructure(self::STRUCTURE);
        $response->assertJson(['message' => 'Categories List']);

    }

    /**
     * A test to get a 404 no found on empty response all categories
     *
     * @return void
     */
    public function test_get_all_categories_with_empty_response(): void
    {

        $administrator = $this->createAdminUser();
        DB::table('categories')->truncate();
        $response = $this->actingAs($administrator)->get(route('categories.all'))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'No categories found']);
    }

    /**
     * A test to store a new category with valid input
     *
     * @return void
     */
    public function test_to_store_category_with_valid_input()
    {
        $administrator = $this->createAdminUser();

        $sub_category = $this->faker->randomElement(['Establishment', 'Event', 'All']);
        $label = $this->faker->word();
        $icon = $this->faker->word();

        $response = $this->actingAs($administrator)->post(route('categories.store'), [
            'category_details' => [
                'sub_category' => $sub_category,
                'label' => $label,
                'icon' => $icon,
            ]
        ]);

        $response->assertStatus(201);
    }

    /**
     * A test to store a new category with invalid input
     *
     * @return void
     */
    public function test_to_store_category_with_invalid_input()
    {
        $administrator = $this->createAdminUser();

        $sub_category = $this->faker->word();
        $label = '';
        $icon = 123;

        $response = $this->actingAs($administrator)->post(route('categories.store'), [
            'category_details' => [
                'sub_category' => $sub_category,
                'label' => $label,
                'icon' => $icon,
            ]
        ]);

        $response->assertStatus(302);
    }

    /**
     * A test to display a category
     */
    public function test_to_display_category()
    {
        $administrator = $this->createAdminUser();

        $category = Category::factory()->create();

        $response = $this->actingAs($administrator)->get(route('categories.show', $category->category_id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'category_id',
                'category_details',
                'deleted_at',
            ]
        ]);
        $response->assertJson(['message' => 'Category found']);
    }

    /**
     * A test to get a 404 error when display a category
     */
    public function test_to_display_category_with_404_status_()
    {
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('categories.show', 800))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Category not found']);
    }

    /**
     * A test to update a category with valid input
     *
     * @return void
     */
    public function test_to_update_category_with_valid_input()
    {
        $administrator = $this->createAdminUser();

        $category = Category::factory()->create();

        $sub_category = $this->faker->randomElement(['Establishment', 'Event', 'All']);
        $label = $this->faker->word();
        $icon = $this->faker->word();

        $response = $this->actingAs($administrator)->put(route('categories.update', $category->category_id), [
            'category_details' => [
                'sub_category' => $sub_category,
                'label' => $label,
                'icon' => $icon,
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'category_id',
                'category_details',
                'deleted_at',
            ]
        ]);
        $response->assertJson(['message' => 'Category updated successfully']);
    }

    /**
     * A test to update a category who not exist
     *
     * @return void
     */
    public function test_to_update_category_with_404_status()
    {
        $administrator = $this->createAdminUser();

        $sub_category = $this->faker->randomElement(['Establishment', 'Event', 'All']);
        $label = $this->faker->word();
        $icon = $this->faker->word();

        $response = $this->actingAs($administrator)->put(route('categories.update', 800), [
            'category_details' => [
                'sub_category' => $sub_category,
                'label' => $label,
                'icon' => $icon,
            ]
        ]);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Category not found']);
    }

    /**
     * A test to delete a category
     */
    public function test_to_delete_category()
    {
        $administrator = $this->createAdminUser();

        $category = Category::factory()->create();

        $response = $this->actingAs($administrator)->delete(route('categories.delete', $category->category_id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Category deleted successfully']);
    }

    /**
     * A test to delete a category already deleted
     */
    public function test_to_delete_category_already_deleted()
    {
        $administrator = $this->createAdminUser();

        $category = Category::first();
        $category->delete();

        $response = $this->actingAs($administrator)->delete(route('categories.delete', $category->category_id));
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Category already deleted']);
    }

    /**
     * A test to get a 404 error when delete a category
     */
    public function test_to_delete_category_with_404_status_()
    {
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->delete(route('categories.delete', 800))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Category not found']);
    }

    /**
     * A test to restore a category
     */
    public function test_to_restore_category()
    {
        $administrator = $this->createAdminUser();

        $category = Category::first();
        $category->delete();

        $response = $this->actingAs($administrator)->get(route('categories.restore', $category->category_id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Category restored successfully']);
    }

    /**
     * A test to get a 404 error when restore a category
     */
    public function test_to_restore_category_with_404_status_()
    {
        $administrator = $this->createAdminUser();

        $response = $this->actingAs($administrator)->get(route('categories.restore', 500))
            ->assertNotFound();
        $response->assertJsonStructure([
            'status',
            'message',
            'data',
        ]);
        $response->assertJson(['message' => 'Category not found']);
    }

    /**
     * A test to restore a category already restored
     */
    public function test_to_restore_category_already_restored()
    {
        $administrator = $this->createAdminUser();

        $category = Category::first();

        $response = $this->actingAs($administrator)->get(route('categories.restore', $category->category_id));
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);
        $response->assertJson(['message' => 'Category already restored']);
    }
}
