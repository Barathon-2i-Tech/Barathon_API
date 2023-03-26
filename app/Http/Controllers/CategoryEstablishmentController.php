<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CategoryEstablishmentController extends Controller
{
    use HttpResponses;

    /**
     * Display all categories associate with an establishment
     */
    public function getAllCategoriesByEstablishmentId($establishmentId): JsonResponse
    {
        $establCategories = DB::table('categories_establishments')
            ->join(
                'establishments',
                'establishments.establishment_id',
                '=',
                'categories_establishments.establishment_id'
            )
            ->join('categories', 'categories.category_id', '=', 'categories_establishments.category_id')
            ->where('categories_establishments.establishment_id', $establishmentId)
            ->select(
                'establishments.establishment_id',
                'establishments.trade_name as establishment_name',
                'categories.*'
            )
            ->get();

        if ($establCategories->isEmpty()) {
            return $this->error(null, "No categories found for this establishment", 404);
        }
        return $this->success($establCategories, "Categories List");
    }
}
