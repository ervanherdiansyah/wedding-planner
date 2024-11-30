<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            //code...
            request()->validate([
                'name' => 'required',
                'password' => 'required|min:8|max:32',
                'email' => 'required|unique:users',
                'phone_number' => 'required',
                'package' => 'required',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone_number' => $request->phone_number,
                'package' => $request->package,
                'role' => "user",
            ]);

            if ($request->package == 1 || $request->package == 2) {
                $project = Projects::create([
                    'name' => $request->name,
                    'invited_project' => 1,
                    'user_id' => $user->id,
                ]);
            } elseif ($request->package == 3) {
                $project = Projects::create([
                    'name' => $request->name,
                    'invited_project' => 4,
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Success Register',
                'data' => ['user' => $user, 'project' => $project]
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            // return response()->json(['message' => 'Internal Server Error'], 500);
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message' => 'Successfully Login',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json([
            'message' => 'Successfully get data user',
            'data' => auth()->user()
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json([
            'message' => 'Successfully refresh token',
            'data' => [
                'access_token' => auth()->refresh(),
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
