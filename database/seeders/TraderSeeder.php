<?php

namespace Database\Seeders;

use App\Models\Trader;
use App\Models\User;
use Illuminate\Database\Seeder;

class TraderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $user = User::factory()->state(['type' => 'NON_APPROVER'])->create();
            Trader::factory()->create(['user_id' => $user->id]);
        }
    }
}
