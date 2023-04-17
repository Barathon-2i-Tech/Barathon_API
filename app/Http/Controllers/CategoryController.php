<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use HttpResponses;

    private const CATEGORIES_LIST = "Categories List";


    /**
     * Get all categories with sub_category = Establishment || All
     *
     */
    public function getAllEstablishmentCategories(): JsonResponse
    {
        $allEstablishmentCategories = Category::where(function ($query) {
            $query->where('category_details->sub_category', 'Establishment')
                ->orWhere('category_details->sub_category', 'All');
        })
            ->get();

        if ($allEstablishmentCategories->isEmpty()) {
            return $this->error(null, "No establishment categories found", 404);
        }

        return $this->success($allEstablishmentCategories, self::CATEGORIES_LIST);
    }

    /**
     * Get all categories with sub_category = Establishment || All
     *
     */
    public function getAllEventCategories(): JsonResponse
    {
        $allEventCategories = Category::where(function ($query) {
            $query->where('category_details->sub_category', 'Event')
                ->orWhere('category_details->sub_category', 'All');
        })
            ->get();

        if ($allEventCategories->isEmpty()) {
            return $this->error(null, "No event categories found", 404);
        }

        return $this->success($allEventCategories, self::CATEGORIES_LIST);

    }

    /**
     * Get all categories for admin part
     *
     */
    public function getAllCategories(): JsonResponse
    {
        $allCategories = Category::all();

        if ($allCategories->isEmpty()) {
            return $this->error(null, "No categories found", 404);
        }

        return $this->success($allCategories, self::CATEGORIES_LIST);
    }

    /**
     * Store a newly created resource in storage.
     *
     */

    public function store(Request $request): JsonResponse
    {
        $beerIcon = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path d='M32 64c0-17.7 14.3-32 32-32H352c17.7 0 32 14.3 32 32V96h51.2c42.4 0 76.8 34.4 76.8 76.8V274.9c0 30.4-17.9 57.9-45.6 70.2L384 381.7V416c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V64zM384 311.6l56.4-25.1c4.6-2.1 7.6-6.6 7.6-11.7V172.8c0-7.1-5.7-12.8-12.8-12.8H384V311.6zM160 144c0-8.8-7.2-16-16-16s-16 7.2-16 16V368c0 8.8 7.2 16 16 16s16-7.2 16-16V144zm64 0c0-8.8-7.2-16-16-16s-16 7.2-16 16V368c0 8.8 7.2 16 16 16s16-7.2 16-16V144zm64 0c0-8.8-7.2-16-16-16s-16 7.2-16 16V368c0 8.8 7.2 16 16 16s16-7.2 16-16V144z'/></svg>";


        $request->validate([
            'category_details.sub_category' => [
                'required',
                'string',
                Rule::in(['Establishment', 'Event', 'All']),
            ],
            'category_details.label' => 'required|string',
            'category_details.icon' => 'nullable|string',
        ], [
            'category_details.sub_category.in' => 'La sous categorie doit être Establishment, Event ou All',
            'category_details.sub_category.required' => 'La sous categorie est requise',
            'category_details.label.required' => 'Le label est requis',
            'category_details.icon.string' => 'L\'icone doit être une chaine de caractères',
        ]);

        if ($request->input('category_details.icon') === null) {
            $request->merge(['category_details.icon' => $beerIcon]);
        }

        $newCategory = array(
            'category_details.sub_category' => $request->input('category_details.sub_category'),
            'category_details.label' => $request->input('category_details.label'),
            'category_details.icon' => $request
                ->input('category_details.icon') === null ? $beerIcon : $request->input('category_details.icon'),
        );

        $newCategory = json_encode($newCategory);
        $category = new Category;
        $category->category_details = $newCategory;
        $category->save();

        return $this->success([
            $category
        ], "Category created successfully", 201);

    }

    /**
     * Display the specified resource.
     *
     */
    public function show(int $categoryId): JsonResponse
    {
        $category = Category::find($categoryId);

        if ($category === null) {
            return $this->error(null, "Category not found", 404);
        }

        return $this->success($category, "Category found");
    }

    public function update(Request $request, int $categoryId): JsonResponse
    {
        $category = Category::find($categoryId);

        if ($category === null) {
            return $this->error(null, "Category not found", 404);
        }

        $request->validate([
            'category_details.sub_category' => [
                'required',
                'string',
                Rule::in(['Establishment', 'Event', 'All']),
            ],
            'category_details.label' => 'required|string',
            'category_details.icon' => 'nullable|string',
        ], [
            'category_details.sub_category.in' => 'La sous categorie doit être Establishment, Event ou All',
            'category_details.sub_category.required' => 'La sous categorie est requise',
            'category_details.label.required' => 'Le label est requis',
            'category_details.icon.string' => 'L\'icone doit être une chaine de caractères',
        ]);

        $category->category_details = json_encode($request->input('category_details'));
        $category->save();

        return $this->success($category, "Category updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(int $categoryId): JsonResponse
    {
        $category = Category::find($categoryId);

        if ($category === null) {
            return $this->error(null, "Category not found", 404);
        }

        $category->delete();

        return $this->success(null, "Category deleted successfully");
    }


    /**
     * Get top ten categories
     *
     * @return JsonResponse
     */
    public function getTopTenCategories()
    {
        //get top ten categories used by events
        $categories = DB::table('categories_events')
            ->join('categories', 'categories_events.category_id', '=', 'categories.category_id')
            ->select(
                'categories.category_id',
                'categories.category_details',
                DB::raw('COUNT(categories_events.category_id) as total_cate'))
            ->groupBy('categories.category_id')
            ->orderBy('total_cate', 'desc')
            ->skip(0)
            ->take(10)
            ->get();

        return $this->success([
            'categories' => $categories,
        ]);
    }
}
