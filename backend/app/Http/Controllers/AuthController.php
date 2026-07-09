<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->authService->login($request->only('email', 'password'));

        if (!$result['success']) {
            return response()->json($result, 401);
        }

        return response()->json($result, 200);
    }

    public function logout(Request $request)
    {
        $result = $this->authService->logout($request->user());
        return response()->json($result, 200);
    }

    public function user(Request $request)
    {
        $user = $this->authService->getCurrentUser($request->user());
        return response()->json([
            'success' => true,
            'user' => $user,
        ], 200);
    }
}
