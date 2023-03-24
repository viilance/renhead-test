<?php

namespace Database\Seeders;

use App\Models\Approval;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApprovalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = Job::all();
        $approvers = User::query()->where('type', '=', 'APPROVER')->get();

        foreach ($jobs as $job) {
            $approver = $approvers->random();
            Approval::factory()->create([
                'user_id' => $approver->id,
                'job_id' => $job->id,
            ]);
        }
    }
}
