<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Event_update;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event_update>
 */
class Event_updateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $EVENT_PENDING = Status::where('comment->code', 'EVENT_PENDING')->first();
        $event = Event::factory()->create();

        return [
            'event_id' => $event->event_id,
            'event_name' => $event->event_name,
            'description'=> $event->description,
            'start_event'=> $event->start_event,
            'end_event'=> $event->end_event,
            'poster'=>$event->poster,
            'price'=>$event->price,
            'capacity'=>$event->capacity,
            'rejected'=>$event->rejected,
            'establishment_id'=>$event->establishment_id,
            'status_id'=> $EVENT_PENDING,
            'user_id'=> $event->user_id,
        ];
    }
}
