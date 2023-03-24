<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testCreateUser()
    {
        $userData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'type' => 'NON_APPROVER'
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/register', $userData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'token',
        ]);

        $user = User::query()->where('email', $userData['email'])->first();

        $this->assertNotNull($user);
        $this->assertEquals($userData['first_name'], $user->first_name);
        $this->assertEquals($userData['last_name'], $user->last_name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['type'], strtoupper($user->type));
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    public function testLoginUser()
    {
        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/auth/login', $credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'token',
        ]);
    }
}
