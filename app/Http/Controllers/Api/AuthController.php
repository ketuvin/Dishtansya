<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRegisterRequest;

class AuthController extends Controller
{
    const SUCCESS_STATUS = 201;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;
    const INTERNAL_SERVER_STATUS = 500;

    // Create User
    public function register(UserRegisterRequest $request) {
        $validated = $request->validated(); //validation
        
        $input = $request->input();

        $input['email'] = $request->request->get('email');
        $input['password'] = $request->request->get('password');

        DB::beginTransaction();

        try {
            $input['password'] = bcrypt($input['password']);
            
            $user = User::create([
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            DB::commit();
    
            return response()->json([
                'success'=> true,
                'message'=> 'User successfully registered'
            ], self::SUCCESS_STATUS); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'=> false,
                'message'=> 'User registration failed'
            ], self::INTERNAL_SERVER_STATUS); 
        }
    }
}
