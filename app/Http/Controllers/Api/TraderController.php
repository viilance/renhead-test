<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTraderRequest;
use App\Http\Requests\UpdateTraderRequest;
use App\Models\Trader;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TraderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $traders = Trader::all();
        $this->authorize('userCanAuth', Trader::class);
        return response()->json(['traders' => $traders]);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(CreateTraderRequest $request): JsonResponse
    {
        $this->authorize('userCanAuth', Trader::class);
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

            Trader::query()->create([
                'user_id' => $user->getAttribute('id'),
                'working_hours' => $validData['working_hours'],
                'payroll_per_hour' => $validData['payroll_per_hour'],
            ]);

            DB::commit();

            return response()->json(['message' => 'Trader created successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to create Trader',
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
        $trader = Trader::query()->findOrFail($id);

        $this->authorize('userCanAuth', $trader);
        return response()->json(['trader' => $trader]);
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateTraderRequest $request, string $id): JsonResponse
    {
        $trader = Trader::query()->with('user')->findOrFail($id);

        $this->authorize('userCanAuth', $trader);

        $validData = $request->validated();

        $trader->fill($validData);
        $trader->user->fill($validData);
        $trader->push();

        return response()->json(['message' => 'Trader updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $trader = Trader::query()->findOrFail($id);
            $this->authorize('userCanAuth', $trader);

            DB::beginTransaction();

            $user = $trader->user;

            if ($user) {
                $user->delete();
            }

            $trader->delete();

            DB::commit();

            return response()->json(['message' => 'Trader deleted successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete Trader',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
