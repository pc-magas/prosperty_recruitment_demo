<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

/**
 * This controller needs no tests it is rather straightforward.
 */
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->post('email');
        $password = $request->post('password');

        if(empty($email) || empty($password)){
            return response()->json([
                'message' => ['Invalid email or password'],
            ],400);
        }

        $user = User::where('email',  $email)->first();
        
        if (! $user || ! Hash::check($password, $user->password)) {
            return response()->json([
                'message' => ['Username or password incorrect'],
            ],404);
        }

      $user->tokens()->delete();

      return new JsonResponse([
          'status' => 'success',
          'message' => 'User logged in successfully',
          'name' => $user->name,
          'token' => $user->createToken('auth_token')->plainTextToken,
      ],201);
    }

    public function refreshToken(Request $request)
    {
        $user = $request->user();

        $newToken = $user->createToken('auth_token')->plainTextToken;

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Token Refreshed successfully',
            'name' => $user->name,
            'token' => $newToken,
        ],201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logout Success',
        ],200);
    }
}
