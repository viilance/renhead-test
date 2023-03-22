<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use App\Models\Professor;
use App\Models\Trader;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $jobs = Job::all();
        return response()->json(['jobs' => $jobs]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateJobRequest $request): JsonResponse
    {
        $validData = $request->validated();

        $employee = $this->getEmployee($validData['employee_type'], $validData['employee_id']);

        $availableHours = $employee->getAttribute('total_available_hours');

        $existingHours = Job::query()
            ->where('employee_type', $validData['employee_type'])
            ->where('employee_id', $validData['employee_id'])
            ->where('date', $validData['date'])
            ->get()
            ->sum('total_hours');

        if ($validData['total_hours'] + $existingHours > $availableHours) {
            return response()->json(['message' => 'The total hours for the job exceed the employee\'s available hours.'], 400);
        }

        $job = Job::query()->create($validData);

        return response()->json(['message' => 'Job created successfully', 'job' => $job]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $job = Job::query()->findOrFail($id);

        return response()->json(['job' => $job]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, string $id): JsonResponse
    {
        $job = Job::query()->findOrFail($id);
        $validData = $request->validated();
        $employeeType = $validData['employee_type'] ?? $job->getAttribute('employee_type');
        $employeeId = $validData['employee_id'] ?? $job->getAttribute('employee_id');

        $employee = $this->getEmployee(
            $employeeType,
            $employeeId
        );

        $availableHours = $employee->getAttribute('total_available_hours');

        $totalHours = $validData['total_hours'] ?? $job->getAttribute('total_hours');
        $date = $validData['date'] ?? $job->getAttribute('date');

        $existingHours = Job::query()
            ->where('employee_type', $employeeType)
            ->where('employee_id', $employeeId)
            ->where('date', $date)
            ->where('id', '!=', $id)
            ->get()
            ->sum('total_hours');

        if ($totalHours + $existingHours > $availableHours) {
            return response()->json(['message' => 'The updated total hours for the job, combined with existing jobs, exceed the employee\'s available hours.'], 400);
        }

        $job->update(array_merge($job->getAttributes(), $validData));

        return response()->json(['message' => 'Job updated successfully', 'job' => $job]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $job = Job::query()->findOrFail($id);

            DB::beginTransaction();

            $job->delete();

            DB::commit();

            return response()->json(['message' => 'Job deleted successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete job',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * @param string $employeeType
     * @param int $employeeId
     * @return Builder|Builder[]|Collection|Model|null
     */
    private function getEmployee(string $employeeType, int $employeeId): Model|Collection|Builder|array|null
    {
        if ($employeeType === 'professor') {
            return Professor::query()->findOrFail($employeeId);
        } else {
            return Trader::query()->findOrFail($employeeId);
        }
    }
}
