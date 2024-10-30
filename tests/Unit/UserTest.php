<?php

namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserTest extends TestCase {
    use RefreshDatabase;

    public function test_get_balance()
    {
        $user = User::factory()->create(['balance' => 100.50]);

        $balance = $user->getBalance();

        $this->assertEquals(100.50, $balance);
    }
}
