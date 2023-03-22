<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProfessorRequest;
use App\Http\Requests\UpdateProfessorRequest;
use App\Models\Professor;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfessorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $professors = Professor::all();
        $this->authorize('userCanAuth', Professor::class);
        return response()->json(['professors' => $professors]);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(CreateProfessorRequest $request): JsonResponse
    {
        $this->authorize('userCanAuth', Professor::class);
        $validData = $request->validated();

        try {
            DB::beginTransaction();

            $user = User::query()->create([
                'first_name' => $validData['first_name'],
                'last_name' => $validData['last_name'],
                'email' => $validData['email'],
                'type' => 'NON_APPROVER',
                'password' => Hash::make($validData['password'])
            ]);

            Professor::query()->create([
                'user_id' => $user->getAttribute('id'),
                'total_available_hours' => $validData['total_available_hours'],
                'payroll_per_hour' => $validData['payroll_per_hour'],
                'total_projects' => $validData['total_projects'] ?? 0,
                'office_number' => $validData['office_number'] ?? 0,
            ]);

            DB::commit();

            return response()->json(['message' => 'Professor created successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to create Professor',
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
        $professor = Professor::query()->findOrFail($id);

        $this->authorize('userCanAuth', $professor);
        return response()->json(['professor' => $professor]);
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateProfessorRequest $request, string $id): JsonResponse
    {
        $professor = Professor::query()->with('user')->findOrFail($id);

        $this->authorize('userCanAuth', $professor);

        $validData = $request->validated();

        $professor->fill($validData);
        $professor->user->fill($validData);
        $professor->push();

        return response()->json(['message' => 'Professor updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $professor = Professor::query()->findOrFail($id);
            $this->authorize('userCanAuth', $professor);

            DB::beginTransaction();

            $user = $professor->user;

            if ($user) {
                $user->delete();
            }

            $professor->delete();

            DB::commit();

            return response()->json(['message' => 'Professor deleted successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete Professor',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
