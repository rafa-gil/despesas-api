<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return response([
                'access_token' => $request->user()->createToken('expense')->plainTextToken,
            ], ResponseAlias::HTTP_OK);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successfully',
        ], ResponseAlias::HTTP_OK);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
        ]);

        $user = User::query()->create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response([
            'access_token' => $user->createToken('expense')->plainTextToken,
        ], ResponseAlias::HTTP_CREATED);
    }
}
