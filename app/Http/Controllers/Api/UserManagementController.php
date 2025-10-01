<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function getUser(Request $request)
    {
        try {
            $query = User::query();

            if (!empty($request->keyword)) {
                $query->where('name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('email', 'like', '%' . $request->keyword . '%')
                    ->orWhere('role', 'like', '%' . $request->keyword . '%')
                    ->orWhere('phone_number', 'like', '%' . $request->keyword . '%')
                    ->orWhere('package', 'like', '%' . $request->keyword . '%');
            }

            // Menentukan sorting berdasarkan parameter
            $sortColumn = $request->sortColumn ?? 'id';
            $sortDirection = $request->sortDirection ?? 'desc';

            $limit = !empty($request->limit) ? (int)$request->limit : 10;

            // Menambahkan sorting pada query
            $User = $query->orderBy($sortColumn, $sortDirection)->paginate($limit);
            return response()->json(['message' => 'Get Data User Successfully!', 'data' => $User], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getUserById($id)
    {
        try {
            $User = User::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $User], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createUser(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'name' => 'required',
                'email' => 'required|unique:\App\Models\User,email',
                'password' => 'required|confirmed|min:8',
                'phone_number' => 'required',
                'role' => 'required',
                'package' => 'required',
            ]);

            $User = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'role' => $request->role,
                'package' => $request->package,
            ]);

            return response()->json(['message' => 'User Created Successfully!', 'data' => $User], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi dan kembalikan dalam format JSON
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            // Tangkap error lainnya
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'name' => 'required|unique:\App\Models\User,name,' . $request->id,
                'email' => 'required|unique:\App\Models\User,email,' . $request->id,
                'password' => 'nullable|confirmed|min:8',
                'phone_number' => 'required',
                'role' => 'required',
                'package' => 'required',
            ]);

            // Cari data bride berdasarkan ID
            $User = User::find($id);
            if (!$User) {
                return response()->json(['message' => 'User not found'], 404);
            }
            // Update data bride
            if ($request->Password) {
                $User->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone_number' => $request->phone_number,
                    'role' => $request->role,
                    'package' => $request->package,
                ]);
            } else {
                $User->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'role' => $request->role,
                    'package' => $request->package,
                ]);
            }


            // Return response sukses
            return response()->json(['message' => 'User Updated successfully!', 'data' => $User], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi dan kembalikan dalam format JSON
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            // Tangkap error lainnya
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            User::where('id', $id)->first()->delete();
            return response()->json(['message' => 'User Deleted Successfully!'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updatepassword(Request $request)
    {
        try {
            //code...
            $request->validate([
                'Password' => ['required', 'confirmed'],
            ]);
            $user =  User::where('id', Auth::user()->id)->first();
            $user->update([
                'Password' =>  Hash::make($request->password),
            ]);

            return response()->json(['message' => 'berhasil ubah password'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
