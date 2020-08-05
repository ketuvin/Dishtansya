<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;

// Swagger Annotation for Auth-Service
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="DISHTANSYA Api",
 *      description="This is the DISHTANSYA API documentation.",
 * )
 * @OA\Server(
 *      url="http://api.dishtansya.com/v1",
 *      description="Local Server"
 * )
 * @OA\Tag(
 *     name="auth-service",
 *     description="Auth Controller",
 * )
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer"
 * )
 * @OA\Post(
 *      path="/register",
 *      operationId="registerUser",
 *      tags={"auth-service"},
 *      summary="Register user into the system",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     format="email"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Success",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized",
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Unprocessable Entity",
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal Server",
 *      ),
 * )
 * @OA\Post(
 *      path="/login",
 *      operationId="loginUser",
 *      tags={"auth-service"},
 *      summary="Logs user into the system",
 *      description="User must be authorized to login",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="email",
 *                     type="string",
 *                     format="email"
 *                 ),
 *                 @OA\Property(
 *                     property="password",
 *                     type="string"
 *                 )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Success",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized",
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Unprocessable Entity",
 *      ),
 *      @OA\Response(
 *          response=429,
 *          description="Too Many Requests",
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal Server",
 *      ),
 * )
 * @OA\Post(
 *      path="/logout",
 *      operationId="logoutUser",
 *      tags={"auth-service"},
 *      summary="Logs out current user",
 *      description="Logs out current logged in user session",
 *      @OA\Response(
 *          response=201,
 *          description="Success",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized",
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal Server",
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
 * )
 */
class AuthController extends Controller
{
    use ThrottlesLogins;

    public $maxAttempts = 5;
    public $decayMinutes = 5;

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

    // Login User
    public function login(UserLoginRequest $request) {
        $validated = $request->validated(); //validation
        
        $input = $request->input();

        $input['email'] = $request->request->get('email');
        $input['password'] = $request->request->get('password');

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
        
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            $this->clearLoginAttempts($request);
            $user = Auth::user();
            $token = 'Bearer ' . $user->createToken('DISHTANSYA')->accessToken; 
            return response()->json(['access_token' => $token], self::SUCCESS_STATUS); 
        } else {
            $this->incrementLoginAttempts($request);
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], self::UNAUTHORIZED_STATUS);
        }
    }

    //Logout User
    public function logout() {
        $accessToken = Auth::user()->token();
        $accessToken->revoke();
    
        return response()->json([
            'success' => true,
            'message' => 'Successfully logout'
        ], self::SUCCESS_STATUS);
    }

    public function username() {
        return 'email';
    }
}
