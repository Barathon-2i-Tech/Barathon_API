<?php

namespace App\Http\Controllers;

use App\Models\Establishment;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryEstablishmentController extends Controller
{
    use HttpResponses;

    /**
     * Display all categories associate with an establishment
     *
     * @return JsonResponse
     */
    public function getAllCategoriesByEstablishmentId(int $estbalishmentId)
    {
        try {
            $establCategories = DB::table('categories_establishments')
                ->join(
                    'establishments',
                    'establishments.establishment_id',
                    '=',
                    'categories_establishments.establishment_id'
                )
                ->join('categories', 'categories.category_id', '=', 'categories_establishments.category_id')
                ->where('categories_establishments.establishment_id', $estbalishmentId)
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

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /*
    * Associate a category to an establishment
    *
    */
    public function associateCategoriesToEstablishment(Request $request, int $establishmentId): jsonResponse
    {
        try {
            $establishment = Establishment::find($establishmentId);
            if (!$establishment) {
                error_log('ERRORRR Establishment not found');
                return $this->error(null, "Establishment not found", 404);
            }

            //check if the categories are in an array
            if (!is_array($request->input('options'))) {
                error_log('ERRORRR Categories must be an array');
                return $this->error(null, "Categories must be an array", 400);
            }

            // check if the array length is under or equal to 4
            if (count($request->input('options')) > 4) {
                error_log("ERRORRR You can't associate more than 4 categories to an establishment");
                return $this->error(null, "You can't associate more than 4 categories to an establishment", 400);
            }

            $establishment->categories()->sync($request->input('options'));
            error_log("ERRORRR Categories associated to the establishment");
            return $this->success(null, "Categories associated to the establishment");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

}
