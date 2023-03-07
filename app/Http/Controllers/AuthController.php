<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['message' => 'Sorry but your credentials are wrong, please try again!'], 401);
    }
    
    public function me()
    {
        return response()->json($this->guard()->user());
    }
    
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }
    
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }
    
    public function guard()
    {
        return Auth::guard();
    }

    public function register (RegisterRequest $request)
    {
        User::create([
            'username' => $request->input('username'),
            'password' => bcrypt($request->input('password'))
        ]);

        return response()->json([
            'message' => 'You have been registered, now you can login!'
        ], 203);
    }
}