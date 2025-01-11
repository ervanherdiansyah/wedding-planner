<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VipGuestLists;
use Illuminate\Http\Request;

class VipGuestListsController extends Controller
{
    public function getVipGuestLists()
    {
        try {
            $VipGuestLists = VipGuestLists::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $VipGuestLists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getVipGuestListsByProjectId($project_id)
    {
        try {
            $VipGuestLists = VipGuestLists::where('project_id', $project_id)->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $VipGuestLists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getVipGuestListsByType($project_id)
    {
        try {
            $VipGuestListsBride = VipGuestLists::where('project_id', $project_id)->where('type', 'bride')->get();
            $VipGuestListsGroom = VipGuestLists::where('project_id', $project_id)->where('type', 'groom')->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => ['bride' => $VipGuestListsBride, 'groom' => $VipGuestListsGroom]], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getVipGuestListsById($id)
    {
        try {
            $VipGuestLists = VipGuestLists::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $VipGuestLists], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createVipGuestLists(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
            ]);

            $VipGuestLists = VipGuestLists::create([
                'project_id' => $request->project_id,
                'role' => $request->role,
                'name' => $request->name,
                'contact' => $request->contact,
                'type' => $request->type,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $VipGuestLists], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateVipGuestLists(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $VipGuestLists = VipGuestLists::find($id);
            if (!$VipGuestLists) {
                return response()->json(['message' => 'Vip Guest List not found'], 404);
            }
            // Update data bride
            $VipGuestLists->update([
                'role' => $request->role,
                'name' => $request->name,
                'contact' => $request->contact,
                'type' => $request->type,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $VipGuestLists], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteVipGuestLists($id)
    {
        try {
            VipGuestLists::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
