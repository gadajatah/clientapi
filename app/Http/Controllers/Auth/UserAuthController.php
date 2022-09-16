<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Str;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'gagal validate!'
            ], 422);
        }

        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        // $user = User::create($request->toArray());
        $user = new User();
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->password = $request['password'];
        $user->save();

        $token = $user->createToken('Laravel Grant Client')->accessToken;
        return response()->json([
            'token' => $token
        ], 200);
    }

    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);
        
        if ($validated->fails()) {
            return response()->json([
                'error' => $validated->errors()->all(),
            ], 422);
        }
        $user = User::where('email', $request->email)->first();
        dd($user);
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                return response()->json([
                    'token' => $token,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'password salah!',
                ], 422);
            }
        } else {
            return response()->json([
                'message' => 'User tidak ada!'
            ], 422);
        }
    }
}
