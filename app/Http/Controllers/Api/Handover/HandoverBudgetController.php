<?php

namespace App\Http\Controllers\Api\Handover;

use App\Http\Controllers\Controller;
use App\Models\HandoverBudget;
use Illuminate\Http\Request;

class HandoverBudgetController extends Controller
{
    public function getHandoverBudget()
    {
        try {
            $HandoverBudget = HandoverBudget::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $HandoverBudget], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getHandoverBudgetByProjectId($project_id)
    {
        try {
            $HandoverBudget = HandoverBudget::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $HandoverBudget], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getHandoverBudgetById($id)
    {
        try {
            $HandoverBudget = HandoverBudget::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $HandoverBudget], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createHandoverBudget(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
            ]);

            $HandoverBudget = HandoverBudget::create([
                'project_id' => $request->project_id,
                'male_budget' => $request->male_budget,
                'female_budget' => $request->female_budget,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $HandoverBudget], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateHandoverBudget(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $HandoverBudget = HandoverBudget::find($id);
            if (!$HandoverBudget) {
                return response()->json(['message' => 'Budget not found'], 404);
            }
            // Update data bride
            $HandoverBudget->update([
                'male_budget' => $request->male_budget,
                'female_budget' => $request->female_budget,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $HandoverBudget], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteHandoverBudget($id)
    {
        try {
            HandoverBudget::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
