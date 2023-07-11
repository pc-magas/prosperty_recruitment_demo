<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if(empty($request->email) || empty($request->password)){
            return response()->json([
                'message' => ['Invalid email or password'],
            ],400);
        }

        $user = User::where('email',  $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => ['Username or password incorrect'],
            ],404);
        }

      $user->tokens()->delete();

      return response()->json([
          'status' => 'success',
          'message' => 'User logged in successfully',
          'name' => $user->name,
          'token' => $user->createToken('auth_token')->plainTextToken,
      ]);
    }
}
