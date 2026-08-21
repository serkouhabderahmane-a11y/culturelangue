<?php

namespace Tests\Feature;

use App\Models\StudentProfile;
use App\Models\User;

class AuthTest extends BaseApiTestCase
{
    public function test_user_can_register_as_student(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'native_language' => 'en',
            'current_level' => 'A1',
            'target_level' => 'B1',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', 'student')
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
        $this->assertDatabaseHas('student_profiles', ['user_id' => $response->json('data.user.id')]);

        $profile = StudentProfile::where('user_id', $response->json('data.user.id'))->first();
        $this->assertNotNull($profile->student_number);
        $this->assertStringStartsWith('STU-', $profile->student_number);
    }

    public function test_register_requires_unique_email(): void
    {
        $user = $this->createUser('student');

        $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => $user->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(422);
    }

    public function test_user_can_login(): void
    {
        $user = $this->createUser('student');

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'nobody@test.ca',
            'password' => 'wrongpassword',
        ])->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_login_is_blocked_for_deactivated_user(): void
    {
        $user = $this->createUser('student');
        $user->update(['is_active' => false]);

        $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(403);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = $this->createUser('admin');
        $token = $this->apiLogin($user);

        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertStatus(200)
            ->assertJsonPath('data.user.email', $user->email);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/me')->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        $user = $this->createUser('student');
        $token = $this->apiLogin($user);

        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
