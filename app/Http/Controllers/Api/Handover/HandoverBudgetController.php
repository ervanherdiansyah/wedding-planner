<?php

namespace App\Http\Controllers\Api\Handover;

use App\Http\Controllers\Controller;
use App\Models\Brides;
use App\Models\Grooms;
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
            $bride = Brides::where('project_id', $project_id)->first();
            $groom = Grooms::where('project_id', $project_id)->first();
            $HandoverBudget = HandoverBudget::where('project_id', $project_id)->first();

            if (!$HandoverBudget) {
                return response()->json(['message' => 'Handover budget not found'], 404);
            }

            $diferent_male = $HandoverBudget->male_budget - $HandoverBudget->used_budget_male;
            $diferent_female = $HandoverBudget->female_budget - $HandoverBudget->used_budget_female;
            $total_budget = $HandoverBudget->male_budget + $HandoverBudget->female_budget;

            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => [
                    'id' => $HandoverBudget->id,
                    'project_id' => $HandoverBudget->project_id,
                    'total_budget' => $total_budget,
                    'male_budget' => $HandoverBudget->male_budget,
                    'female_budget' => $HandoverBudget->female_budget,
                    'used_budget_male' => $HandoverBudget->used_budget_male,
                    'used_budget_female' => $HandoverBudget->used_budget_female,
                    'diferent_male' => $diferent_male,
                    'diferent_female' => $diferent_female,
                    'bride' => $bride ? $bride->name_bride : null,
                    'groom' => $groom ? $groom->name_groom : null,
                ]
            ], 200);
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
