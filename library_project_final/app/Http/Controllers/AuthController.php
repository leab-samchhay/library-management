<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        $user = User::where('email',$credentials['email'])->first();

        if(!$user || !Hash::check($credentials['password'],$user->password)){
            return response()->json([
                'message' => 'Invalid Email or password. Received Email: [' . $credentials['email'] . '], Pass: [' . $credentials['password'] . ']',
            ],401);
        }

        $token = $user->createToken(
            'minipos-browser',
            ['*'],
            now()->addHour(8),
        )->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    public function currentUser(Request $request){
        return response()->json(

            $request->user()->only(['id','name','email','profile']),

        );
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->noContent();
    }


}
