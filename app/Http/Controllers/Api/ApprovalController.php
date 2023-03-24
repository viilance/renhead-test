<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateApprovalRequest;
use App\Http\Requests\UpdateApprovalRequest;
use App\Models\Approval;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $approvals = Approval::all();
        return response()->json(['approvals' => $approvals]);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function approve($id): JsonResponse
    {
        $approval = Approval::query()->findOrFail($id);

        $this->authorize('userCanAuth', $approval);

        $approval->setAttribute('status', 'APPROVED');
        $approval->save();

        return response()->json(['message' => 'Job approved successfully']);
    }

    /**
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function disapprove($id): JsonResponse
    {
        $approval = Approval::query()->findOrFail($id);

        $this->authorize('userCanAuth', $approval);

        $approval->setAttribute('status', 'DISAPPROVED');
        $approval->save();

        return response()->json(['message' => 'Job disapproved successfully']);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(CreateApprovalRequest $request): JsonResponse
    {
        $this->authorize('userCanAuth', Approval::class);
        $validData = $request->validated();

        try {
            DB::beginTransaction();

            $approval = Approval::query()->create($validData);

            DB::commit();

            return response()->json(['message' => 'Approval created successfully', 'approval' => $approval]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to create Approval',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(string $id): JsonResponse
    {
        $approval = Approval::query()->findOrFail($id);

        $this->authorize('userCanAuth', $approval);
        return response()->json(['approval' => $approval]);
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateApprovalRequest $request, string $id): JsonResponse
    {
        $this->authorize('userCanAuth', Approval::class);
        $validData = $request->validated();

        $approval = Approval::query()->findOrFail($id);

        $approval->update($validData);

        return response()->json(['message' => 'Approval updated successfully', 'approval' => $approval]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $approval = Approval::query()->findOrFail($id);
            $this->authorize('userCanAuth', $approval);

            DB::beginTransaction();

            $approval->delete();

            DB::commit();

            return response()->json(['message' => 'Approval deleted successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete Approval',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
