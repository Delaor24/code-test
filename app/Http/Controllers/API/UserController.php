<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller {
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegistrationRequest $request) {

        try {
            $requestAll = $request->all();
            $requestAll['password'] = bcrypt($requestAll['password']);
            $user = User::create($requestAll);
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['user'] = $user;

            return response()->json(
                [
                    'success' => true,
                    'message' => "Data Saved!",
                    'data' => $success,
                ], 201
            );
        } catch (\Exception $ex) {
            $message = env('APP_ENV') !== 'production' ? $ex->getMessage() : "";
            $code = $ex->getCode();

            return response()->json(
                [
                    'success' => false,
                    'message' => $message,
                ], $code
            );
        }

    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request) {

        $checkUser = User::where("email", $request->email)->first();

        if (!$checkUser) {
            return response()->json(
                [
                    'success' => false,
                    'message' => "You are not registered user!",
                ], 404
            );
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['user'] = $user;

            return response()->json(
                [
                    'success' => true,
                    'message' => "Login Successfully!",
                    'data' => $success,
                ], 200
            );

        } else {

            return response()->json(
                [
                    'success' => false,
                    'message' => "Unauthorised!",
                ],
                403
            );
        }
    }

    /**
     * logout user
     */

    public function logout() {
        auth()->user()->tokens()->delete();

        return response()->json(
            [
                'success' => true,
                'message' => "User Logout",
            ], 200
        );
    }
}
