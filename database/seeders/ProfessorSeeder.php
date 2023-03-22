<?php

namespace Database\Seeders;

use App\Models\Professor;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfessorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $user = User::factory()->state(['type' => 'NON_APPROVER'])->create();
            Professor::factory()->create(['user_id' => $user->id]);
        }
    }
}
