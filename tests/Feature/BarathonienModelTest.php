<?php


namespace Tests\Feature;

use App\Models\Barathonien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarathonienModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if barathonien model exist.
     *
     */
    public function test_barathonien_model_exist()
    {
        $barathonien = Barathonien::query()->first();

        $this->assertModelExists($barathonien);
    }


}
