<?php


use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if event model exist.
     *
     */
    public function test_event_model_exist()
    {
        $event = Event::factory()->create();
        $this->assertModelExists($event);
    }


}
