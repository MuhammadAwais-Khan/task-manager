<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Registration Request
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('leejam')->accessToken;

        return response()->json(['token' => $token], 201);
    }

    /**
     * Login Request
     *
     * @param  Request  $request
     * @return  \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $user = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($user)) {
            $token = auth()->user()->createToken('leejam')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised User'], 401);
        }
    }

    /**
     * Returns Authenticated User Record
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userRecord()
    {
        return response()->json(['user' => auth()->user()], 200);
    }


    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
