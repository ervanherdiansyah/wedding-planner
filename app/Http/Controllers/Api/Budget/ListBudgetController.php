<?php

namespace App\Http\Controllers\Api\Budget;

use App\Http\Controllers\Controller;
use App\Models\ListBudgets;
use Illuminate\Http\Request;

class ListBudgetController extends Controller
{
    public function getListBudgets()
    {
        try {
            $ListBudgets = ListBudgets::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $ListBudgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getListBudgetsByCategoryBudgetId($category_budget_id)
    {
        try {
            $ListBudgets = ListBudgets::where('category_budget_id', $category_budget_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $ListBudgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getListBudgetsById($id)
    {
        try {
            $ListBudgets = ListBudgets::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $ListBudgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createListBudgets(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'category_budget_id' => 'required',
                'estimated_payment' => 'required',
                'actual_payment' => 'required',
                'paid' => 'required',
                'deadline' => 'required',
                'status_payment' => 'required',
                'first_payment' => 'required',
                'deadline_first_payment' => 'required',
                'status_first_payment' => 'required',
                'second_payment' => 'required',
                'deadline_second_payment' => 'required',
                'status_second_payment' => 'required',
            ]);

            $ListBudgets = ListBudgets::create([
                'category_budget_id' => $request->category_budget_id,
                'actual_payment' => $request->actual_payment,
                'estimated_payment' => $request->estimated_payment,
                'difference' => $request->estimated_payment - $request->actual_payment,
                'paid' => $request->paid,
                'remaining_payment' => $request->estimated_payment - $request->paid - $request->first_payment - $request->second_payment,
                'deadline' => $request->deadline,
                'status_payment' => $request->status_payment,
                'first_payment' => $request->first_payment,
                'deadline_first_payment' => $request->deadline_first_payment,
                'status_first_payment' => $request->status_first_payment,
                'second_payment' => $request->second_payment,
                'deadline_second_payment' => $request->deadline_second_payment,
                'status_second_payment' => $request->status_second_payment,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $ListBudgets], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateListBudgets(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'estimated_payment' => 'required',
                'actual_payment' => 'required',
                'paid' => 'required',
                'remaining_payment' => 'required',
                'deadline' => 'required',
                'status_payment' => 'required',
                'first_payment' => 'required',
                'deadline_first_payment' => 'required',
                'status_first_payment' => 'required',
                'second_payment' => 'required',
                'deadline_second_payment' => 'required',
                'status_second_payment' => 'required',
            ]);

            // Cari data bride berdasarkan ID
            $ListBudgets = ListBudgets::find($id);
            if (!$ListBudgets) {
                return response()->json(['message' => 'List Budget not found'], 404);
            }
            // Update data bride
            $ListBudgets->update([
                'category_budget_id' => $request->category_budget_id,
                'actual_payment' => $request->actual_payment,
                'estimated_payment' => $request->estimated_payment,
                'difference' => $request->estimated_payment - $request->actual_payment,
                'paid' => $request->paid,
                'remaining_payment' => $request->remaining_payment,
                'deadline' => $request->deadline,
                'status_payment' => $request->status_payment,
                'first_payment' => $request->first_payment,
                'deadline_first_payment' => $request->deadline_first_payment,
                'status_first_payment' => $request->status_first_payment,
                'second_payment' => $request->second_payment,
                'deadline_second_payment' => $request->deadline_second_payment,
                'status_second_payment' => $request->status_second_payment,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $ListBudgets], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteListBudgets($id)
    {
        try {
            ListBudgets::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
