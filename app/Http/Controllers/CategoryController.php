<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    use HttpResponses;

    private const CATEGORIES_NOT_FOUND = "Categories not found";


    /**
     * Get all categories with sub_category = Establishment || All
     * and state = Approved
     *
     */
    public function getAllEstablishmentCategories(): JsonResponse
    {
        $allEstablishmentCategories = Category::where(function ($query) {
            $query->where('category_details->sub_category', 'Establishment')
                ->orWhere('category_details->sub_category', 'All');
        })
            ->where('category_details->state', 'Approved')
            ->get();

        if ($allEstablishmentCategories->isEmpty()) {
            return $this->error(null, "No establishment categories found", 404);
        }

        return $this->success($allEstablishmentCategories, "Categories List");
    }

    /**
     * Get all categories with sub_category = Establishment || All
     * and state = Approved
     *
     */
    public function getAllEventCategories(): JsonResponse
    {
        $allEventCategories = Category::where(function ($query) {
            $query->where('category_details->sub_category', 'Event')
                ->orWhere('category_details->sub_category', 'All');
        })
            ->where('category_details->state', 'Approved')
            ->get();

        if ($allEventCategories->isEmpty()) {
            return $this->error(null, "No event categories found", 404);
        }

        return $this->success($allEventCategories, "Categories List");

    }


    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $request->validate([
            'sub_category' => 'required|string',
            'icon' => 'required|string',
            'label' => 'required|string',
        ]);

        $label = array('sub_category' => $request->sub_category, 'icon' => $request->icon, 'label' => $request->label, 'state' => 'Hold');

        $label = json_encode('label');

        $category = Category::create([
            'label' => $label,
        ]);

        $category->save();

        return $this->success([
            $category
        ], "Category created", 201);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
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
