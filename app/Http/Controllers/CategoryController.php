<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use HttpResponses;

    private const CATEGORIES_LIST = "Categories List";
    private const CATEGORY_NOT_FOUND = "Category not found";

    private const SUB_CATEGORY = 'category_details->sub_category';

    private const BEER_ICON = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path d='M32 64c0-17.7 14.3-32 32-32H352c17.7 0 32 14.3 32 32V96h51.2c42.4 0 76.8 34.4 76.8 76.8V274.9c0 30.4-17.9 57.9-45.6 70.2L384 381.7V416c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V64zM384 311.6l56.4-25.1c4.6-2.1 7.6-6.6 7.6-11.7V172.8c0-7.1-5.7-12.8-12.8-12.8H384V311.6zM160 144c0-8.8-7.2-16-16-16s-16 7.2-16 16V368c0 8.8 7.2 16 16 16s16-7.2 16-16V144zm64 0c0-8.8-7.2-16-16-16s-16 7.2-16 16V368c0 8.8 7.2 16 16 16s16-7.2 16-16V144zm64 0c0-8.8-7.2-16-16-16s-16 7.2-16 16V368c0 8.8 7.2 16 16 16s16-7.2 16-16V144z'/></svg>";

    private const UNAUTHORIZED_ACTION = "This action is unauthorized.";

    /**
     * Get all categories with sub_category = Establishment || All
     *
     */
    public function getAllEstablishmentCategories(): JsonResponse
    {
        $allEstablishmentCategories = Category::where(function ($query) {
            $query->where(self::SUB_CATEGORY, 'Establishment')
                ->orWhere(self::SUB_CATEGORY, 'All');
        })
            ->get();

        if ($allEstablishmentCategories->isEmpty()) {
            return $this->error(null, "No establishment categories found", 404);
        }

        return $this->success($allEstablishmentCategories, self::CATEGORIES_LIST);
    }

    /**
     * Get all categories with sub_category = Event || All
     *
     */
    public function getAllEventCategories(): JsonResponse
    {
        $allEventCategories = Category::where(function ($query) {
            $query->where(self::SUB_CATEGORY, 'Event')
                ->orWhere(self::SUB_CATEGORY, 'All');
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
        $allCategories = Category::withTrashed()->get();

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
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
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


        $newCategory = array(
            'sub_category' => $request->input('category_details.sub_category'),
            'label' => $request->input('category_details.label'),
            'icon' => $request
                ->input('category_details.icon') === null ? self::BEER_ICON: $request->input('category_details.icon'),
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
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $category = Category::find($categoryId);

        if ($category === null) {
            return $this->error(null, self::CATEGORY_NOT_FOUND, 404);
        }

        return $this->success($category, "Category found");
    }

    public function update(Request $request, int $categoryId): JsonResponse
    {
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $category = Category::find($categoryId);

        if ($category === null) {
            return $this->error(null, self::CATEGORY_NOT_FOUND, 404);
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


        $updateCategory = array(
            'sub_category' => $request->input('category_details.sub_category'),
            'label' => $request->input('category_details.label'),
            'icon' => $request
                ->input('category_details.icon') === null ? self::BEER_ICON : $request->input('category_details.icon'),
        );

        $updateCategory = json_encode($updateCategory);


        $category->category_details =  $updateCategory;
        $category->save();

        return $this->success($category, "Category updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(int $categoryId): JsonResponse
    {
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $category = Category::withTrashed()->find($categoryId);

        if ($category === null) {
            return $this->error(null, self::CATEGORY_NOT_FOUND, 404);
        }

        if ($category->trashed()) {
            return $this->error(null, "Category already deleted", 404);
        }

        $category->delete();

        return $this->success(null, "Category deleted successfully");
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(int $categoryId): JsonResponse
    {
        $user = Auth::user();

        if ( $user->administrator_id === null) {
            return $this->error(null, self::UNAUTHORIZED_ACTION, 401);
        }

        $category = Category::withTrashed()->find($categoryId);


        if ($category === null) {
            return $this->error(null, self::CATEGORY_NOT_FOUND, 404);
        }

        if (!$category->trashed()) {
            return $this->error(null, "Category already restored", 404);
        }

        $category->restore();

        return $this->success(null, "Category restored successfully");
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
                DB::raw('COUNT(categories_events.category_id) as total_cate')
            )
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
