<?php


use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if status model exist.
     *
     */
    public function test_status_model_exist()
    {
       $status = Status::query()->first();

       $this->assertModelExists($status);
    }


}
