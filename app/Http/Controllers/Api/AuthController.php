<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new mobile user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 201);
    }

    /**
     * Mobile Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized / Invalid credentials',
            ], 401);
        }

        $user = Auth::guard('api')->user();

        if ($user->is_admin) {
            Auth::guard('api')->logout();
            return response()->json([
                'message' => 'Admin accounts cannot be used on the mobile application.',
            ], 403);
        }

        return response()->json([
            'message' => 'Success!',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Mobile Logout
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get Current User Profile
     */
    public function user()
    {
        return response()->json([
            'user' => Auth::guard('api')->user(),
        ]);
    }
}
