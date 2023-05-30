<?php


namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Check if user model exist.
     *
     */
    public function test_user_model_exist()
    {
        $user = User::query()->first();
        $this->assertModelExists($user);

    }
}
