<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => ['required', 'confirmed', Password::defaults()],
            'native_language' => 'nullable|string|max:50',
            'current_level' => 'nullable|string|max:10',
            'target_level' => 'nullable|string|max:10',
            'goal' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->assignRole('student');

        StudentProfile::create([
            'user_id' => $user->id,
                    'student_number' => $this->generateStudentNumber(),
            'native_language' => $validated['native_language'] ?? null,
            'current_level' => $validated['current_level'] ?? null,
            'target_level' => $validated['target_level'] ?? null,
            'goal' => $validated['goal'] ?? null,
            'enrollment_date' => now(),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'user' => $this->userPayload($user),
            'token' => $token,
        ], 'Registration successful', 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->error('Invalid credentials.', 401);
        }

        if (! $user->is_active) {
            return $this->error('Your account is deactivated. Contact support.', 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        $user->forceFill(['last_login_at' => now()])->save();

        return $this->success([
            'user' => $this->userPayload($user),
            'token' => $token,
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['studentProfile', 'teacherProfile']);

        return $this->success([
            'user' => $this->userPayload($user),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:30',
            'native_language' => 'nullable|string|max:50',
            'current_level' => 'nullable|string|max:10',
            'target_level' => 'nullable|string|max:10',
            'goal' => 'nullable|string|max:500',
            'preferred_language' => 'nullable|string|max:10',
        ]);

        $user->update([
            'first_name' => $validated['first_name'] ?? $user->first_name,
            'last_name' => $validated['last_name'] ?? $user->last_name,
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        if ($user->studentProfile) {
            $profile = $user->studentProfile;
            foreach (['native_language', 'current_level', 'target_level', 'goal', 'preferred_language'] as $field) {
                if (array_key_exists($field, $validated)) {
                    $profile->{$field} = $validated[$field];
                }
            }
            $profile->save();
        }

        return $this->success([
            'user' => $this->userPayload($user->load(['studentProfile', 'teacherProfile'])),
        ], 'Profil mis à jour.');
    }

    protected function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => trim($user->first_name . ' ' . $user->last_name),
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'role' => $user->roles->pluck('name')->first(),
            'roles' => $user->roles->pluck('name'),
            'is_active' => $user->is_active,
            'last_login_at' => $user->last_login_at,
            'student_profile' => $user->studentProfile,
            'teacher_profile' => $user->teacherProfile,
            'created_at' => $user->created_at,
        ];
    }

    protected function generateStudentNumber(): string
    {
        $year = now()->year;
        $latest = StudentProfile::where('student_number', 'like', "STU-$year-%")
            ->orderByDesc('student_number')
            ->value('student_number');

        $next = 1001;

        if ($latest) {
            $suffix = (int) substr($latest, strrpos($latest, '-') + 1);
            $next = max(1001, $suffix + 1);
        }

        return 'STU-' . $year . '-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
