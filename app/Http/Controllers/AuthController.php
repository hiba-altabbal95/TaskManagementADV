<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest as AuthLoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{   
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(AuthLoginRequest $request)
    {
       
           
            $validatedUser = $this->authService->Login($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'User loggedin successfully',
                'user Information' => $validatedUser,
            ], 201);
            

    }   

    public function logout()
    {
        $this->authService->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}