<?php

namespace App\Http\Controllers\Api\BrideGroom;

use App\Http\Controllers\Controller;
use App\Models\FamilyMemberBrides;
use Illuminate\Http\Request;

class FamilyMemberBrideController extends Controller
{
    public function getFamilyMemberBrides()
    {
        try {
            $FamilyMemberBrides = FamilyMemberBrides::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $FamilyMemberBrides], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getFamilyMemberBridesByBrideId($bride_id)
    {
        try {
            $FamilyMemberBrides = FamilyMemberBrides::where('bride_id', $bride_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $FamilyMemberBrides], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getFamilyMemberBridesById($id)
    {
        try {
            $FamilyMemberBrides = FamilyMemberBrides::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $FamilyMemberBrides], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createFamilyMemberBrides(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'bride_id' => 'required',
            ]);

            $FamilyMemberBrides = FamilyMemberBrides::create([
                'bride_id' => $request->bride_id,
                'relationship' => $request->relationship,
                'name_family' => $request->name_family,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $FamilyMemberBrides], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateFamilyMemberBrides(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'relationship' => 'required|string|max:255',
                'name_family' => 'required|string|max:255',
            ]);

            // Cari data bride berdasarkan ID
            $FamilyMemberBrides = FamilyMemberBrides::find($id);
            if (!$FamilyMemberBrides) {
                return response()->json(['message' => 'Bride not found'], 404);
            }
            // Update data bride
            $FamilyMemberBrides->update([
                'relationship' => $request->relationship,
                'name_family' => $request->name_family,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Bride updated successfully', 'data' => $FamilyMemberBrides], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteFamilyMemberBrides($id)
    {
        try {
            FamilyMemberBrides::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
