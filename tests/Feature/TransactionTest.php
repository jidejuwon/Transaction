<?php

namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;


class TransactionTest extends TestCase {

    public function test_unathorise_invalid_token(){
        $user = User::factory()->create(['balance' => 100]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. "INVALID TOKEN" ,
        ])->getJson('/api/balance?email='. urlencode($user->email));

        $response->assertStatus(401);
    }

    public function test_invalid_user(){

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '. env("API_TOKEN") ,
        ])->getJson('/api/balance?email='. urlencode("invalid@email.com"));

        $response->assertStatus(400);
    }

    public function test_credit_transaction()
    {
        $user = User::factory()->create(['balance' => 100]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' .env("API_TOKEN"),
        ])->postJson('/api/transaction', [
            'email' => $user->email,
            'amount' => 50,
            'type' => 'credit'
        ]);


        $response->assertStatus(201);
        $this->assertEquals(150, $user->fresh()->balance);
    }

    public function test_debit_transaction(){
        $user = User::factory()->create(['balance' => 100]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' .env("API_TOKEN"),
        ])->postJson('/api/transaction', [
            'email' => $user->email,
            'amount' => 50,
            'type' => 'debit'
        ]);

        $response->assertStatus(201);
        $this->assertEquals(50, $user->fresh()->balance);
    }

    public function test_invalid_transaction(){
        $user = User::factory()->create(['balance' => 100]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' .env("API_TOKEN"),
        ])->postJson('/api/transaction', [
            'email' => $user->email,
            'amount' => 50,
            'type' => 'card'
        ]);

        $response->assertStatus(400);
    }

    public function test_invalid_amount(){
        $user = User::factory()->create(['balance' => 20]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' .env("API_TOKEN"),
        ])->postJson('/api/transaction', [
            'email' => $user->email,
            'amount' => -50,
            'type' => 'credit'
        ]);

        $response->assertStatus(400);
        $this->assertEquals(20, $user->fresh()->balance);
    }

    public function test_insufficient_funds()
    {
        $user = User::factory()->create(['balance' => 20]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' .env("API_TOKEN"),
        ])->postJson('/api/transaction', [
            'email' => $user->email,
            'amount' => 50,
            'type' => 'debit'
        ]);

        $response->assertStatus(400);
        $this->assertEquals(20, $user->fresh()->balance);
    }

    public function test_accurate_balance(){
        $user = User::factory()->create(['balance' => 20]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' .env("API_TOKEN"),
        ])->getJson('/api/balance?email='. urlencode($user->email));

        $response->assertStatus(200);
        $this->assertEquals(20, $user->fresh()->balance);
    }

}
