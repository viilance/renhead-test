<?php

namespace Tests\Feature;

use App\Models\Professor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfessorControllerTest extends TestCase
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

    public function test_create_professor()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $payload = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'total_available_hours' => $this->faker->randomFloat(2, 1, 24),
            'payroll_per_hour' => $this->faker->randomFloat(2, 10, 50),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/professors', $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Professor created successfully']);
    }

    public function test_index_professors()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $professors = collect();
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();
            $professor = Professor::factory()->create(['user_id' => $user->id]);
            $professors->push($professor);
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/professors');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'professors');
    }

    public function test_show_professor()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $user = User::factory()->create();
        $professor = Professor::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/professors/' . $professor->id);

        $response->assertStatus(200);
        $response->assertJson(['professor' => $professor->toArray()]);
    }

    public function test_update_professor()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $user = User::factory()->create();
        $professor = Professor::factory()->create(['user_id' => $user->id]);

        $payload = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'total_available_hours' => $this->faker->randomFloat(2, 1, 24),
            'payroll_per_hour' => $this->faker->randomFloat(2, 10, 50),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/professors/' . $professor->id, $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Professor updated successfully']);
    }

    public function test_destroy_professor()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $user = User::factory()->create(['type' => 'NON_APPROVER']);
        $professor = Professor::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/professors/' . $professor->id);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Professor deleted successfully']);

        $checkProfessor = Professor::query()->find($professor->id);
        $checkUser = User::query()->find($user->id);

        $this->assertNull($checkProfessor);
        $this->assertNull($checkUser);
    }
}
