<?php


use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdministratorModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if administrator model exist.
     *
     */
    public function test_administrator_model_exist()
    {
       $administrator = Administrator::query()->first();

       $this->assertModelExists($administrator);
    }


}
