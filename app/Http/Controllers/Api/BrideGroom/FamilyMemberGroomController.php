<?php

namespace App\Http\Controllers\Api\BrideGroom;

use App\Http\Controllers\Controller;
use App\Models\FamilyMemberGrooms;
use Illuminate\Http\Request;

class FamilyMemberGroomController extends Controller
{
    public function getFamilyMemberGrooms()
    {
        try {
            $FamilyMemberGrooms = FamilyMemberGrooms::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $FamilyMemberGrooms], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getFamilyMemberGroomsByGroomId($groom_id)
    {
        try {
            $FamilyMemberGrooms = FamilyMemberGrooms::where('groom_id', $groom_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $FamilyMemberGrooms], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getFamilyMemberGroomsById($id)
    {
        try {
            $FamilyMemberGrooms = FamilyMemberGrooms::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $FamilyMemberGrooms], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createFamilyMemberGrooms(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'groom_id' => 'required',
            ]);

            $FamilyMemberGrooms = FamilyMemberGrooms::create([
                'groom_id' => $request->groom_id,
                'relationship_groom' => $request->relationship_groom,
                'name_family_groom' => $request->name_family_groom,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $FamilyMemberGrooms], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateFamilyMemberGrooms(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $FamilyMemberGrooms = FamilyMemberGrooms::find($id);
            if (!$FamilyMemberGrooms) {
                return response()->json(['message' => 'Family Member Groom not found'], 404);
            }
            // Update data bride
            $FamilyMemberGrooms->update([
                'relationship_groom' => $request->relationship_groom,
                'name_family_groom' => $request->name_family_groom,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $FamilyMemberGrooms], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteFamilyMemberGrooms($id)
    {
        try {
            FamilyMemberGrooms::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
