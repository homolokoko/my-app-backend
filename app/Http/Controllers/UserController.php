<?php

namespace App\Http\Controllers;

use App\Models\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Features;

class UserController extends Controller
{
    public function retrieve()
    {
        try {
            $users = User::get()->map(fn($item) => ['value' => $item->id, 'text' => $item->name]);
            return response()->json($users, 200);
        } catch (Error $err) {
            return response()->json(['message' => $err->getMessage()], 503);
        }
    }

    public function signin(Request $request)
    {
        try {
            if (!auth()->attempt($request->only('email', 'password'))) {
                return response()->json(['status' => false, 'message' => 'Invalid credentials'], 401);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            return response()->json([
                'status' => true,
                'token' => $user->createToken('auth_token')->plainTextToken
            ], 200);
        } catch (Error $err) {
            return ['message' => $err->getMessage()];
        }
    }

    public function signup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => true,
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $user
            ], 201);
        } catch (Error $err) {
            return response()->json(['message' => $err->getMessage()]);
        }
    }

    public function emailVerification($email)
    {
        try {
            $user = User::where('email', $email);
            if ($user->exists())
                return response()->json($user->first(), 404);
            else return response()->json(['message' => 'Email found!'], 200);
        } catch (Error $err) {
            return response()->json(['message' => $err->getMessage()], 401);
        }
    }

    public function confirmedPassword($id, Request $request)
    {
        try {
            $user = User::find($id);
            $validator = Validator::make(
                $request->all(),
                ['password' => 'required|string|min:8|confirmed']
            );
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            } else if ($user) {
                $user->update(['password' => Hash::make($request->password)]);
                return response()->json(['token' => $user->remember_token], 200);
            }
        } catch (Error $err) {
            return response()->json(['message' => $err->getMessage()], 401);
        }
    }
}
