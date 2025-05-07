<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brides;
use App\Models\Grooms;
use App\Models\ListPhoto;
use Illuminate\Http\Request;

class ListPhotoController extends Controller
{
    public function getListPhoto()
    {
        try {
            $ListPhoto = ListPhoto::paginate(10);
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $ListPhoto], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getListPhotoByProjectId($project_id)
    {
        try {
            $bride = Brides::where('project_id', $project_id)->first();
            $groom = Grooms::where('project_id', $project_id)->first();

            $ListPhoto = ListPhoto::where('project_id', $project_id)->get();
            return response()->json([
                'message' => 'Fetch Data Successfully',
                'data' => $ListPhoto,
                'photo_bride' => $bride ? $bride->photo_bride : null,
                'photo_groom' => $groom ? $groom->photo_groom : null,
            ], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getListPhotoByType($project_id)
    {
        try {
            $ListPhotoBride = ListPhoto::where('project_id', $project_id)->where('type', 'bride')->get();
            $ListPhotoGroom = ListPhoto::where('project_id', $project_id)->where('type', 'groom')->get();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => ['bride' => $ListPhotoBride, 'groom' => $ListPhotoGroom]], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function getListPhotoById($id)
    {
        try {
            $ListPhoto = ListPhoto::where('id', $id)->first();
            return response()->json(['message' => 'Fetch Data Successfully', 'data' => $ListPhoto], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
    public function createListPhoto(Request $request)
    {
        try {
            //code...
            Request()->validate([
                'project_id' => 'required',
            ]);

            $ListPhoto = ListPhoto::create([
                'project_id' => $request->project_id,
                'name' => $request->name,
                'relationship' => $request->relationship,
                'type' => $request->type,
            ]);

            return response()->json(['message' => 'Create Data Successfully', 'data' => $ListPhoto], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function updateListPhoto(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([]);

            // Cari data bride berdasarkan ID
            $ListPhoto = ListPhoto::find($id);
            if (!$ListPhoto) {
                return response()->json(['message' => 'List Photo not found'], 404);
            }
            // Update data bride
            $ListPhoto->update([
                'name' => $request->name,
                'relationship' => $request->relationship,
                'type' => $request->type,
            ]);

            // Return response sukses
            return response()->json(['message' => 'Updated data successfully', 'data' => $ListPhoto], 200);
        } catch (\Throwable $th) {
            // Tangani error
            return response()->json(['message' => 'An error occurred', 'error' => $th->getMessage()], 500);
        }
    }

    public function deleteListPhoto($id)
    {
        try {
            ListPhoto::where('id', $id)->first()->delete();
            return response()->json(['message' => 'Delete Data Successfully'], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
