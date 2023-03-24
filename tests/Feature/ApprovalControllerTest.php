<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\Job;
use App\Models\Professor;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class ApprovalControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function getUserWithToken(): array
    {
        $user = User::factory()->create(['type' => 'APPROVER']);
        $token = $user->createToken('API TOKEN')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    private function createJobs($max)
    {
        for ($i = 0; $i < $max; $i++) {
            $user = User::factory()->create(['type' => 'NON_APPROVER']);
            Professor::factory()->create(['user_id' => $user->id]);
            $user2 = User::factory()->create(['type' => 'NON_APPROVER']);
            Trader::factory()->create(['user_id' => $user2->id]);
        }
        Job::factory()->count($max)->create();
    }

    private function createApprovals($jobs)
    {
        $approvers = User::query()->where('type', '=', 'APPROVER')->get();

        foreach ($jobs as $job) {
            $approver = $approvers->random();
            Approval::factory()->create([
                'user_id' => $approver->id,
                'job_id' => $job->id,
            ]);
        }
    }

    public function test_index_approval()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $this->createJobs(3);
        $jobs = Job::all();
        $this->createApprovals($jobs);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->get('/api/approvals');

        $response->assertOk();
        $response->assertJsonStructure([
            'approvals' => [
                '*' => [
                    'id',
                    'user_id',
                    'job_id',
                    'status'
                ]
            ]
        ]);
    }

    public function test_create_approval()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $this->createJobs(1);
        $job = Job::query()->first();
        $approver = User::factory()->create(['type' => 'APPROVER']);

        $apiData = [
            'user_id' => $approver->id,
            'job_id' => $job->id,
            'status' => 'APPROVED'
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/approvals', $apiData);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'approval' => [
                'id',
                'user_id',
                'job_id',
                'status'
            ]
        ]);
    }

    public function test_show_approval()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $this->createJobs(1);
        $jobs = Job::all();
        $this->createApprovals($jobs);
        $approval = Approval::query()->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/approvals/' . $approval->id);

        $response->assertOk();
        $response->assertJsonStructure([
            'approval' => [
                'id',
                'user_id',
                'job_id',
                'status',
            ]
        ]);
    }

    public function test_update_approval()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $this->createJobs(1);
        $jobs = Job::all();
        $this->createApprovals($jobs);
        $approval = Approval::query()->first();

        $apiData = ['status' => 'APPROVED'];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/approvals/' . $approval->id, $apiData);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'approval' => [
                'id',
                'user_id',
                'job_id',
                'status',
            ]
        ]);
        $this->assertEquals('APPROVED', $approval->fresh()->status);
    }

    public function test_delete_approval()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $this->createJobs(1);
        $jobs = Job::all();
        $this->createApprovals($jobs);
        $approval = Approval::query()->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->delete('/api/approvals/' . $approval->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Approval deleted successfully'
            ]);

        $checkApproval = Approval::query()->find($approval->id);
        $this->assertNull($checkApproval);
    }

    public function test_approval_approve()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $this->createJobs(1);
        $job = Job::query()->first();
        $approval = Approval::factory()->create([
            'user_id' => $data['user']['id'],
            'job_id' => $job->id,
            'status' => 'DISAPPROVED'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patch('/api/approvals/' . $approval->id . '/approve');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Job approved successfully'
        ]);
    }

    public function test_approval_disapprove()
    {
        $data = $this->getUserWithToken();
        $token = $data['token'];
        $this->createJobs(1);
        $job = Job::query()->first();
        $approval = Approval::factory()->create([
            'user_id' => $data['user']['id'],
            'job_id' => $job->id,
            'status' => 'APPROVED'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->patch('/api/approvals/' . $approval->id . '/disapprove');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Job disapproved successfully'
            ]);
    }
}
