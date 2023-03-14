<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;



class RegisteredUserController extends Controller
{

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:8'],
            'role_id'  => ['required', 'numeric'],
            'added_by' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'added_by' => $request->added_by,
            ]);
            // event(new Registered($user));
            // Auth::login($user);
            return response()->json([
                'message' => 'Sub Admin Registered successfully',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    #login with mobile
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => ['required', 'string', 'email'],
            'password'  => ['required', 'string', 'min:8', 'max:8'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        if (!$token = $token = JWTAuth::attempt($validator->validated())) {
            return response()->json([
                'error' => 'Unauthorized',
            ]);
        }
        return $this->getUserWithToken($token);
    }

    #logout
    public function logout(Request $request)
    {
        try {
            $user = auth()->user()->id;
            if ($user) {
                auth('api')->logout();
                return response()->json([
                    'message' => 'Logged Out Successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Login First',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getUserWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            // 'user' => $user->userformat(),
        ];
    }
}
