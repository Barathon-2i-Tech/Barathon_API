<?php


use App\Models\Event_update;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Event_updateModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if event_update model exist.
     *
     */
    public function test_event_update_model_exist()
    {
        $eventUpdate = Event_update::factory()->create();
        $this->assertModelExists($eventUpdate);
    }


}
