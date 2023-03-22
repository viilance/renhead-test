<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\Professor;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobControllerTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker;

    private function getUserWithToken(): array
    {
        $user = User::factory()->create(['type' => 'APPROVER']);
        $token = $user->createToken('API TOKEN')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function test_index_jobs()
    {
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create(['type' => 'NON_APPROVER']);
            $professor = Professor::factory()->create(['user_id' => $user->id]);
            $user2 = User::factory()->create(['type' => 'NON_APPROVER']);
            $trader = Trader::factory()->create(['user_id' => $user2->id]);
        }
        $jobs = Job::factory()->count(5)->create();
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/jobs');

        $response->assertOk();
        $response->assertJsonCount(5, 'jobs');
    }

    public function test_create_job()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];

        $user = User::factory()->create(['type' => 'NON_APPROVER']);
        $professor = Professor::factory()->create(['user_id' => $user->id]);
        $totalAvailableHours = $professor->total_available_hours;
        $totalHours = $this->faker->numberBetween(1, $totalAvailableHours);

        $data = [
            'employee_type' => 'professor',
            'employee_id' => $professor->id,
            'date' => $this->faker->date(),
            'total_hours' => $totalHours,
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/jobs', $data);

        $response->assertOk();
        $response->assertJsonFragment([
            'employee_type' => 'professor',
            'employee_id' => $professor->id,
            'date' => $data['date'],
            'total_hours' => $totalHours,
        ]);
    }

    public function test_show_job()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $user = User::factory()->create(['type' => 'NON_APPROVER']);
        $professor = Professor::factory()->create(['user_id' => $user->id]);
        $trader = Trader::factory()->create(['user_id' => $user->id]);
        $job = Job::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson(route('jobs.show', $job->id));

        $response->assertOk();
        $response->assertJson([
            'job' => $job->toArray()
        ]);
    }

    public function test_update_job()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $user1 = User::factory()->create(['type' => 'NON_APPROVER']);
        $user2 = User::factory()->create(['type' => 'NON_APPROVER']);
        $professor = Professor::factory()->create(['user_id' => $user1->id]);
        $trader = Trader::factory()->create(['user_id' => $user2->id]);
        $job = Job::factory()->create();

        $data = [
            'employee_type' => $job->employee_type,
            'employee_id' => $job->employee_id,
            'total_hours' => $this->faker->numberBetween(1, $job->total_available_hours),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/jobs/{$job->id}", $data);

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $job->id,
            'employee_id' => $job->employee_id,
            'total_hours' => $data['total_hours'],
        ]);
    }

    public function test_destroy_job()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $user1 = User::factory()->create(['type' => 'NON_APPROVER']);
        $user2 = User::factory()->create(['type' => 'NON_APPROVER']);
        $professor = Professor::factory()->create(['user_id' => $user1->id]);
        $trader = Trader::factory()->create(['user_id' => $user2->id]);
        $job = Job::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->delete('/api/jobs/' . $job->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Job deleted successfully'
            ]);

        $checkJob = Job::query()->find($job->id);
        $this->assertNull($checkJob);
    }
}
