<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *    path="/api/login",
     *    tags={"Auth"},
     *    summary="Login",
     *    description="Login",
     *    operationId="login",
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *          required={"email", "password"},
     *          @OA\Property(property="email", type="string", format="email", example="mail@mail.com"),
     *          @OA\Property(property="password", type="string", format="password", example="password")
     *       )
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="Successful operation",
     *       @OA\JsonContent(
     *          @OA\Property(property="access_token", type="string", example="your-access-token-here"),
     *       )
     *    ),
     *    @OA\Response(
     *       response=401,
     *       description="Invalid credentials"
     *    )
     * )
    */

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

    /**
     * @OA\Post(
     *    path="/api/logout",
     *    security={{"bearerAuth": {}}},
     *    tags={"Auth"},
     *    summary="Logout",
     *    description="Logout",
     *    operationId="logout",
     *    @OA\Response(
     *       response=200,
     *       description="Successful operation",
     *       @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Logout successfully"),
     *       )
     *    )
     * )
    */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successfully',
        ], ResponseAlias::HTTP_OK);
    }

    /**
         * @OA\Post(
         *    path="/api/register",
         *    tags={"Auth"},
         *    summary="Register",
         *    description="Register",
         *    operationId="register",
         *    @OA\RequestBody(
         *       required=true,
         *       @OA\JsonContent(
         *          required={"name", "email", "password"},
         *          @OA\Property(property="name", type="string", example="John Doe"),
         *          @OA\Property(property="email", type="string", format="email", example="mail@mail.com"),
         *          @OA\Property(property="password", type="string", format="password", example="password")
         *       )
         *    ),
         *    @OA\Response(
         *       response=201,
         *       description="Successful registration",
         *       @OA\JsonContent(
         *          @OA\Property(property="access_token", type="string", example="your-access-token-here"),
         *       )
         *    ),
         *    @OA\Response(
         *       response=400,
         *       description="Bad Request"
         *    )
         * )
     */

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
