<?php

namespace App\Http\Controllers\Api\Budget;

use App\Http\Controllers\Controller;
use App\Models\Budgets;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function getBudgets()
    {
        try {
            $Budgets = Budgets::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Budgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getBudgetsByProjectId($project_id)
    {
        try {
            $Budgets = Budgets::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Budgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getBudgetsById($id)
    {
        try {
            $Budgets = Budgets::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $Budgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createBudgets(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
                'estimated_payment' => 'required',
                'actual_payment' => 'required',
            ]);

            $Budgets = Budgets::create([
                'project_id' => $request->project_id,
                'actual_payment' => $request->actual_payment,
                'estimated_payment' => $request->estimated_payment,
                'paid' => 0,
                'unpaid' => 0,
                'difference' => $request->estimated_payment -  $request->actual_payment,
                'balance' => 0,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $Budgets], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateBudgets(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'estimated_payment' => 'required',
                'actual_payment' => 'required',
            ]);

            // Cari data bride berdasarkan ID
            $Budgets = Budgets::find($id);
            if (!$Budgets) {
                return response()->json(['message' => 'Budget not found'], 404);
            }
            // Update data bride
            $Budgets->update([
                'actual_payment' => $request->actual_payment,
                'estimated_payment' => $request->estimated_payment,
                'difference' => $request->estimated_payment - $request->actual_payment,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $Budgets], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteBudgets($id)
    {
        try {
            Budgets::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
