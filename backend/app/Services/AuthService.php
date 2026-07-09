<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'user' => $user->load('role'),
            'token' => $token,
        ];
    }

    public function logout(User $user)
    {
        $user->tokens()->delete();

        return [
            'success' => true,
            'message' => 'Logged out successfully',
        ];
    }

    public function getCurrentUser(User $user)
    {
        return $user->load('role');
    }
}
