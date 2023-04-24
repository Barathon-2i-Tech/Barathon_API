<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryEventController extends Controller
{
    use HttpResponses;

    /**
     * Display all categories associate with an event
     */
    public function getAllCategoriesByEventId(int $eventId): JsonResponse
    {
        try {
            $eventCategories = DB::table('categories_events')
                ->join(
                    'events',
                    'events.event_id',
                    '=',
                    'categories_events.event_id'
                )
                ->join('categories', 'categories.category_id', '=', 'categories_events.category_id')
                ->where('categories_events.event_id', $eventId)
                ->select(
                    'events.event_id',
                    'events.event_name',
                    'categories.*'
                )
                ->get();

            if ($eventCategories->isEmpty()) {
                return $this->error(null, "No categories found for this event", 404);
            }


            return $this->success($eventCategories, "Categories List");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

    /*
    * Associate a category to an event
    *
    */
    public function associateCategoriesToEvent(Request $request, int $eventId): jsonResponse
    {
        try {
            $event = Event::find($eventId);
            if (!$event) {
                error_log('ERROR Event not found');
                return $this->error(null, "Event not found", 404);
            }

            //check if the categories are in an array
            if (!is_array($request->input('options'))) {
                error_log('ERROR Categories must be an array');
                return $this->error(null, "Categories must be an array", 400);
            }

            // check if the array length is under or equal to 4
            if (count($request->input('options')) > 4) {
                error_log("ERROR You can't associate more than 4 categories to an event");
                return $this->error(null, "You can't associate more than 4 categories to an event", 400);
            }
            if (count($request->input('options')) == 0 ){
                error_log("ERROR You must associate at least one category to an event");
                return $this->error(null, "You must associate at least one category to an event", 400);
            }

            $event->categories()->sync($request->input('options'));
            error_log("ERRORRR Categories associated to the event");
            return $this->success(null, "Categories associated to the event");

        } catch (Exception $error) {
            return $this->error(null, $error->getMessage(), 500);
        }
    }

}
