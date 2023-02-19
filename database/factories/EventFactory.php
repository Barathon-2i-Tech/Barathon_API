<?php

namespace Database\Factories;


use App\Models\Establishment;
use App\Models\Event;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;


/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition()
    {


        $establishment = Establishment::where('trade_name', 'Fait Foif')->first();
        $eventValid = Status::where('comment->code', 'EVENT_VALID')->first();

        $user = DB::table('users')
            ->join('employees', 'users.employee_id', "=", "employees.employee_id")
            ->join('establishments_employees', 'employees.employee_id', "=", "establishments_employees.employee_id")
            ->join('establishments', 'establishments_employees.establishment_id', "=", "establishments.establishment_id")
            ->where('establishments.trade_name', $establishment->trade_name)
            ->get();

        return [
            'event_name' => fake()->words(3, true),
            'description'=> fake()->text(),
            'start_event'=> Carbon::now()->addDays(15),
            'end_event'=> Carbon::now()->addDays(15)->addRealHours(3),
            'poster'=>fake()->imageUrl(1920, 540, "barathon poster event", false),
            'price'=>fake()->numberBetween(0, 50),
            'capacity'=>fake()->numberBetween(1, 200),
            'establishment_id'=>$establishment->establishment_id,
            'status_id'=> $eventValid->status_id,
            'user_id'=> $user[0]->user_id
        ];
    }
}
