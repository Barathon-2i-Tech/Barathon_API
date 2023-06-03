<?php


namespace Tests\Feature;

use App\Models\Establishment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstablishmentModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if establishment model exist.
     *
     */
    public function test_establishment_model_exist()
    {
        $establishment = Establishment::factory()->create();
        $this->assertModelExists($establishment);
    }


}
