<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function getPayments(Request $request)
    {
        try {
            $query = Payments::query();

            if (!empty($request->keyword)) {
                $query->where('name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('status', 'like', '%' . $request->keyword . '%')
                    ->orWhere('price', 'like', '%' . $request->keyword . '%')
                    ->orWhere('bank_type', 'like', '%' . $request->keyword . '%')
                    ->orWhere('payment_date', 'like', '%' . $request->keyword . '%');
            }

            // Menentukan sorting berdasarkan parameter
            $sortColumn = $request->sortColumn ?? 'id';
            $sortDirection = $request->sortDirection ?? 'desc';

            $limit = !empty($request->limit) ? (int)$request->limit : 10;

            // Menambahkan sorting pada query
            $Payments = $query->orderBy($sortColumn, $sortDirection)->paginate($limit);
            return response()->json(['message' => 'Get Data Payments Successfully!', 'data' => $Payments], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getPaymentsById($id)
    {
        try {
            $Payments = Payments::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Payments], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createPayments(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'user_id' => 'required',
                'status' => 'required',
                'price' => 'required',
                'bank_type' => 'required',
            ]);

            $Payments = Payments::create([
                'user_id' => $request->user_id,
                'status' => $request->status,
                'price' => $request->price,
                'bank_type' => $request->bank_type,
                'payment_date' => now(),
            ]);

            return response()->json(['message' => 'Payments Created Successfully!', 'data' => $Payments], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi dan kembalikan dalam format JSON
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            // Tangkap error lainnya
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updatePayments(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'user_id' => 'required',
                'status' => 'required',
                'price' => 'required',
                'bank_type' => 'required',
            ]);

            // Cari data bride berdasarkan ID
            $Payments = Payments::find($id);
            if (!$Payments) {
                return response()->json(['message' => 'Payments not found'], 404);
            }

            $Payments->update([
                'user_id' => $request->user_id,
                'status' => $request->status,
                'price' => $request->price,
                'bank_type' => $request->bank_type,
                'payment_date' => now(),
            ]);


            // Return response sukses
            return response()->json(['message' => 'Payments Updated successfully!', 'data' => $Payments], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi dan kembalikan dalam format JSON
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            // Tangkap error lainnya
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function deletePayments($id)
    {
        try {
            Payments::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Payments Deleted Successfully!'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateStatusPayments(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'status' => 'required',
            ]);

            // Cari data bride berdasarkan ID
            $Payments = Payments::find($id);
            if (!$Payments) {
                return response()->json(['message' => 'Payments not found'], 404);
            }

            $Payments->update([
                'status' => $request->status,
            ]);


            // Return response sukses
            return response()->json(['message' => 'Payments Status Updated successfully!', 'data' => $Payments], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap error validasi dan kembalikan dalam format JSON
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            // Tangkap error lainnya
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
