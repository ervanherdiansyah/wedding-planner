<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Models\Brides;
use App\Models\Events;
use App\Models\FamilyMemberBrides;
use App\Models\FamilyMemberGrooms;
use App\Models\Grooms;
use App\Models\Payments;
use App\Models\Projects;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

            $event = Events::create([
                'project_id' => $project->id,
                'bridegroom_name' => $request->bridegroom_name,
                'event_name' => $request->event_name,
                'event_datetime' => $request->event_datetime,
                'address' => $request->address,
                'description' => $request->description,
            ]);

            $bride = Brides::create([
                'project_id' => $project->id,
                'name_bride' => $request->name_bride,
                'child_bride' => $request->child_bride,
                'father_name_bride' => $request->father_name_bride,
                'mother_name_bride' => $request->mother_name_bride,
            ]);

            $FamilyMemberBrides = FamilyMemberBrides::create([
                'bride_id' => $bride->id
            ]);
            $groom = Grooms::create([
                'project_id' => $project->id,
                'name_groom' => $request->name_groom,
                'child_groom' => $request->child_groom,
                'father_name_groom' => $request->father_name_groom,
                'mother_name_groom' => $request->mother_name_groom,
            ]);
            $FamilyMemberGrooms = FamilyMemberGrooms::create([
                'groom_id' => $groom->id
            ]);

            $payment = Payments::create([
                'user_id' => $user->id,
                'status' => $request->status,
                'price' => $request->price,
                'bank_type' => $request->bank_type,
                'payment_date' => now(),
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Success Register',
            ], 200);
        } catch (ValidationException $e) {
            // Menangkap error validasi
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $errors[] = [
                        'field' => $field,
                        'message' => $message,
                    ];
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        } catch (\Throwable $th) {
            // Menangkap kesalahan lain
            DB::rollback();
            return response()->json([
                'message' => 'Internal Server Error',
                'details' => $th->getMessage(),
            ], 500);
        }
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['message' => "Validation failed.", 'code' => "VALIDATION_ERROR", 'details' => 'Incorrect email or password. Please try again.'], 400);
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
        $user = auth()->user()->load('projects');
        return response()->json([
            'message' => 'Successfully get data user',
            'data' => $user
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
