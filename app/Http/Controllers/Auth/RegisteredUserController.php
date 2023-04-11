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
use Illuminate\Support\Facades\DB;



class RegisteredUserController extends Controller
{

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    #login user
    public function loginUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'     => ['required', 'numeric', 'digits:10'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }
        try {
            $user = User::where('phone', $request->phone)->first();
            if (!empty($user)) {
                $verified['phone_verified'] = now();
                $verified['active_device_id'] = 1;
                $token = JWTAuth::fromUser($user);
                return $this->getUserWithToken($token);

                if (!$token = JWTAuth::attempt($validator->validated())) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized',
                    ]);
                }
                return $this->getUserWithToken($token);
            } else {
                $user = User::create([
                    'phone'     => $request->phone,
                    'role_id' => 2,
                ]);
                $data['id'] = $user->id;
                $data['phone'] = $user->phone;
                $data['role_id'] = $user->role_id;
                $data['created_at'] = $user->created_at;
                $data['updated_at'] = $user->updated_at;

                $verified['phone_verified'] = now();
                $verified['active_device_id'] = 1;
                $token = JWTAuth::fromUser($user);
                return $this->getUserWithToken($token);
                if (!$token = JWTAuth::attempt($validator->validated())) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized',
                    ]);
                }

                return $this->getUserWithToken($token);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    #login user
    public function loginAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'     => ['required', 'numeric', 'digits:10'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()]);
        }
        try {
            $user = User::where('phone', $request->phone)->first();
            if (!empty($user)) {
                $verified['phone_verified'] = now();
                $verified['active_device_id'] = 1;
                $token = JWTAuth::fromUser($user);
                return $this->getUserWithToken($token);
                if (!$token = JWTAuth::attempt($validator->validated())) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized',
                    ]);
                }
                return $this->getUserWithToken($token);
            } else {
                $user = User::create([
                    'phone'     => $request->phone,
                    'role_id' => 1,
                ]);
                $data['id'] = $user->id;
                $data['phone'] = $user->phone;
                $data['role_id'] = $user->role_id;
                $data['created_at'] = $user->created_at;
                $data['updated_at'] = $user->updated_at;

                $verified['phone_verified'] = now();
                $verified['active_device_id'] = 1;
                $token = JWTAuth::fromUser($user);
                return $this->getUserWithToken($token);
                if (!$token = JWTAuth::attempt($validator->validated())) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized',
                    ]);
                }

                return $this->getUserWithToken($token);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    #logout
    public function logout(Request $request)
    {
        try {
            $authUser = auth()->user();
            $user = auth()->user()->id;
            if ($user) {
                auth('api')->logout();
                $phone = $authUser->phone;

                $data['active_device_id'] = 0;

                User::where('id', $user)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'Logged Out Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Login First',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }








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


    #User registration via mobile
    public function registerUserViaMobile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => ['required', 'numeric', 'digits:10', 'unique:users'],
            'otp'      => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }

        try {
            // $otp = 1234;
            $code['otp'] = $this->generateOTP();

            # Create User and store its Information
            $user = User::create([
                'phone'     => $request->phone,
                'otp'  => $code['otp'],
                'role_id' => 2,
            ]);
            $data['id'] = $user->id;
            $data['phone'] = $user->phone;
            $data['role_id'] = $user->role_id;
            $data['created_at'] = $user->created_at;
            $data['updated_at'] = $user->updated_at;

            return response()->json([
                'message' => 'OTP has been sent on your mobile no ' . $request->phone,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'     => ['required', 'numeric', 'digits:10'],
            'otp'       => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $user = User::where('phone', $request->phone)->first();
            if (!empty($user)) {
                if (!empty($user->otp)) {
                    if ($user->otp == $request->otp) {
                        $verified['otp'] = NULL;
                        $verified['phone_verified'] = now();
                        $verified['active_device_id'] = 1;
                        User::where('id', $user->id)->update($verified);
                        $token = JWTAuth::fromUser($user);
                        return $this->getUserWithToken($token);
                        if (!$token = JWTAuth::attempt($validator->validated())) {
                            return response()->json([
                                'error' => 'Unauthorized',
                            ]);
                        }
                        return $this->getUserWithToken($token);
                    } else {
                        return response()->json([
                            'message' => 'Please enter a valid OTP with 4 digit',
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => 'OTP get expired!. Please resend',
                    ]);
                }
            } else {
                return response()->json([
                    'message' => 'Invalid Phone Number',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }


    #resend otp
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'         => ['required', 'numeric', 'digits:10'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        try {
            $user = User::where('phone', $request->phone)->where('phone_verified', '!=', NULL)->first();

            # Get the User
            if (!empty($user)) {
                $code['otp'] = $this->generateOTP();
                User::where('id', $user->id)->update($code);
                return response()->json([
                    'message' => 'OTP has been resent on your mobile no ' . $request->phone,
                ]);
            } else {
                return response()->json([
                    'message' => 'Invalid Phone Number',
                ]);
            }
            # Return Resonse with Token
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getUserWithToken($token)
    {
        return [
            'success' => true,
            'message' => 'Login Successfully',
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => JWTAuth::factory()->getTTL() * 43200,
            // 'user' => $user->userformat(),
        ];
    }

    public function generateOTP()
    {
        return '1234';
    }



    #User registration via mobile
    public function registerAdminViaMobile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => ['required', 'numeric', 'digits:10', 'unique:users'],
            'otp'      => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }

        try {
            $code['otp'] = $this->generateOTP();

            # Create User and store its Information
            $user = User::create([
                'phone'     => $request->phone,
                'otp'  => $code['otp'],
                'role_id' => 1,
            ]);
            $data['id'] = $user->id;
            $data['phone'] = $user->phone;
            $data['role_id'] = $user->role_id;
            $data['created_at'] = $user->created_at;
            $data['updated_at'] = $user->updated_at;

            return response()->json([
                'message' => 'OTP has been sent on your mobile no ' . $request->phone,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
