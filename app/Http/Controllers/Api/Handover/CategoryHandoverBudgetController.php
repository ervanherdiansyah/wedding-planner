<?php

namespace App\Http\Controllers\Api\Handover;

use App\Http\Controllers\Controller;
use App\Models\CategoryHandover;
use Illuminate\Http\Request;

class CategoryHandoverBudgetController extends Controller
{
    public function getCategoryHandover()
    {
        try {
            $CategoryHandover = CategoryHandover::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryHandover], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getCategoryHandoverByHandoverBudgetId($handover_budgets_id)
    {
        try {
            $CategoryHandover = CategoryHandover::where('handover_budgets_id', $handover_budgets_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryHandover], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getCategoryHandoverById($id)
    {
        try {
            $CategoryHandover = CategoryHandover::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $CategoryHandover], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createCategoryHandover(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'handover_budgets_id' => 'required',
            ]);

            $CategoryHandover = CategoryHandover::create([
                'handover_budgets_id' => $request->handover_budgets_id,
                'title' => $request->title,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $CategoryHandover], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateCategoryHandover(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $CategoryHandover = CategoryHandover::find($id);
            if (!$CategoryHandover) {
                return response()->json(['message' => 'Uniform Category not found'], 404);
            }
            // Update data bride
            $CategoryHandover->update([
                'title' => $request->title,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $CategoryHandover], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteCategoryHandover($id)
    {
        try {
            CategoryHandover::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
