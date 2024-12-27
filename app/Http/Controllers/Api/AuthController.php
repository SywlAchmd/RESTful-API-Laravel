<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Register new user
     * @param App\Http\Requests\RegisterRequest $request
     * @return JSONResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
            ]);

            if ($user) {
                return ResponseHelper::success(message: 'User has been registered successfully!', data: $user, statusCode: 201);
            }
            return ResponseHelper::error(message: 'Unable to register user!', statusCode: 400);
        } catch (Exception $e) {
            Log::error('Unable to register user : ' . $e->getMessage() . ' - Line no. ' . $e->getLine());
            return ResponseHelper::error(message: 'Unable to register user!' . $e->getMessage(), statusCode: 500);
        }
    }

    /**
     * Login user
     * @param App\Http\Requests\LoginRequest $request
     */
    public function login(LoginRequest $request)
    {
        try {
            // if credentials are incorrect
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return ResponseHelper::error(message: 'Unable to login, wrong credentials!', statusCode: 400);
            };

            $user = Auth::user();

            // create API token
            $token = $user->createToken('API Token')->plainTextToken;

            $authUser = [
                'user' => $user,
                'token' => $token
            ];

            return ResponseHelper::success(message: 'You are login successfully!', data: $authUser, statusCode: 200);
        } catch (Exception $e) {
            Log::error('Unable to login : ' . $e->getMessage() . ' - Line no. ' . $e->getLine());
            return ResponseHelper::error(message: 'Unable to login!' . $e->getMessage(), statusCode: 500);
        }
    }

    /**
     * Auth user data / Profile data
     * @param NA
     * @return JSONResponse
     */
    public function userProfile()
    {
        try {
            $user = Auth::user();

            if ($user) {
                return ResponseHelper::success(message: 'User profile fetched successfully!', data: $user, statusCode: 200);
            }

            return ResponseHelper::error(message: 'Unable to fetch user data due to invalid token!', statusCode: 400);
        } catch (Exception $e) {
            Log::error('Unable to fetch user profile : ' . $e->getMessage() . ' - Line no. ' . $e->getLine());
            return ResponseHelper::error(message: 'Unable to fetch user profile!' . $e->getMessage(), statusCode: 500);
        }
    }

    /**
     * User Logout
     * @param NA
     * @return JSONResponse
     */
    public function userLogout()
    {
        try {
            $user = Auth::user();

            if ($user) {
                $user->currentAccessToken()->delete();
                return ResponseHelper::success(message: 'User logged out successfully!', statusCode: 200);
            }

            return ResponseHelper::error(message: 'Unable to logged out due to invalid token!', statusCode: 400);
        } catch (Exception $e) {
            Log::error('Unable to logout : ' . $e->getMessage() . ' - Line no. ' . $e->getLine());
            return ResponseHelper::error(message: 'Unable to logout!' . $e->getMessage(), statusCode: 500);
        }
    }
}
