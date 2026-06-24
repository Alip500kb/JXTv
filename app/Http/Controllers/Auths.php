<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth ;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Auths extends Controller
{
    public function login(Request $request) {
        $valid = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        } elseif (!Auth::attempt($request->all())) {
            return response()->json([
                'status' => 'gagal',
                'message' => 'email atau password anda salah'
            ],401);
        }

        $user = User::where('email', $request->email)->first;
        $user->tokens->delete();
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'token' => $user->createToken('login')->plainTextToken
        ]);
    }

    public function signup(Request $request) {
        $valid = Validator::make($request->all(), [
            'email' => 'required|unique:users,email',
            'name' => 'required|unique:users,name',
            'password' => 'required|min:6'
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        }

        $users = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // $user = User::where('email', $request->email)->first();
        // dd($users);
        
        return response()->json([
            'token' => $users->createToken('signup')->plainTextToken
        ]);

    }
}
