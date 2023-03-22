<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function earningsReport(): JsonResponse
    {
        $approvedJobIds = Approval::query()->select('job_id')
            ->groupBy('job_id')
            ->havingRaw('SUM(status = ?) = COUNT(*)', ['APPROVED'])
            ->pluck('job_id')
            ->toArray();

        $earnings = Job::query()
            ->whereIn('id', $approvedJobIds)
            ->select([
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(total_hours * CASE
                    WHEN employee_type = "professor" THEN (SELECT payroll_per_hour FROM professors WHERE id = employee_id)
                    ELSE (SELECT payroll_per_hour FROM traders WHERE id = employee_id)
                END) as total_earnings')
            ])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return response()->json($earnings);
    }
}
