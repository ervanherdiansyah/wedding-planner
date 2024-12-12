<?php

namespace App\Http\Controllers\Api\Budget;

use App\Http\Controllers\Controller;
use App\Models\CategoryBudgets;
use Illuminate\Http\Request;

class CategoryBudgetController extends Controller
{
    public function getCategoryBudgets()
    {
        try {
            $CategoryBudgets = CategoryBudgets::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryBudgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getCategoryBudgetsByBudgetId($budget_id)
    {
        try {
            $CategoryBudgets = CategoryBudgets::where('budget_id', $budget_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryBudgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getCategoryBudgetsById($id)
    {
        try {
            $CategoryBudgets = CategoryBudgets::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryBudgets], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createCategoryBudgets(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'budget_id' => 'required',
                'title' => 'required',
            ]);

            $CategoryBudgets = CategoryBudgets::create([
                'budget_id' => $request->budget_id,
                'title' => $request->title,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $CategoryBudgets], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateCategoryBudgets(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'title' => 'required',
            ]);

            // Cari data bride berdasarkan ID
            $CategoryBudgets = CategoryBudgets::find($id);
            if (!$CategoryBudgets) {
                return response()->json(['message' => 'Category Budget not found'], 404);
            }
            // Update data bride
            $CategoryBudgets->update([
                'title' => $request->title,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $CategoryBudgets], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteCategoryBudgets($id)
    {
        try {
            CategoryBudgets::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
