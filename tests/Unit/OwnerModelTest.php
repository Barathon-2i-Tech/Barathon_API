<?php


use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if owner model exist.
     *
     */
    public function test_owner_model_exist()
    {
        $owner = Owner::query()->first();

        $this->assertModelExists($owner);
    }
}
