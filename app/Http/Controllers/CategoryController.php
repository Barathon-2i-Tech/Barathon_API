<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
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
                'categories.label',
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
