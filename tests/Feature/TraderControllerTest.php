<?php

namespace Tests\Feature;

use App\Models\Trader;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TraderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @return array
     */
    private function getUserWithToken(): array
    {
        $user = User::factory()->create(['type' => 'APPROVER']);
        $token = $user->createToken('API TOKEN')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function test_create_trader()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $payload = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'working_hours' => $this->faker->randomFloat(2, 1, 24),
            'payroll_per_hour' => $this->faker->randomFloat(2, 10, 50),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/traders', $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Trader created successfully']);
    }

    public function test_index_traders()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $traders = collect();
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();
            $trader = Trader::factory()->create(['user_id' => $user->id]);
            $traders->push($trader);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/traders');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'traders');
    }

    public function test_show_trader()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $user = User::factory()->create();
        $trader = Trader::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/traders/' . $trader->id);

        $response->assertStatus(200);
        $response->assertJson(['trader' => $trader->toArray()]);
    }

    public function test_update_trader()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $user = User::factory()->create();
        $trader = Trader::factory()->create(['user_id' => $user->id]);

        $payload = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'working_hours' => $this->faker->randomFloat(2, 1, 24),
            'payroll_per_hour' => $this->faker->randomFloat(2, 10, 50),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/traders/' . $trader->id, $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Trader updated successfully']);
    }

    public function test_destroy_trader()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $user = User::factory()->create(['type' => 'NON_APPROVER']);
        $trader = Trader::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/traders/' . $trader->id);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Trader deleted successfully']);

        $checkTrader = Trader::query()->find($trader->id);
        $checkUser = User::query()->find($user->id);

        $this->assertNull($checkTrader);
        $this->assertNull($checkUser);
    }
}
