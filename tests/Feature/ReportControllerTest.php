<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\Job;
use App\Models\Professor;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testEarningsReportUnauthorized()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/report/earnings');

        $response->assertStatus(401);
    }

    public function testEarningsReport()
    {
        $approver = User::factory(['type' => 'APPROVER'])->create();
        $nonApprover1 = User::factory(['type' => 'NON_APPROVER'])->create();
        $nonApprover2 = User::factory(['type' => 'NON_APPROVER'])->create();
        $professor = Professor::factory(['user_id' => $nonApprover1->id])->create();
        $trader = Trader::factory(['user_id' => $nonApprover2->id])->create();
        $jobForProfessor = Job::factory(['employee_type' => 'professor', 'employee_id' => $professor->id, 'date' => '2020-02-03'])->create();
        $jobForTrader = Job::factory(['employee_type' => 'trader', 'employee_id' => $trader->id, 'date' => '2020-02-04'])->create();
        Approval::factory(['user_id' => $approver->id, 'job_id' => $jobForProfessor->id, 'status' => 'APPROVED'])->create();
        Approval::factory(['user_id' => $approver->id, 'job_id' => $jobForTrader->id, 'status' => 'APPROVED'])->create();

        Sanctum::actingAs(
            $approver,
            ['*']
        );

        $response = $this->get('/api/report/earnings');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['year', 'month', 'total_earnings'],
        ]);

        $responseData = $response->json();
        $this->assertCount(1, $responseData);

        $expectedEarnings = ($jobForProfessor->total_hours * $professor->payroll_per_hour) + ($jobForTrader->total_hours * $trader->payroll_per_hour);
        $this->assertEquals($responseData[0]['total_earnings'], $expectedEarnings);
    }
}
